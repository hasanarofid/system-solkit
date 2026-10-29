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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
            $table->decimal('price', 14, 2)->default(0)->after('description');
            $table->decimal('compare_at_price', 14, 2)->nullable()->after('price');
            $table->integer('stock')->default(100)->after('compare_at_price');
            $table->integer('weight_grams')->default(250)->after('stock');
            $table->json('images')->nullable()->after('thumbnail');
            $table->string('material')->nullable()->change();

            $table->index(['tenant_id', 'is_featured', 'status']);
            $table->index(['tenant_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id', 'is_featured', 'status']);
            $table->dropIndex(['tenant_id', 'slug']);
            $table->dropColumn(['tenant_id', 'price', 'compare_at_price', 'stock', 'weight_grams', 'images']);
        });
    }
};
