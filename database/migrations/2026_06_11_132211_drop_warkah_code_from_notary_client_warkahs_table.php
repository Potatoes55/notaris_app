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
        Schema::table('notary_client_warkahs', function (Blueprint $table) {
            $table->dropColumn('warkah_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notary_client_warkahs', function (Blueprint $table) {
            $table->string('warkah_code')->nullable();
        });
    }
};
