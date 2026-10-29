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
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('store_name')->nullable();
            $table->string('tagline')->nullable();
            $table->text('about')->nullable();
            $table->string('primary_color')->default('#2563EB');
            $table->string('secondary_color')->default('#0F172A');
            $table->string('currency')->default('IDR');
            $table->string('currency_symbol')->default('Rp');
            $table->text('wa_checkout_template')->nullable();
            $table->text('llm_system_prompt')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->boolean('is_ai_sales_enabled')->default(true);
            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
