<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public $withinTransaction = false; // <-- ajoute ça

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('session_focus', function (Blueprint $table) {
            $table->boolean('is_valid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_focus', function (Blueprint $table) {
            $table->dropColumn([
                'is_valid'
            ]);
        });
    }
};
