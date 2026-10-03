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
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->foreignId('promo_id')->nullable()->after('credential_id')->constrained('promos')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0)->after('promo_id');
            $table->text('void_reason')->nullable()->after('status');
            $table->foreignId('voided_by_credential_id')->nullable()->after('void_reason')->constrained('credentials')->nullOnDelete();
            $table->timestamp('voided_at')->nullable()->after('voided_by_credential_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('promo_id');
            $table->dropConstrainedForeignId('voided_by_credential_id');
            $table->dropColumn(['discount_amount', 'void_reason', 'voided_at']);
        });
    }
};
