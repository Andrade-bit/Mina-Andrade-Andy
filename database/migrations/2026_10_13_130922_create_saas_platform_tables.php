<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const SHOP_TABLES = ['product_categories', 'inventory_items', 'products', 'ingredients', 'product_ingredients', 'cup_sizes', 'credentials', 'inventory_transactions', 'expense_categories', 'expenses', 'suppliers', 'supply_purchases', 'supply_purchase_items', 'sales_transactions', 'sales_transaction_items', 'product_cup_sizes', 'promos', 'product_ingredient_sizes', 'inventory_batches', 'uploaded_images'];

    public function up(): void
    {
        if (DB::table('users')->select('name')->groupBy('name')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Give existing admin accounts unique usernames before enabling shared SaaS login.');
        }
        Schema::create('plans', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->unsignedInteger('max_products')->nullable();
            $table->unsignedInteger('max_staff')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable();
            $table->timestamps();
        });
        $planId = DB::table('plans')->insertGetId(['name' => 'Existing shop', 'monthly_price' => 0, 'features' => json_encode(['Inventory', 'POS', 'Reports']), 'created_at' => now(), 'updated_at' => now()]);
        Schema::create('shops', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status')->default('active');
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->string('subscription_status')->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('renews_at')->nullable();
            $table->timestamps();
        });
        DB::table('shops')->insert(['id' => 1, 'name' => 'Catbrews', 'slug' => 'catbrews', 'plan_id' => $planId, 'status' => 'active', 'subscription_status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('shop_id')->nullable()->default(1)->constrained()->restrictOnDelete();
            $table->string('role')->default('owner');
            $table->boolean('is_active')->default(true);
            $table->unique('name');
        });
        foreach (self::SHOP_TABLES as $name) {
            Schema::table($name, function (Blueprint $table): void {
                $table->foreignId('shop_id')->default(1)->constrained()->restrictOnDelete();
            });
        }
        foreach (['credentials' => 'passcode', 'promos' => 'code', 'supply_purchases' => 'invoice_number'] as $name => $column) {
            Schema::table($name, function (Blueprint $table) use ($column): void {
                $table->dropUnique([$column]);
                $table->unique(['shop_id', $column]);
            });
        }
        Schema::create('platform_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->text('description');
            $table->timestamps();
        });
        Schema::create('platform_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('platform_name')->default('Catbrews');
            $table->string('support_email')->nullable();
            $table->unsignedInteger('trial_days')->default(14);
            $table->timestamps();
        });
        DB::table('platform_settings')->insert(['id' => 1, 'platform_name' => 'Catbrews', 'trial_days' => 14, 'created_at' => now(), 'updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('shops')->where('id', '!=', 1)->exists()) {
            throw new RuntimeException('Cannot remove shop isolation after additional shops have been created. Use a forward migration.');
        }
        Schema::dropIfExists('platform_activities');
        Schema::dropIfExists('platform_settings');
        foreach (['credentials' => 'passcode', 'promos' => 'code', 'supply_purchases' => 'invoice_number'] as $name => $column) {
            Schema::table($name, function (Blueprint $table) use ($column): void {
                $table->dropUnique(['shop_id', $column]);
                $table->unique($column);
            });
        }
        foreach (array_reverse(self::SHOP_TABLES) as $name) {
            Schema::table($name, fn (Blueprint $table) => $table->dropConstrainedForeignId('shop_id'));
        }
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['name']);
            $table->dropConstrainedForeignId('shop_id');
            $table->dropColumn(['role', 'is_active']);
        });
        Schema::dropIfExists('shops');
        Schema::dropIfExists('plans');
    }
};
