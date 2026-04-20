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
        Schema::table('results', function (Blueprint $table) {
            // drop foreign key dulu
            $table->dropForeign(['domain_id']);

            // ubah kolom jadi nullable
            $table->foreignId('domain_id')->nullable()->change();

            // tambahkan lagi foreign key dengan nullOnDelete
            $table->foreign('domain_id')
                ->references('id')
                ->on('domains')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign(['domain_id']);

            $table->foreignId('domain_id')->nullable(false)->change();

            $table->foreign('domain_id')
                ->references('id')
                ->on('domains');
        });
    }
};
