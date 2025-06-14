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
        Schema::create('aroma_notes', function (Blueprint $table) {
            $table->id();
            $table->string('title_ru');
            $table->string('title_en');
            $table->string('color');
            $table->text('short_description')->nullable();
            $table->string('seo_title')->nullable();
            $table->longText('seo_description')->nullable();
            $table->foreignId('note_grouping_id')->constrained('note_groupings');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aroma_notes');
    }
};
