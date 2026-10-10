<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('supply_purchases')->whereNotNull('invoice_number')->get(['id', 'invoice_number']);
        $seen = [];
        foreach ($rows as $row) {
            $reference = mb_strtoupper(trim($row->invoice_number));
            if ($reference !== '' && isset($seen[$reference])) {
                throw new RuntimeException('Resolve duplicate purchase references on purchases '.$seen[$reference].' and '.$row->id.' before migrating.');
            }
            $seen[$reference] = $row->id;
        }
        foreach ($rows as $row) {
            DB::table('supply_purchases')->where('id', $row->id)->update(['invoice_number' => mb_strtoupper(trim($row->invoice_number)) ?: null]);
        }
        Schema::table('supply_purchases', fn (Blueprint $table) => $table->unique('invoice_number'));
    }

    public function down(): void
    {
        Schema::table('supply_purchases', fn (Blueprint $table) => $table->dropUnique(['invoice_number']));
    }
};
