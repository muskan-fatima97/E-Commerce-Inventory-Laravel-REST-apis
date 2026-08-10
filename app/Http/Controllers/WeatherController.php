<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'city' => 'required|string',
        ]);

        $cacheKey = 'weather_' . $validated['lat'] . '_' . $validated['lon'];

        $weather = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($validated) {
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $validated['lat'],
                'longitude' => $validated['lon'],
                'current' => 'temperature_2m,weather_code',
                'timezone' => 'auto',
            ]);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            return [
                'temperature' => round($data['current']['temperature_2m']),
                'weather_code' => $data['current']['weather_code'],
            ];
        });

        if (! $weather) {
            return response()->json(['success' => false, 'message' => 'Weather fetch failed'], 500);
        }

        return response()->json([
            'success' => true,
            'city' => $validated['city'],
            'temperature' => $weather['temperature'],
            'weather_code' => $weather['weather_code'],
        ]);
    }
}