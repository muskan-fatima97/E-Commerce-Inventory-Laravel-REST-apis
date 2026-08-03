<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentLog;
use Illuminate\Support\Str;
use RuntimeException;

class JazzCashService
{
    protected string $merchantId;
    protected string $password;
    protected string $integritySalt;
    protected string $endpoint;
    protected string $returnUrl;
    protected int $expiryMinutes;

    public function __construct()
    {
        $this->merchantId    = (string) config('jazzcash.merchant_id');
        $this->password      = (string) config('jazzcash.password');
        $this->integritySalt = (string) config('jazzcash.integrity_salt');
        $this->returnUrl     = (string) (config('jazzcash.return_url') ?: url('/jazzcash/return'));
        $this->expiryMinutes = (int) config('jazzcash.txn_expiry_minutes', 60);

        $this->endpoint = config('jazzcash.environment') === 'production'
            ? config('jazzcash.production_url')
            : config('jazzcash.sandbox_url');
    }

    /**
     * Build the form fields to redirect the customer to JazzCash.
     */
    public function buildPaymentRequest(Order $order): array
    {
        $this->ensureCredentialsAreConfigured();

        $txnRefNo = 'T' . now()->format('YmdHis') . Str::upper(Str::random(4));
        $txnDateTime = now()->format('YmdHis');
        $txnExpiry = now()->addMinutes($this->expiryMinutes)->format('YmdHis');

        // JazzCash expects amount in the lowest currency unit (paisa) — no decimals
        $amountInPaisa = (int) round($order->total_amount * 100);

        $fields = [
            'pp_Version'          => '1.1',
            'pp_TxnType'          => 'MWALLET',
            'pp_Language'         => 'EN',
            'pp_MerchantID'       => $this->merchantId,
            'pp_SubMerchantID'    => '',
            'pp_Password'         => $this->password,
            'pp_BankID'           => '',
            'pp_ProductID'        => '',
            'pp_TxnRefNo'         => $txnRefNo,
            'pp_Amount'           => $amountInPaisa,
            'pp_TxnCurrency'      => 'PKR',
            'pp_TxnDateTime'      => $txnDateTime,
            'pp_BillReference'    => (string) $order->id,
            'pp_Description'      => 'Order #' . $order->id . ' - Closyyy',
            'pp_TxnExpiryDateTime' => $txnExpiry,
            'pp_ReturnURL'        => $this->returnUrl,
            'ppmpf_1' => '',
            'ppmpf_2' => '',
            'ppmpf_3' => '',
            'ppmpf_4' => '',
            'ppmpf_5' => '',
        ];

        $fields['pp_SecureHash'] = $this->generateHash($fields);

        // Save the reference + expiry on the order so we can match the return callback
        $order->update([
            'txn_ref_no'          => $txnRefNo,
            'payment_expires_at'  => now()->addMinutes($this->expiryMinutes),
        ]);

        PaymentLog::create([
            'order_id' => $order->id,
            'type'     => 'request',
            'payload'  => $fields,
        ]);

        return [
            'action' => $this->endpoint,
            'fields' => $fields,
        ];
    }

    /**
     * Generate JazzCash's HMAC-SHA256 secure hash.
     * Formula: sort fields by key, join as key=value&key=value..., prefix with integrity salt, HMAC-SHA256.
     */
    protected function generateHash(array $fields): string
    {
        $filtered = array_filter($fields, fn ($v) => $v !== '' && $v !== null);
        ksort($filtered);

        $stringToHash = $this->integritySalt . '&' . implode('&', $filtered);

        return hash_hmac('sha256', $stringToHash, $this->integritySalt);
    }

    /**
     * Verify the hash JazzCash sent back on return, and process the result.
     */
    public function handleReturn(array $data): array
    {
        try {
            $this->ensureCredentialsAreConfigured();
        } catch (RuntimeException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        PaymentLog::create([
            'order_id'   => Order::where('txn_ref_no', $data['pp_TxnRefNo'] ?? null)->value('id'),
            'type'       => 'response',
            'payload'    => $data,
            'hash_valid' => $this->verifyHash($data),
        ]);

        if (! $this->verifyHash($data)) {
            return ['success' => false, 'message' => 'Invalid response signature. Possible tampering.'];
        }

        $order = Order::where('txn_ref_no', $data['pp_TxnRefNo'] ?? null)->first();

        if (! $order) {
            return ['success' => false, 'message' => 'Order not found for this transaction.'];
        }

        // Idempotency: if we've already processed this order as paid, don't reprocess
        if ($order->payment_status === 'paid') {
            return ['success' => true, 'order_id' => $order->id, 'already_processed' => true];
        }

        $responseCode = $data['pp_ResponseCode'] ?? null;

        if ($responseCode !== '000') {
            $order->update(['payment_status' => 'failed']);
            return ['success' => false, 'message' => $data['pp_ResponseMessage'] ?? 'Payment failed.', 'order_id' => $order->id];
        }

        return ['success' => true, 'order_id' => $order->id];
    }

    protected function ensureCredentialsAreConfigured(): void
    {
        if (blank($this->merchantId) || blank($this->password) || blank($this->integritySalt)) {
            throw new RuntimeException('JazzCash credentials are not configured. Set JAZZCASH_MERCHANT_ID, JAZZCASH_PASSWORD, and JAZZCASH_INTEGRITY_SALT in your .env file.');
        }
    }

    protected function verifyHash(array $data): bool
    {
        if (empty($data['pp_SecureHash'])) {
            return false;
        }

        $incomingHash = $data['pp_SecureHash'];
        $fieldsToVerify = collect($data)->except('pp_SecureHash')->toArray();

        $computedHash = $this->generateHash($fieldsToVerify);

        return hash_equals($computedHash, $incomingHash);
    }
}