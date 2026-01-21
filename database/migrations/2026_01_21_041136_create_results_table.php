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
            $table->string('type');
            $table->string('keyword');
            $table->string('url');
            $table->longText('description');
            $table->string('target_account')->nullable();
            $table->string('capture')->nullable();
            $table->foreignId('domain_id')->constrained();
            $table->foreignId('validator_id')->constrained('users');
            $table->foreignId('team_id')->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->enum('status',['new','validated','unvalidated']);
            $table->integer('hits')->default(0);
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
