<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Tahun;

class MetabasePendudukService extends MetabaseBaseService
{
    // Database configuration
    private const DATABASE_ID = 2;
    private const TABLE_ID = 19;
    // private const COLLECTION_ID = 4; //metabase oss baru 1
    private const COLLECTION_ID = 6; //metabase oss baru 2

    // Field IDs metabase oss baru 1
    // private const FIELD_POPULATION = 132;
    // private const FIELD_DISTRICT = 136;
    // private const FIELD_YEAR = 135;

    // Field IDs metabase oss baru 1
    private const FIELD_POPULATION = 106;
    private const FIELD_DISTRICT = 105;
    private const FIELD_YEAR = 103;

    public function createQuestion($tahun)
    {
        $this->refreshSessionIfNeeded();

        $tahunValue = Tahun::find($tahun)->tahun ?? $tahun;

        $questionData = [
            'name' => "Jumlah Penduduk Kab. Lamongan Tahun {$tahunValue}",
            'description' => "Pemetaan jumlah penduduk berdasarkan kecamatan tahun {$tahunValue}",
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
                                self::FIELD_POPULATION,
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
                        '=',
                        [
                            'field',
                            self::FIELD_YEAR,
                            ['base-type' => 'type/BigInteger']
                        ],
                        $tahun
                    ]
                ]
            ]
        ];

        $response = Http::timeout(30)
            ->withHeaders([
                'X-Metabase-Session' => $this->sessionId
            ])
            ->post(
                "{$this->baseUrl}/api/card",
                array_merge($questionData, $this->getDefaultMapSettings())
            );

        if (!$response->successful()) {
            Log::error('Failed to create Metabase question', [
                'tahun' => $tahun,
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new \Exception('Failed to create Metabase question: ' . $response->body());
        }

        return $response->json();
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
