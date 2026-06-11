<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WilayahController extends Controller
{
    public function provinsi()
    {
        return response()->json(
            Cache::remember(
                'wilayah_provinsi',
                now()->addMonth(),
                fn () => Http::timeout(10)
                    ->connectTimeout(5)
                    ->get(
                        'https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json'
                    )
                    ->json()
            )
        );
    }

    public function kota(int $provinsi_id)
    {
        return response()->json(
            Cache::remember(
                "wilayah_kota_{$provinsi_id}",
                now()->addMonth(),
                fn () => Http::timeout(10)
                    ->connectTimeout(5)
                    ->get(
                        "https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provinsi_id}.json"
                    )
                    ->json()
            )
        );
    }

    public function kecamatan(int $kota_id)
    {
        return response()->json(
            Cache::remember(
                "wilayah_kecamatan_{$kota_id}",
                now()->addMonth(),
                fn () => Http::timeout(10)
                    ->connectTimeout(5)
                    ->get(
                        "https://www.emsifa.com/api-wilayah-indonesia/api/districts/{$kota_id}.json"
                    )
                    ->json()
            )
        );
    }

    public function kelurahan(int $kecamatan_id)
    {
        return response()->json(
            Cache::remember(
                "wilayah_kelurahan_{$kecamatan_id}",
                now()->addMonth(),
                fn () => Http::timeout(10)
                    ->connectTimeout(5)
                    ->get(
                        "https://www.emsifa.com/api-wilayah-indonesia/api/villages/{$kecamatan_id}.json"
                    )
                    ->json()
            )
        );
    }
}
