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
            // ->nullable()->change();
            $table->enum('legal_status', [
                'legal_entity',      // badan hukum
                'non_legal_entity',  // non badan hukum
            ])->nullable()->change();
            $table->string('business_form')->nullable()->change();
            $table->string('pic_name')->nullable()->change();
            $table->string('nik')->nullable()->change();
            $table->string('birth_place')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->string('marital_status')->nullable()->change();
            $table->string('job')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
};
