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
        Schema::create('product_top_notes', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->constrained('products')->cascadeOnDelete();
            $table->foreignId('aroma_note_id')
                ->constrained('aroma_notes')->cascadeOnDelete();
            $table->primary(['product_id','aroma_note_id']);
        });
        Schema::create('product_middle_notes', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->constrained('products')->cascadeOnDelete();
            $table->foreignId('aroma_note_id')
                ->constrained('aroma_notes')->cascadeOnDelete();
            $table->primary(['product_id','aroma_note_id']);
        });
        Schema::create('product_base_notes', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->constrained('products')->cascadeOnDelete();
            $table->foreignId('aroma_note_id')
                ->constrained('aroma_notes')->cascadeOnDelete();
            $table->primary(['product_id','aroma_note_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_note_pivot_tables');
    }
};
