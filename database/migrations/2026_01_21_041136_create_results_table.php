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
        Schema::disableForeignKeyConstraints();

        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->nullable();
            $table->string('url')->nullable();
            $table->longText('description')->nullable();
            $table->string('target_account')->nullable();
            $table->string('capture')->nullable();
            $table->foreignId('domain_id')->nullable()->constrained();
            $table->foreignId('validator_id')->nullable()->constrained('users');
            $table->foreignId('team_id')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->enum('status',['new','validated','unvalidated'])->default('new');
            $table->integer('hits')->default(0);
            $table->foreignId('tracer_id')->nullable()->constrained('tracers');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
