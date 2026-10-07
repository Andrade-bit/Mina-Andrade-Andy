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
        Schema::table('cup_sizes', function (Blueprint $table) {
            $table->decimal('volume_ml', 8, 2)->nullable()->after('price');
            $table->boolean('is_recipe_size')->default(false)->after('volume_ml');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cup_sizes', function (Blueprint $table) {
            $table->dropColumn(['volume_ml', 'is_recipe_size']);
        });
    }
};
