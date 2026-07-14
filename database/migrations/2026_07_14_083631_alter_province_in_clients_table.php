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
            $table->string('province_id')->nullable()->change();
            $table->string('province_name')->nullable()->change();

            $table->string('provinsi_id')->nullable()->after('province_id');
            $table->string('provinsi_name')->nullable()->after('provinsi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            //
            $table->string('province_id')->nullable(false)->change();
            $table->string('province_name')->nullable(false)->change();

            $table->dropColumn([
                'provinsi_id',
                'provinsi_name',
            ]);
        });
    }
};
