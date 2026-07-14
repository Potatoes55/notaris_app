<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            //
            $table->string('kota_id')->nullable()->after('provinsi_name');
            $table->string('kota_name')->nullable()->after('kota_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            //
            $table->dropColumn([
                'kota_id',
                'kota_name',
            ]);
        });
    }
};
