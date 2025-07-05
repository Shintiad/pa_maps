<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

abstract class MetabaseBaseService
{
    protected $baseUrl;
    protected $sessionId;
    private const SESSION_CACHE_KEY = 'metabase_session_token';
    private const SESSION_CACHE_MINUTES = 60 * 23; // 23 hours
    
    public function __construct()
    {
        $this->baseUrl = rtrim(env('METABASE_URL', 'http://localhost:3000'), '/');
        $this->initializeSession();
    }
    
    protected function initializeSession()
    {
        try {
            $this->sessionId = $this->getSessionToken();
            if (empty($this->sessionId)) {
                throw new \Exception('Failed to get valid session token');
            }
        } catch (\Exception $e) {
            Log::error('Failed to initialize Metabase session', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Metabase authentication failed: ' . $e->getMessage());
        }
    }
    
    protected function getSessionToken()
    {
        try {
            return Cache::remember(self::SESSION_CACHE_KEY, self::SESSION_CACHE_MINUTES, function () {
                $username = env('METABASE_USERNAME');
                $password = env('METABASE_PASSWORD');

                if (empty($username) || empty($password)) {
                    throw new \Exception('Metabase credentials not properly configured');
                }

                $response = Http::timeout(30)
                    ->withHeaders([
                        'Content-Type' => 'application/json'
                    ])
                    ->post("{$this->baseUrl}/api/session", [
                        'username' => $username,
                        'password' => $password
                    ]);

                if (!$response->successful()) {
                    Log::error('Metabase authentication failed', [
                        'status' => $response->status(),
                        'body' => $response->json()
                    ]);
                    throw new \Exception('Authentication failed: ' . $response->body());
                }

                $sessionId = $response->json('id');
                if (empty($sessionId)) {
                    throw new \Exception('No session ID returned from Metabase');
                }

                return $sessionId;
            });
        } catch (\Exception $e) {
            Cache::forget(self::SESSION_CACHE_KEY);
            throw $e;
        }
    }

    protected function refreshSessionIfNeeded()
    {
        try {
            if (!Cache::has(self::SESSION_CACHE_KEY)) {
                $this->sessionId = $this->getSessionToken();
            }

            // Verify session is still valid
            $testResponse = Http::withHeaders([
                'X-Metabase-Session' => $this->sessionId
            ])->get("{$this->baseUrl}/api/user/current");

            if (!$testResponse->successful()) {
                Cache::forget(self::SESSION_CACHE_KEY);
                $this->sessionId = $this->getSessionToken();
            }
        } catch (\Exception $e) {
            Log::error('Session refresh failed', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Failed to refresh Metabase session: ' . $e->getMessage());
        }
    }

    protected function makeAuthenticatedRequest($method, $endpoint, $data = [])
    {
        $this->refreshSessionIfNeeded();

        try {
            $response = Http::withHeaders([
                'X-Metabase-Session' => $this->sessionId
            ])->$method("{$this->baseUrl}{$endpoint}", $data);

            if (!$response->successful()) {
                throw new \Exception("Request failed with status {$response->status()}: {$response->body()}");
            }

            return $response;
        } catch (\Exception $e) {
            Log::error("Metabase API request failed", [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getEmbedUrl($cardId)
    {
        $this->refreshSessionIfNeeded();

        try {
            // Enable embedding for the card
            $enableResponse = Http::withHeaders([
                'X-Metabase-Session' => $this->sessionId
            ])->put("{$this->baseUrl}/api/card/{$cardId}", [
                'enable_embedding' => true,
                'embedding_params' => new \stdClass()
            ]);

            if (!$enableResponse->successful()) {
                throw new \Exception('Failed to enable embedding');
            }

            // Get the public link
            $response = Http::withHeaders([
                'X-Metabase-Session' => $this->sessionId
            ])->post("{$this->baseUrl}/api/card/{$cardId}/public_card");

            if (!$response->successful()) {
                throw new \Exception('Failed to get public link');
            }

            $uuid = $response->json('uuid');
            return "{$this->baseUrl}/public/question/{$uuid}";
        } catch (\Exception $e) {
            Log::error('Failed to get embed URL', [
                'cardId' => $cardId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function deleteQuestion($cardId)
    {
        $this->refreshSessionIfNeeded();

        try {
            $response = Http::withHeaders([
                'X-Metabase-Session' => $this->sessionId
            ])->delete("{$this->baseUrl}/api/card/{$cardId}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to delete Metabase question', [
                'cardId' => $cardId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    protected function generateColorGradient()
    {
        // Generate a random hue (0-360)
        $hue = rand(0, 360);
        
        // Fixed saturation ranges for consistent vibrancy
        $saturationRanges = [
            'veryLight' => [25, 35],
            'light' => [45, 55],
            'medium' => [65, 75],
            'dark' => [85, 90],
            'veryDark' => [95, 100]
        ];
        
        // Fixed lightness ranges for consistent progression
        $lightnessRanges = [
            'veryLight' => [85, 90],
            'light' => [70, 75],
            'medium' => [55, 60],
            'dark' => [35, 40],
            'veryDark' => [20, 25]
        ];
        
        $colors = [];
        foreach (['veryLight', 'light', 'medium', 'dark', 'veryDark'] as $intensity) {
            $saturation = rand($saturationRanges[$intensity][0], $saturationRanges[$intensity][1]);
            $lightness = rand($lightnessRanges[$intensity][0], $lightnessRanges[$intensity][1]);
            $colors[] = "hsl({$hue}, {$saturation}%, {$lightness}%)";
        }
        
        return $colors;
    }

    protected function getDefaultMapSettings()
    {
        $colors = $this->generateColorGradient();
        
        return [
            'display' => 'map',
            'visualization_settings' => [
                'map.type' => 'region',
                // 'map.region' => 'e383fad9-6a0f-b57b-2fce-8cd11f8d3660', //metabase oss baru 1
                'map.region' => '793ecd97-821e-400a-7b9d-34f6b204d38e', //metabase oss baru 2
                'map.colors' => $colors,
                'map.color_scheme' => 'Custom',
                'map.dimension_config' => [
                    'color' => [
                        'type' => 'quantile'
                    ]
                ]
            ]
        ];
    }
}