<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Invite</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1f2937; margin: 0; padding: 24px; background: #f8fafc;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 32px;">
        <h1 style="margin-top: 0; font-size: 28px; color: #111827;">You're invited to join {{ $invite->campaign->name }}</h1>

        <p><strong>{{ $invite->inviter->name }}</strong> invited you to join their campaign on Campaign Compendium.</p>

        <p>
            Use the button below to accept the invite.
            @if ($invite->expires_at)
                This invite expires on {{ $invite->expires_at->toFormattedDayDateString() }}.
            @endif
        </p>

        <p style="margin: 32px 0;">
            <a href="{{ $joinUrl }}" style="display: inline-block; background: #d4a72c; color: #111827; text-decoration: none; font-weight: 700; padding: 12px 20px; border-radius: 8px;">
                View Invite
            </a>
        </p>

        <p style="font-size: 14px; color: #6b7280;">If you already have an account, sign in with <strong>{{ $invite->email }}</strong>. If not, you'll be able to create one and join automatically.</p>

        <p style="font-size: 14px; color: #6b7280; word-break: break-all;">If the button doesn't work, use this link: {{ $joinUrl }}</p>
    </div>
</body>
</html>
