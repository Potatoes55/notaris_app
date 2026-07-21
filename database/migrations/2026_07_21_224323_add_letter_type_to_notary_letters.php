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
        Schema::table('notary_letters', function (Blueprint $table) {
            //
            $table->enum('letter_type', ['surat_keluar', 'surat_masuk'])->default('surat_keluar')->after('letter_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notary_letters', function (Blueprint $table) {
            //
            $table->dropColumn('letter_type');
        });
    }
};
