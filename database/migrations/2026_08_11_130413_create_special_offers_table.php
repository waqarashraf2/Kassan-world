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
        Schema::create('special_offers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ur')->nullable();
            $table->string('slug')->unique();
            $table->string('banner_image')->nullable();
            $table->unsignedInteger('discount_percentage')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ur')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_top')->default(false)->index()->after('is_featured');
            $table->foreignId('special_offer_id')->nullable()->after('category_id')->constrained('special_offers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['special_offer_id']);
            $table->dropColumn(['is_top', 'special_offer_id']);
        });

        Schema::dropIfExists('special_offers');
    }
};
