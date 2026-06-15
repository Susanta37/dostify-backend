<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->string('phone', 20)->nullable()->unique()->after('email');
            $table->string('role')->default('user')->after('password');
            $table->boolean('is_profile_complete')->default(false)->after('role');
            $table->boolean('is_active')->default(true)->after('is_profile_complete');
            $table->string('referral_code', 20)->nullable()->unique()->after('is_active');
            $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->timestamp('last_seen_at')->nullable()->after('remember_token');
            $table->foreignId('created_by')->nullable()->after('last_seen_at')->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();

            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn([
                'uuid',
                'phone',
                'role',
                'is_profile_complete',
                'is_active',
                'referral_code',
                'referred_by',
                'phone_verified_at',
                'last_seen_at',
                'created_by',
                'updated_by',
            ]);
        });
    }
};
