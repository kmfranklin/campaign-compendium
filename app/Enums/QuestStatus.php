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
            self::Planned   => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
            self::Active    => 'bg-accent/10 text-accent',
            self::Completed => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            self::Failed    => 'bg-danger/10 text-danger',
            self::Abandoned => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        };
    }
}
