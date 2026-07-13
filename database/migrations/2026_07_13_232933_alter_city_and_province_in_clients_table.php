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
            $table->string('province')->nullable()->change();
            $table->string('city')->nullable()->change();

            $table->string('province_id')->nullable()->after('province');
            $table->string('province_name')->nullable()->after('province_id');
            $table->string('kecamatan_id')->nullable()->after('province_name');
            $table->string('kecamatan_name')->nullable()->after('kecamatan_id');
            $table->string('kelurahan_id')->nullable()->after('kecamatan_name');
            $table->string('kelurahan_name')->nullable()->after('kelurahan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            //
            $table->string('province')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();

            $table->dropColumn([
                'province_id',
                'province_name',
                'city_id',
                'kecamatan_name',
                'kelurahan_id',
                'kelurahan_name',
            ]);
        });
    }
};
