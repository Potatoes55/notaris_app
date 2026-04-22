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
        Schema::create('proses_lain', function (Blueprint $table) {
            $table->id();
            $table->string('client_code', 50)->nullable();
            $table->foreign('client_code')
                ->references('client_code')
                ->on('clients')
                ->onDelete('set null');
            $table->foreignId('notaris_id')
                ->nullable()
                ->constrained('notaris')
                ->cascadeOnDelete();
            $table->string('transaction_code')->nullable();
            $table->string('name')->nullable();
            $table->integer('time_estimation')->nullable();
            $table->foreignId('pic_id')
                ->nullable()
                ->constrained('pic_documents')
                ->cascadeOnDelete();
            $table->string('status')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proses_lain');
    }
};
