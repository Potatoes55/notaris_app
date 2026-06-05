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
            // Legalitas
            $table->enum('legal_status', [
                'legal_entity',      // badan hukum
                'non_legal_entity',  // non badan hukum
            ]);

            $table->string('business_form'); // PT, CV, Firma, Yayasan, Koperasi, dll

            // Akta
            $table->string('deed_number')->nullable();
            $table->date('deed_date')->nullable();

            // Perizinan
            $table->string('nib')->nullable();

            // Perpajakan
            // $table->string('npwp')->nullable();

            // PIC
            $table->string('pic_name');
            $table->string('pic_position')->nullable();
            $table->string('pic_phone')->nullable();
            $table->string('pic_email')->nullable();

            // Alamat
            // $table->text('address')->nullable();
            // $table->string('city')->nullable();
            // $table->string('province')->nullable();
            // $table->string('postal_code', 10)->nullable();

            // Kontak perusahaan
            // $table->string('company_phone')->nullable();
            // $table->string('company_email')->nullable();

            // $table->timestamps();
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
