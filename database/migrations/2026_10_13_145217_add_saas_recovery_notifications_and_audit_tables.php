<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('invitation_token', 64)->nullable()->unique();
            $table->timestamp('invitation_expires_at')->nullable();
        });
        Schema::table('plans', fn (Blueprint $table) => $table->json('enabled_features')->nullable());
        Schema::create('email_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kind', 50);
            $table->string('deduplication_key', 64)->unique();
            $table->longText('payload')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('available_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
        Schema::create('shop_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shop_id')->constrained();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('credential_id')->nullable();
            $table->string('action', 30);
            $table->string('subject_type', 80);
            $table->string('subject_id', 100)->nullable();
            $table->json('changes')->nullable();
            $table->timestamps();
            $table->index(['shop_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_activities');
        Schema::dropIfExists('email_messages');
        Schema::table('plans', fn (Blueprint $table) => $table->dropColumn('enabled_features'));
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['invitation_token']);
            $table->dropColumn(['invitation_token', 'invitation_expires_at']);
        });
    }
};
