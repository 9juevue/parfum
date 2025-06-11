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
        Schema::create('product_subtypes', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedSmallInteger('product_subtype_type');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('qty');
            $table->unsignedInteger('popularity')->nullable();
            $table->foreignId('product_subtype_format_id')->nullable()->constrained('product_subtype_formats')->onDelete('cascade');
            $table->decimal('volume', 10, 2)->nullable();
            $table->decimal('bottle_volume', 10, 2)->nullable();
            $table->string('volume_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_subtypes');
    }
};
