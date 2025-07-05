<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Penyakit;
use App\Models\Tahun;

class MetabaseKasusService extends MetabaseBaseService
{
    // Database configuration
    private const DATABASE_ID = 2;
    private const TABLE_ID = 9;
    // private const COLLECTION_ID = 4; //metabase oss baru 1
    private const COLLECTION_ID = 6; //metabase oss baru 2

    // Field IDs metabase oss baru 1
    // private const FIELD_CASES = 73; // Field untuk jumlah terjangkit
    // private const FIELD_DISTRICT = 72; // Field untuk kecamatan_id
    // private const FIELD_YEAR = 78; // Field untuk tahun_id
    // private const FIELD_DISEASE = 77; // Field untuk penyakit_id

    // Field IDs metabase oss baru 2
    private const FIELD_CASES = 80; // Field untuk jumlah terjangkit
    private const FIELD_DEATHS = 152; // Field untuk jumlah meninggal
    private const FIELD_DISTRICT = 79; // Field untuk kecamatan_id
    private const FIELD_YEAR = 83; // Field untuk tahun_id
    private const FIELD_DISEASE = 82; // Field untuk penyakit_id

    public function createDiseaseQuestion($tahun, $penyakit)
    {
        $this->refreshSessionIfNeeded();

        $tahunValue = Tahun::find($tahun)->tahun ?? $tahun;
        $penyakitValue = Penyakit::find($penyakit)->nama_penyakit ?? $penyakit;

        $questionData = [
            'name' => "Jumlah Terjangkit Penyakit {$penyakitValue} Kab. Lamongan Tahun {$tahunValue}",
            'description' => "Pemetaan kasus {$penyakitValue} berdasarkan kecamatan tahun {$tahunValue}",
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
                            self::FIELD_DISTRICT,
                            ['base-type' => 'type/BigInteger']
                        ]
                    ],
                    'filter' => [
                        'and',
                        ['=', [
                            'field',
                            self::FIELD_YEAR,
                            ['base-type' => 'type/BigInteger']
                        ], $tahun],
                        ['=', [
                            'field',
                            self::FIELD_DISEASE,
                            ['base-type' => 'type/Text']
                        ], $penyakit]
                    ],
                    'aggregation-idents' => [
                        '0' => '8wBlDzUcuIhgh9XaAMSO8',
                        '1' => 'Qzo3k4l54i1GOqbPVSsHp'
                    ],
                    'breakout-idents' => [
                        '0' => '4rmM9kMT_PMUYzwz1btrT'
                    ]
                ]
            ],
            'visualization_settings' => [
                'map.type' => 'region',
                'map.region' => '793ecd97-821e-400a-7b9d-34f6b204d38e',
                'map.colors' => $this->generateColorGradient(),
                'map.color_scheme' => 'Custom',
                'map.dimension_config' => [
                    'color' => [
                        'type' => 'quantile'
                    ]
                ],
                'map.metric' => 'sum',
                'map.dimension' => 'kecamatan_id'
            ],
            'display' => 'map'
        ];

        $response = Http::timeout(30)
            ->withHeaders([
                'X-Metabase-Session' => $this->sessionId
            ])
            // ->post(
            //     "{$this->baseUrl}/api/card",
            //     array_merge($questionData, $this->getDefaultMapSettings())
            // );
            ->post("{$this->baseUrl}/api/card", $questionData);

        if (!$response->successful()) {
            Log::error('Failed to create disease case map', [
                'tahun' => $tahun,
                'penyakit' => $penyakit,
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new \Exception('Failed to create disease case map: ' . $response->body());
        }

        return $response->json();
    }

    public function updateDiseaseQuestion($cardId, $tahun, $penyakit)
    {
        $this->refreshSessionIfNeeded();

        $tahunValue = Tahun::find($tahun)->tahun ?? $tahun;
        $penyakitValue = Penyakit::find($penyakit)->nama_penyakit ?? $penyakit;

        $questionData = [
            'name' => "Jumlah Terjangkit Penyakit {$penyakitValue} Kab. Lamongan Tahun {$tahunValue}",
            'description' => "Pemetaan kasus {$penyakitValue} berdasarkan kecamatan tahun {$tahunValue}",
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
                        ['field', self::FIELD_DISTRICT, 
                            ['base-type' => 'type/BigInteger']
                        ]
                    ],
                    'filter' => [
                        'and',
                        ['=', ['field', self::FIELD_YEAR, 
                            ['base-type' => 'type/BigInteger']
                        ], $tahun],
                        ['=', ['field', self::FIELD_DISEASE, 
                            ['base-type' => 'type/Text']
                        ], $penyakit]
                    ],
                    'aggregation-idents' => [
                        '0' => '8wBlDzUcuIhgh9XaAMSO8',
                        '1' => 'Qzo3k4l54i1GOqbPVSsHp'
                    ],
                    'breakout-idents' => [
                        '0' => '4rmM9kMT_PMUYzwz1btrT'
                    ]
                ]
            ]
        ];

        $response = Http::timeout(30)
            ->withHeaders([
                'X-Metabase-Session' => $this->sessionId
            ])
            ->put("{$this->baseUrl}/api/card/{$cardId}", $questionData);

        if (!$response->successful()) {
            Log::error('Failed to update disease case map', [
                'cardId' => $cardId,
                'tahun' => $tahun,
                'penyakit' => $penyakit,
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new \Exception('Failed to update disease case map: ' . $response->body());
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

            Log::info('Getting embed URL for card', ['cardId' => $cardId]);

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
            Log::info('Successfully got embed URL', ['metabase_url' => $embedUrl]);

            return $embedUrl;
        } catch (\Exception $e) {
            Log::error('Error getting embed URL: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
