<?php

namespace App\Http\Controllers;

class WilayahController extends Controller
{
    public function provinsi()
    {
        $data = json_decode(
            file_get_contents(
                public_path('wilayah/provinces.json')
            ),
            true
        );

        return response()->json($data);
    }

    public function kota(int $provinsi_id)
    {
        $data = json_decode(
            file_get_contents(
                public_path('wilayah/regencies.json')
            ),
            true
        );

        $result = collect($data)
            ->where('province_id', $provinsi_id)
            ->values();

        return response()->json($result);
    }

    public function kecamatan(int $kota_id)
    {
        $data = json_decode(
            file_get_contents(
                public_path('wilayah/districts.json')
            ),
            true
        );

        $result = collect($data)
            ->where('regency_id', $kota_id)
            ->values();

        return response()->json($result);
    }

    public function kelurahan(int $kecamatan_id)
    {
        $data = json_decode(
            file_get_contents(
                public_path('wilayah/villages.json')
            ),
            true
        );

        $result = collect($data)
            ->where('district_id', $kecamatan_id)
            ->values();

        return response()->json($result);
    }
}
