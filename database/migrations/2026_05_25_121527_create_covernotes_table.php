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
        Schema::create('covernotes', function (Blueprint $bluePrint) {
            $bluePrint->id();
            $bluePrint->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $bluePrint->string('client_code')->nullable();
            $bluePrint->string('covernote_number');
            $bluePrint->string('recipient')->nullable();
            $bluePrint->string('subject')->nullable();
            $bluePrint->date('date')->nullable();
            $bluePrint->date('expiry_date')->nullable(); 
            $bluePrint->text('attachment')->nullable();
            $bluePrint->string('file_path')->nullable();
            $bluePrint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('covernotes');
    }
};