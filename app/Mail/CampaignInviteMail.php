<?php

namespace App\Mail;

use App\Models\CampaignInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CampaignInvite $invite)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->invite->inviter->name} invited you to join {$this->invite->campaign->name}"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign-invite',
            with: [
                'invite' => $this->invite,
                'joinUrl' => route('invites.show', $this->invite->token),
            ],
        );
    }
}
