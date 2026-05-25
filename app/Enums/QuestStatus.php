<?php

namespace App\Enums;

enum QuestStatus: string
{
    case Planned   = 'planned';
    case Active    = 'active';
    case Completed = 'completed';
    case Failed    = 'failed';
    case Abandoned = 'abandoned';

    /**
     * Human-readable label for display.
     */
    public function label(): string
    {
        return match($this) {
            self::Planned   => 'Planned',
            self::Active    => 'Active',
            self::Completed => 'Completed',
            self::Failed    => 'Failed',
            self::Abandoned => 'Abandoned',
        };
    }

    /**
     * Tailwind classes for the status badge pill.
     *
     * Full class strings are used (no interpolation) so the Tailwind
     * content scanner can detect them when app/Enums is in the content paths.
     */
    public function badgeClasses(): string
    {
        return match($this) {
            self::Planned   => 'ui-badge ui-badge-muted',
            self::Active    => 'ui-badge ui-badge-accent',
            self::Completed => 'ui-badge ui-badge-success',
            self::Failed    => 'ui-badge ui-badge-danger',
            self::Abandoned => 'ui-badge ui-badge-warning',
        };
    }
}
