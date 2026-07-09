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
        Schema::table('notary_costs', function (Blueprint $table) {
            //
            $table->double('pph')->nullable()->after('other_cost');
            $table->double('bphtb')->nullable()->after('pph');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notary_costs', function (Blueprint $table) {
            //
            $table->dropColumn('pph');
            $table->dropColumn('bphtb');
        });
    }
};
