<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Penyakit;

class MetabasePenyakitService extends MetabaseBaseService
{
    // Database configuration
    private const DATABASE_ID = 2;
    private const TABLE_ID = 9;
    // private const COLLECTION_ID = 4; //metabase oss baru 1
    private const COLLECTION_ID = 6; //metabase oss baru 2

    // Field IDs metabase oss baru 1
    // private const FIELD_CASES = 73;      // Jumlah terjangkit
    // private const FIELD_YEAR = 109;      // Tahun dari relasi
    // private const FIELD_YEAR_SOURCE = 78; // Field sumber tahun
    // private const FIELD_DISEASE = 77;    // ID Penyakit

    // Field IDs metabase oss baru 2
    private const FIELD_CASES = 80;      // Jumlah terjangkit
    private const FIELD_YEAR = 123;      // Tahun dari relasi
    private const FIELD_YEAR_SOURCE = 83; // Field sumber tahun
    private const FIELD_DISEASE = 82;    // ID Penyakit

    private function generateRandomColor()
    {
        // Generate vibrant base color
        $hue = rand(0, 359);         // Full hue spectrum
        $saturation = rand(60, 90);  // High saturation for vibrant colors
        $lightness = rand(45, 65);   // Medium lightness for good visibility

        // Create primary color (more saturated)
        $primary = $this->HSLToHex($hue, $saturation, $lightness);

        // Create lighter version for secondary color
        $secondary = $this->HSLToHex(
            $hue,
            max($saturation - 30, 20),  // Lower saturation
            min($lightness + 25, 90)    // Higher lightness
        );

        return [
            'primary' => $primary,
            'secondary' => $secondary
        ];
    }

    private function HSLToHex($h, $s, $l)
    {
        // Convert HSL percentages to decimals
        $h /= 360;
        $s /= 100;
        $l /= 100;

        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = $this->hueToRGB($p, $q, $h + 1 / 3);
            $g = $this->hueToRGB($p, $q, $h);
            $b = $this->hueToRGB($p, $q, $h - 1 / 3);
        }

        // Convert to hex
        return sprintf(
            "#%02x%02x%02x",
            round($r * 255),
            round($g * 255),
            round($b * 255)
        );
    }

    private function hueToRGB($p, $q, $t)
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1 / 6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1 / 2) return $q;
        if ($t < 2 / 3) return $p + ($q - $p) * (2 / 3 - $t) * 6;
        return $p;
    }

    private function ensureReadableColor($color)
    {
        // Strip # if present
        $hex = ltrim($color, '#');

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Calculate relative luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        // If color is too light or too dark, regenerate
        if ($luminance < 0.2 || $luminance > 0.8) {
            return false;
        }

        return true;
    }

    public function createTrendQuestion($penyakitId)
    {
        $this->refreshSessionIfNeeded();

        $penyakit = Penyakit::findOrFail($penyakitId);

        // Generate random colors and ensure they're readable
        do {
            $colors = $this->generateRandomColor();
        } while (!$this->ensureReadableColor($colors['primary']));

        $questionData = [
            'name' => "Jumlah Terjangkit Penyakit {$penyakit->nama_penyakit} Kab. Lamongan Sejak Tahun 2020",
            'description' => "Penyakit {$penyakit->nama_penyakit} dipetakan berdasarkan kecamatan sejak tahun 2020",
            'collection_id' => self::COLLECTION_ID,
            'dataset_query' => [
                'database' => self::DATABASE_ID,
                'type' => 'query',
                'query' => [
                    'source-table' => self::TABLE_ID,
                    'aggregation' => [
                        [
                            'sum',
                            [
                                'field',
                                self::FIELD_CASES,
                                ['base-type' => 'type/Integer']
                            ]
                        ]
                    ],
                    'breakout' => [
                        [
                            'field',
                            self::FIELD_YEAR,
                            [
                                'base-type' => 'type/Integer',
                                'source-field' => self::FIELD_YEAR_SOURCE
                            ]
                        ]
                    ],
                    'filter' => [
                        '=',
                        [
                            'field',
                            self::FIELD_DISEASE,
                            ['base-type' => 'type/BigInteger']
                        ],
                        $penyakitId
                    ]
                ]
            ],
            'display' => 'area',
            'visualization_settings' => [
                'graph.dimensions' => ['tahun'],
                'graph.metrics' => ['sum'],
                // 'graph.x_axis.scale' => 'linear',
                'graph.x_axis.scale' => 'timesery',
                'graph.y_axis.title_text' => "Jumlah Terjangkit {$penyakit->nama_penyakit}",
                'graph.x_axis.title_text' => "Tahun",
                'series_settings' => [
                    'sum' => [
                        'color' => $colors['primary'],
                        'line.interpolate' => 'cardinal',
                        'line.style' => 'solid',
                        'line.size' => 'L',
                        'area.opacity' => 0.2
                    ]
                ],
                'graph.area.opacity' => 0.2,
                'graph.colors' => [$colors['primary'], $colors['secondary']]
            ]
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-Metabase-Session' => $this->sessionId
                ])
                ->post("{$this->baseUrl}/api/card", $questionData);

            if (!$response->successful()) {
                Log::error('Failed to create Metabase trend question', [
                    'penyakit' => $penyakitId,
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);
                throw new \Exception('Failed to create Metabase trend question: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error creating trend question', [
                'penyakit' => $penyakitId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getEmbedUrl($cardId)
    {
        $response = Http::withHeaders([
            'X-Metabase-Session' => $this->sessionId
        ])->post($this->baseUrl . "/api/card/{$cardId}/public_link");

        if ($response->successful()) {
            $uuid = $response->json('uuid');
            return "{$this->baseUrl}/public/question/{$uuid}";
        }

        return null;
    }
}
