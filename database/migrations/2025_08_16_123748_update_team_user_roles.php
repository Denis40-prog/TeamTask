<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            // Mettre à jour les valeurs existantes de 'member' vers 'user'
            DB::table('team_user')->where('role', 'member')->update(['role' => 'user']);

            // Changer la colonne pour utiliser ENUM au lieu de string
            $table->dropColumn('role');
        });

        Schema::table('team_user', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin', 'rh'])->default('user')->after('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('team_user', function (Blueprint $table) {
            $table->string('role')->default('member')->after('team_id');
        });
    }
};
