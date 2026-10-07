<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The amount of an ingredient one drink needs in a particular cup size (Small 1 g, Medium 2 g, Large 3 g).
     * A size with no row here falls back to the ingredient's base amount, adjusted for cup volume.
     */
    public function up(): void
    {
        Schema::create('product_ingredient_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnDelete();
            $table->foreignId('cup_size_id')->constrained('cup_sizes')->cascadeOnDelete();
            $table->decimal('quantity_required', 10, 2);
            $table->timestamps();

            $table->unique(['product_id', 'ingredient_id', 'cup_size_id'], 'product_ingredient_size_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_ingredient_sizes');
    }
};
