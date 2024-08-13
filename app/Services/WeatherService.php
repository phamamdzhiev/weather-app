<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENWEATHER_API_KEY');
    }

    public function getCoordinates(string $city)
    {
        $url = "https://api.openweathermap.org/geo/1.0/direct?q={$city}&appid={$this->apiKey}";

        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
                return [
                    'lat' => $data[0]['lat'],
                    'lon' => $data[0]['lon'],
                ];
            }

            return [
                'error' => true,
                'message' => 'Unable to find location coordinates',
            ];
        }

        return $this->handleErrorResponse($response);
    }

    public function getWeatherData($lat, $lon)
    {
        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&lang=bg&units=metric&appid={$this->apiKey}";

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return $this->handleErrorResponse($response);
    }

    protected function handleErrorResponse($response)
    {
        if ($response->clientError() || $response->serverError()) {
            $errorData = $response->json();

            if (isset($errorData['cod']) && isset($errorData['message'])) {
                return [
                    'error' => true,
                    'code' => $errorData['cod'],
                    'message' => $errorData['message'],
                    'parameters' => $errorData['parameters'] ?? []
                ];
            }

            return [
                'error' => true,
                'message' => 'An unexpected error occurred',
            ];
        }

        return [
            'error' => true,
            'message' => 'Unable to fetch data',
        ];
    }
}
