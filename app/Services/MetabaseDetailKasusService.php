<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Penyakit;
use App\Models\Tahun;
use App\Models\Kecamatan;

class MetabaseDetailKasusService extends MetabaseBaseService
{
    // Database configuration
    private const DATABASE_ID = 2;
    private const TABLE_ID = 21; // Table untuk detail kasus (desa level)
    private const COLLECTION_ID = 6;

    // Field IDs untuk detail kasus (desa level)
    private const FIELD_CASES = 161; // Field untuk jumlah terjangkit
    private const FIELD_DEATHS = 162; // Field untuk jumlah meninggal
    private const FIELD_VILLAGE = 160; // Field untuk desa_id
    private const FIELD_YEAR = 163; // Field untuk tahun_id
    private const FIELD_PENYAKIT = 158; // Field untuk penyakit_id
    
    // Default map region (dapat disesuaikan atau dibuat dinamis)
    private const DEFAULT_MAP_REGION = '16d3e55a-595c-5aef-3936-52fcc6af8783';
    
    // Map region IDs untuk setiap kecamatan - dapat diperluas sesuai kebutuhan
    private const KECAMATAN_MAP_REGIONS = [
        21 => '16d3e55a-595c-5aef-3936-52fcc6af8783', // Karanggeneng
        // Tambahkan mapping untuk kecamatan lain jika diperlukan
        // Jika tidak ada mapping spesifik, akan menggunakan DEFAULT_MAP_REGION
    ];

    public function createDetailDiseaseQuestion($tahun, $penyakit, $kecamatan)
    {
        $this->refreshSessionIfNeeded();

        $tahunValue = Tahun::find($tahun)->tahun ?? $tahun;
        $penyakitValue = Penyakit::find($penyakit)->nama_penyakit ?? $penyakit;
        $kecamatanValue = Kecamatan::find($kecamatan)->nama_kecamatan ?? $kecamatan;

        // Get map region for the specific kecamatan, fallback to default if not found
        $mapRegion = self::KECAMATAN_MAP_REGIONS[$kecamatan] ?? self::DEFAULT_MAP_REGION;

        // Get desa IDs for the specific kecamatan
        $desaIds = \App\Models\Desa::where('kecamatan_id', $kecamatan)->pluck('id')->toArray();
        
        if (empty($desaIds)) {
            throw new \Exception("Tidak ada desa yang ditemukan untuk kecamatan ID: {$kecamatan}");
        }

        // Build dynamic filter for desa IDs based on the selected kecamatan
        $desaFilter = $this->buildDesaFilter($desaIds);

        $questionData = [
            'name' => "Jumlah Terjangkit Penyakit {$penyakitValue} Kec. {$kecamatanValue} Tahun {$tahunValue}",
            'description' => "Pemetaan kasus {$penyakitValue} berdasarkan desa tahun {$tahunValue}",
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
                        ],
                        [
                            'sum',
                            [
                                'field',
                                self::FIELD_DEATHS,
                                ['base-type' => 'type/Integer']
                            ]
                        ]
                    ],
                    'breakout' => [
                        [
                            'field',
                            self::FIELD_VILLAGE,
                            ['base-type' => 'type/BigInteger']
                        ]
                    ],
                    'filter' => [
                        'and',
                        // Filter by tahun_id (dynamic)
                        ['=', [
                            'field',
                            self::FIELD_YEAR,
                            ['base-type' => 'type/BigInteger']
                        ], (int)$tahun],
                        // Filter by penyakit_id (dynamic)
                        ['=', [
                            'field',
                            self::FIELD_PENYAKIT,
                            ['base-type' => 'type/BigInteger']
                        ], (int)$penyakit],
                        // Filter by desa IDs (dynamic based on selected kecamatan)
                        $desaFilter
                    ]
                ]
            ],
            'visualization_settings' => [
                'map.type' => 'region',
                'map.region' => $mapRegion,
                'map.colors' => $this->generateColorGradient(),
                'map.color_scheme' => 'Custom',
                'map.dimension_config' => [
                    'color' => [
                        'type' => 'quantile'
                    ]
                ],
                'map.metric' => 'sum',
                'map.dimension' => 'desa_id'
            ],
            'display' => 'map'
        ];

        $response = Http::timeout(30)
            ->withHeaders([
                'X-Metabase-Session' => $this->sessionId
            ])
            ->post("{$this->baseUrl}/api/card", $questionData);

        if (!$response->successful()) {
            Log::error('Failed to create detail disease case map', [
                'tahun' => $tahun,
                'penyakit' => $penyakit,
                'kecamatan' => $kecamatan,
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new \Exception('Failed to create detail disease case map: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Build dynamic filter for desa IDs based on kecamatan
     */
    private function buildDesaFilter($desaIds)
    {
        if (count($desaIds) === 1) {
            return ['=', [
                'field',
                self::FIELD_VILLAGE,
                ['base-type' => 'type/BigInteger']
            ], (int)$desaIds[0]];
        }
        
        // For multiple desas, build OR filter
        $orFilters = array_map(function($desaId) {
            return ['=', [
                'field',
                self::FIELD_VILLAGE,
                ['base-type' => 'type/BigInteger']
            ], (int)$desaId];
        }, $desaIds);

        return array_merge(['or'], $orFilters);
    }

    public function updateDetailDiseaseQuestion($cardId, $tahun, $penyakit, $kecamatan)
    {
        $this->refreshSessionIfNeeded();

        $tahunValue = Tahun::find($tahun)->tahun ?? $tahun;
        $penyakitValue = Penyakit::find($penyakit)->nama_penyakit ?? $penyakit;
        $kecamatanValue = Kecamatan::find($kecamatan)->nama_kecamatan ?? $kecamatan;

        // Get map region for the specific kecamatan, fallback to default
        $mapRegion = self::KECAMATAN_MAP_REGIONS[$kecamatan] ?? self::DEFAULT_MAP_REGION;

        // Get desa IDs for the specific kecamatan
        $desaIds = \App\Models\Desa::where('kecamatan_id', $kecamatan)->pluck('id')->toArray();
        
        if (empty($desaIds)) {
            throw new \Exception("Tidak ada desa yang ditemukan untuk kecamatan ID: {$kecamatan}");
        }

        // Build dynamic filter for desa IDs
        $desaFilter = $this->buildDesaFilter($desaIds);

        $questionData = [
            'name' => "Jumlah Terjangkit Penyakit {$penyakitValue} Kec. {$kecamatanValue} Tahun {$tahunValue}",
            'description' => "Pemetaan kasus {$penyakitValue} berdasarkan desa tahun {$tahunValue}",
            'dataset_query' => [
                'database' => self::DATABASE_ID,
                'type' => 'query',
                'query' => [
                    'source-table' => self::TABLE_ID,
                    'aggregation' => [
                        ['sum', ['field', self::FIELD_CASES, 
                            ['base-type' => 'type/Integer']]
                        ],
                        ['sum', ['field', self::FIELD_DEATHS, 
                            ['base-type' => 'type/Integer']]
                        ]
                    ],
                    'breakout' => [
                        ['field', self::FIELD_VILLAGE, 
                            ['base-type' => 'type/BigInteger']
                        ]
                    ],
                    'filter' => [
                        'and',
                        // Filter by tahun_id (dynamic)
                        ['=', ['field', self::FIELD_YEAR, 
                            ['base-type' => 'type/BigInteger']
                        ], (int)$tahun],
                        // Filter by penyakit_id (dynamic)
                        ['=', ['field', self::FIELD_PENYAKIT, 
                            ['base-type' => 'type/BigInteger']
                        ], (int)$penyakit],
                        // Filter by desa IDs (dynamic based on kecamatan)
                        $desaFilter
                    ]
                ]
            ],
            'visualization_settings' => [
                'map.type' => 'region',
                'map.region' => $mapRegion,
                'map.colors' => $this->generateColorGradient(),
                'map.color_scheme' => 'Custom',
                'map.dimension_config' => [
                    'color' => [
                        'type' => 'quantile'
                    ]
                ],
                'map.metric' => 'sum',
                'map.dimension' => 'desa_id'
            ],
            'display' => 'map'
        ];

        $response = Http::timeout(30)
            ->withHeaders([
                'X-Metabase-Session' => $this->sessionId
            ])
            ->put("{$this->baseUrl}/api/card/{$cardId}", $questionData);

        if (!$response->successful()) {
            Log::error('Failed to update detail disease case map', [
                'cardId' => $cardId,
                'tahun' => $tahun,
                'penyakit' => $penyakit,
                'kecamatan' => $kecamatan,
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new \Exception('Failed to update detail disease case map: ' . $response->body());
        }

        return $response->json();
    }

    public function getCardDetails($cardId)
    {
        $this->refreshSessionIfNeeded();

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-Metabase-Session' => $this->sessionId
                ])
                ->get("{$this->baseUrl}/api/card/{$cardId}");

            if (!$response->successful()) {
                Log::error('Failed to get card details', [
                    'cardId' => $cardId,
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);
                throw new \Exception('Failed to get card details: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error getting card details', [
                'cardId' => $cardId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function publishCard($cardId)
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

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to publish card', [
                'cardId' => $cardId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function getEmbedUrl($cardId)
    {
        try {
            if (empty($this->sessionId)) {
                throw new \Exception('No valid session ID available');
            }

            Log::info('Getting embed URL for detail card', ['cardId' => $cardId]);

            // First, make sure embedding is enabled
            $this->publishCard($cardId);

            // Then get the public link
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-Metabase-Session' => $this->sessionId
                ])
                ->post($this->baseUrl . "/api/card/{$cardId}/public_link");

            if (!$response->successful()) {
                Log::error('Failed to get embed URL', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Failed to get embed URL: ' . $response->body());
            }

            $uuid = $response->json('uuid');
            if (empty($uuid)) {
                throw new \Exception('UUID not found in response');
            }

            $embedUrl = "{$this->baseUrl}/public/question/{$uuid}";
            Log::info('Successfully got embed URL for detail map', ['metabase_url' => $embedUrl]);

            return $embedUrl;
        } catch (\Exception $e) {
            Log::error('Error getting embed URL for detail map: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Add new map region for kecamatan - untuk ekspansi di masa depan
     */
    public static function addKecamatanMapRegion($kecamatanId, $mapRegionId)
    {
        Log::info('Map region should be added', [
            'kecamatan_id' => $kecamatanId,
            'map_region_id' => $mapRegionId
        ]);
        // Implementasi untuk menambah mapping baru bisa ditambahkan di sini
        // Misalnya: menyimpan ke config file atau database
    }

    /**
     * Get available map regions
     */
    public static function getAvailableMapRegions()
    {
        return self::KECAMATAN_MAP_REGIONS;
    }

    /**
     * Get map region for specific kecamatan
     */
    public function getMapRegionForKecamatan($kecamatanId)
    {
        return self::KECAMATAN_MAP_REGIONS[$kecamatanId] ?? self::DEFAULT_MAP_REGION;
    }

    /**
     * Delete existing question before creating new one
     */
    public function deleteExistingQuestion($cardId)
    {
        try {
            $this->refreshSessionIfNeeded();
            
            $response = Http::withHeaders([
                'X-Metabase-Session' => $this->sessionId
            ])->delete("{$this->baseUrl}/api/card/{$cardId}");

            if ($response->successful()) {
                Log::info('Successfully deleted existing question', ['cardId' => $cardId]);
                return true;
            } else {
                Log::warning('Failed to delete existing question', [
                    'cardId' => $cardId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error deleting existing question', [
                'cardId' => $cardId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}