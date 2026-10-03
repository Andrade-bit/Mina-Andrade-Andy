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
        Schema::table('supply_purchases', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('purchase_source');
            $table->text('notes')->nullable()->after('payment_terms');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_purchases', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'notes', 'tax_amount']);
        });
    }
};
