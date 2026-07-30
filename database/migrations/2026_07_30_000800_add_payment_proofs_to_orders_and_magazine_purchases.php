<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('payment_proof_path')->nullable()->after('payment_details');
            $table->string('payment_proof_original_name')->nullable()->after('payment_proof_path');
        });

        Schema::table('magazine_purchases', function (Blueprint $table): void {
            $table->json('payment_details')->nullable()->after('payment_reference');
            $table->string('payment_proof_path')->nullable()->after('payment_details');
            $table->string('payment_proof_original_name')->nullable()->after('payment_proof_path');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['payment_proof_path', 'payment_proof_original_name']);
        });

        Schema::table('magazine_purchases', function (Blueprint $table): void {
            $table->dropColumn(['payment_details', 'payment_proof_path', 'payment_proof_original_name']);
        });
    }
};
