<?php

namespace Tests\Feature\Api\V1;

use App\Enums\Gender;
use App\Enums\MatchStatus;
use App\Models\MatchRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MatchTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithProfile(string $phone, Gender $gender, string $language): User
    {
        $user = User::factory()->create(['phone' => $phone, 'phone_verified_at' => now()]);
        $user->profile()->create([
            'nickname' => 'User '.$phone,
            'gender' => $gender,
            'language' => $language,
        ]);

        return $user;
    }

    public function test_user_can_request_match_and_get_matched(): void
    {
        $userA = $this->createUserWithProfile('+911111111111', Gender::Male, 'en');
        $userB = $this->createUserWithProfile('+912222222222', Gender::Female, 'en');

        MatchRequest::query()->create([
            'user_id' => $userB->id,
            'status' => MatchStatus::Pending,
            'expires_at' => now()->addMinutes(5),
        ]);

        Sanctum::actingAs($userA);

        $response = $this->postJson('/api/v1/match/request', [
            'preferred_language' => 'en',
            'match_type' => 'random',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'matched')
            ->assertJsonStructure(['match' => ['uuid', 'status', 'partner']]);
    }

    public function test_user_can_cancel_match_request(): void
    {
        $user = $this->createUserWithProfile('+913333333333', Gender::Male, 'en');
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/match/request');
        $this->postJson('/api/v1/match/cancel')->assertOk();

        $this->assertDatabaseMissing('match_requests', [
            'user_id' => $user->id,
            'status' => MatchStatus::Pending->value,
        ]);
    }
}
