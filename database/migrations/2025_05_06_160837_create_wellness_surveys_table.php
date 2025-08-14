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
        Schema::create('wellness_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date'); // date du sondage
            $table->unsignedTinyInteger('sleep')->comment('Note de 1 à 10');
            $table->unsignedTinyInteger('stress')->comment('Note de 1 à 10');
            $table->unsignedTinyInteger('soreness')->comment('Note de 1 à 10');
            $table->unsignedTinyInteger('energy')->comment('Note de 1 à 10');
            $table->timestamps();
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wellness_surveys');
    }
};
