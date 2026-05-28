<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_notification_routes(): void
    {
        $systemNotification = SystemNotification::create([
            'title' => 'Maintenance',
            'message' => 'Scheduled maintenance tonight.',
            'type' => SystemNotification::TYPE_INFO,
            'delivery_method' => SystemNotification::DELIVERY_BOTH,
            'is_active' => true,
        ]);

        $this->get(route('notifications.index'))
            ->assertRedirect(route('login'));

        $this->post(route('notifications.markAllRead'))
            ->assertRedirect(route('login'));

        $this->post(route('system-notifications.dismiss', $systemNotification))
            ->assertRedirect(route('login'));
    }

    public function test_notification_index_marks_only_current_users_system_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $systemNotification = SystemNotification::create([
            'title' => 'Maintenance',
            'message' => 'Scheduled maintenance tonight.',
            'type' => SystemNotification::TYPE_INFO,
            'delivery_method' => SystemNotification::DELIVERY_INBOX,
            'is_active' => true,
        ]);

        $userSystem = Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_SYSTEM,
            'notifiable_type' => SystemNotification::class,
            'notifiable_id' => $systemNotification->id,
            'data' => ['title' => 'Maintenance'],
        ]);

        $userCampaignUpdate = Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_CAMPAIGN_UPDATE,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Quest updated'],
        ]);

        $otherSystem = Notification::create([
            'user_id' => $otherUser->id,
            'type' => Notification::TYPE_SYSTEM,
            'notifiable_type' => SystemNotification::class,
            'notifiable_id' => $systemNotification->id,
            'data' => ['title' => 'Maintenance'],
        ]);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk();

        $this->assertNotNull($userSystem->fresh()->read_at);
        $this->assertNull($userCampaignUpdate->fresh()->read_at);
        $this->assertNull($otherSystem->fresh()->read_at);
    }

    public function test_mark_all_read_only_updates_current_users_notifications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $userNotification = Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_INVITE,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Invite'],
        ]);

        $otherNotification = Notification::create([
            'user_id' => $otherUser->id,
            'type' => Notification::TYPE_INVITE,
            'notifiable_type' => User::class,
            'notifiable_id' => $otherUser->id,
            'data' => ['message' => 'Invite'],
        ]);

        $this->actingAs($user)
            ->from(route('notifications.index'))
            ->post(route('notifications.markAllRead'))
            ->assertRedirect(route('notifications.index'));

        $this->assertNotNull($userNotification->fresh()->read_at);
        $this->assertNull($otherNotification->fresh()->read_at);
    }

    public function test_dismissing_banner_marks_matching_inbox_copy_read_for_current_user_only(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $systemNotification = SystemNotification::create([
            'title' => 'Maintenance',
            'message' => 'Scheduled maintenance tonight.',
            'type' => SystemNotification::TYPE_INFO,
            'delivery_method' => SystemNotification::DELIVERY_BOTH,
            'is_active' => true,
        ]);

        $userInbox = Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_SYSTEM,
            'notifiable_type' => SystemNotification::class,
            'notifiable_id' => $systemNotification->id,
            'data' => ['title' => 'Maintenance'],
        ]);

        $otherInbox = Notification::create([
            'user_id' => $otherUser->id,
            'type' => Notification::TYPE_SYSTEM,
            'notifiable_type' => SystemNotification::class,
            'notifiable_id' => $systemNotification->id,
            'data' => ['title' => 'Maintenance'],
        ]);

        $this->actingAs($user)
            ->post(route('system-notifications.dismiss', $systemNotification))
            ->assertRedirect();

        $this->assertDatabaseHas('system_notification_dismissals', [
            'system_notification_id' => $systemNotification->id,
            'user_id' => $user->id,
        ]);
        $this->assertNotNull($userInbox->fresh()->read_at);
        $this->assertNull($otherInbox->fresh()->read_at);
    }
}
