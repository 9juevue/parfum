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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('sku');
            $table->string('tag')->nullable();
            $table->text('description');

            $table->unsignedSmallInteger('subtype_category_type');
            $table->unsignedSmallInteger('category_type');

            $table->foreignId('brand_id')->constrained('brands');

            $table->integer('year')->nullable();
            $table->unsignedSmallInteger('gender_type')->nullable();

            $table->string('video')->nullable();

            $table->unsignedSmallInteger('longevity')->nullable();
            $table->unsignedSmallInteger('sillage')->nullable();
            $table->unsignedSmallInteger('season')->nullable();
            $table->unsignedSmallInteger('time_of_day')->nullable();
            $table->unsignedSmallInteger('gender')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
