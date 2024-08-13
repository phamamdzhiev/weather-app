<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function getWeather(Request $request)
    {
        $request->validate([
            'city' => 'required|string',
        ]);

        $city = $request->input('city');

        $coordinates = $this->weatherService->getCoordinates($city);

        if (isset($coordinates['error']) && $coordinates['error']) {
            return response()->json([
                'error' => $coordinates['message'],
                'code' => $coordinates['code'] ?? null,
                'parameters' => $coordinates['parameters'] ?? [],
            ], 400);
        }

        $weatherData = $this->weatherService->getWeatherData($coordinates['lat'], $coordinates['lon']);

        if (isset($weatherData['error']) && $weatherData['error']) {
            return response()->json([
                'error' => $weatherData['message'],
                'code' => $weatherData['code'] ?? null,
                'parameters' => $weatherData['parameters'] ?? [],
            ], 400);
        }

        return response()->json($weatherData, 200);
    }
}
