<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_one_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_two_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('match_request_one_id')->nullable()->constrained('match_requests')->nullOnDelete();
            $table->foreignId('match_request_two_id')->nullable()->constrained('match_requests')->nullOnDelete();
            $table->string('status')->default('matched');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index('user_one_id');
            $table->index('user_two_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
