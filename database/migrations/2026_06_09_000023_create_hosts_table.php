<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('is_verified')->default(false);
            $table->unsignedBigInteger('total_earnings')->default(0);
            $table->unsignedInteger('total_sessions')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosts');
    }
};
