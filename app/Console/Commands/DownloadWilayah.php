<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class DownloadWilayah extends Command
{
    protected $signature = 'wilayah:download';

    protected $description = 'Download wilayah Indonesia ke file lokal';

    public function handle()
    {
        $folder = public_path('wilayah');

        if (! File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $files = [
            'provinces.json' => 'https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json',

            'regencies.json' => 'https://www.emsifa.com/api-wilayah-indonesia/api/regencies.json',

            'districts.json' => 'https://www.emsifa.com/api-wilayah-indonesia/api/districts.json',

            'villages.json' => 'https://www.emsifa.com/api-wilayah-indonesia/api/villages.json',
        ];

        foreach ($files as $filename => $url) {

            $this->info("Downloading {$filename}");

            $data = Http::timeout(60)->get($url)->body();

            File::put(
                $folder.'/'.$filename,
                $data
            );
        }

        $this->info('Selesai.');
    }
}
