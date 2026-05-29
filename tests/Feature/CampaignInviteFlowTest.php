<?php

namespace Tests\Feature;

use App\Mail\CampaignInviteMail;
use App\Models\Campaign;
use App\Models\CampaignInvite;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CampaignInviteFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dm_can_send_email_invite_and_existing_user_gets_in_app_notification(): void
    {
        Mail::fake();

        $dm = User::factory()->create(['name' => 'Dungeon Master']);
        $player = User::factory()->create(['email' => 'player@example.com']);
        $campaign = $this->createDmCampaign($dm, 'Misty Vale');

        $this->actingAs($dm)
            ->post(route('campaigns.invites.store', $campaign), [
                'email' => 'player@example.com',
            ])
            ->assertRedirect();

        $invite = CampaignInvite::first();

        $this->assertNotNull($invite);
        $this->assertSame('player@example.com', $invite->email);
        $this->assertSame(CampaignInvite::STATUS_PENDING, $invite->status);
        $this->assertSame($player->id, $invite->invitee_id);
        $this->assertNotNull($invite->expires_at);

        Mail::assertQueued(CampaignInviteMail::class, function (CampaignInviteMail $mail) use ($invite) {
            return $mail->invite->is($invite);
        });

        $this->assertDatabaseHas('notifications', [
            'user_id' => $player->id,
            'type' => Notification::TYPE_INVITE,
            'notifiable_type' => CampaignInvite::class,
            'notifiable_id' => $invite->id,
        ]);
    }

    public function test_guest_can_register_from_invite_and_join_campaign_automatically(): void
    {
        $dm = User::factory()->create(['name' => 'Dungeon Master']);
        $campaign = $this->createDmCampaign($dm, 'Misty Vale');
        $invite = CampaignInvite::create([
            'campaign_id' => $campaign->id,
            'inviter_id' => $dm->id,
            'invitee_id' => null,
            'email' => 'newplayer@example.com',
            'token' => 'invite-token-123',
            'status' => CampaignInvite::STATUS_PENDING,
            'expires_at' => now()->addDays(7),
        ]);

        $this->get(route('invites.show', $invite->token))
            ->assertOk()
            ->assertSee('Create Account');

        $response = $this->post(route('register'), [
            'name' => 'New Player',
            'email' => 'newplayer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'invite_token' => $invite->token,
        ]);

        $player = User::where('email', 'newplayer@example.com')->first();

        $response->assertRedirect(route('campaigns.show', $campaign));
        $this->assertNotNull($player);
        $this->assertTrue($campaign->members()->where('user_id', $player->id)->exists());
        $this->assertDatabaseHas('campaign_invites', [
            'id' => $invite->id,
            'invitee_id' => $player->id,
            'status' => CampaignInvite::STATUS_ACCEPTED,
        ]);
    }

    public function test_existing_user_can_sign_in_and_accept_invite_from_tokenized_link(): void
    {
        $dm = User::factory()->create(['name' => 'Dungeon Master']);
        $player = User::factory()->create(['email' => 'player@example.com']);
        $campaign = $this->createDmCampaign($dm, 'Misty Vale');
        $invite = CampaignInvite::create([
            'campaign_id' => $campaign->id,
            'inviter_id' => $dm->id,
            'invitee_id' => $player->id,
            'email' => 'player@example.com',
            'token' => 'invite-token-456',
            'status' => CampaignInvite::STATUS_PENDING,
            'expires_at' => now()->addDays(7),
        ]);

        $this->actingAs($player)
            ->get(route('invites.show', $invite->token))
            ->assertOk()
            ->assertSee('Join Campaign');

        $response = $this->actingAs($player)
            ->post(route('invites.accept', $invite->token));

        $response->assertRedirect(route('campaigns.show', $campaign));
        $this->assertTrue($campaign->members()->where('user_id', $player->id)->exists());
        $this->assertDatabaseHas('campaign_invites', [
            'id' => $invite->id,
            'status' => CampaignInvite::STATUS_ACCEPTED,
        ]);
    }

    public function test_invite_login_screen_links_to_register_with_same_invite_token(): void
    {
        $dm = User::factory()->create(['name' => 'Dungeon Master']);
        $campaign = $this->createDmCampaign($dm, 'Misty Vale');
        $invite = CampaignInvite::create([
            'campaign_id' => $campaign->id,
            'inviter_id' => $dm->id,
            'invitee_id' => null,
            'email' => 'newplayer@example.com',
            'token' => 'invite-token-login-link',
            'status' => CampaignInvite::STATUS_PENDING,
            'expires_at' => now()->addDays(7),
        ]);

        $this->get(route('login', ['invite' => $invite->token]))
            ->assertOk()
            ->assertSee(route('register', ['invite' => $invite->token]), false)
            ->assertSee('Create an account');
    }

    public function test_wrong_account_cannot_accept_invite(): void
    {
        $dm = User::factory()->create();
        $wrongUser = User::factory()->create(['email' => 'wrong@example.com']);
        $campaign = $this->createDmCampaign($dm, 'Misty Vale');
        $invite = CampaignInvite::create([
            'campaign_id' => $campaign->id,
            'inviter_id' => $dm->id,
            'invitee_id' => null,
            'email' => 'right@example.com',
            'token' => 'invite-token-789',
            'status' => CampaignInvite::STATUS_PENDING,
            'expires_at' => now()->addDays(7),
        ]);

        $this->actingAs($wrongUser)
            ->post(route('invites.accept', $invite->token))
            ->assertForbidden();

        $this->assertFalse($campaign->members()->where('user_id', $wrongUser->id)->exists());
    }

    private function createDmCampaign(User $user, string $name): Campaign
    {
        Role::firstOrCreate(['id' => Role::DM], ['name' => 'DM']);
        Role::firstOrCreate(['id' => Role::PLAYER], ['name' => 'Player']);

        $campaign = Campaign::create([
            'name' => $name,
            'description' => 'Test campaign',
            'dm_id' => $user->id,
        ]);

        $campaign->members()->attach($user->id, ['role_id' => Role::DM]);

        return $campaign;
    }
}
