<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'filename',
        'path',
        'mime_type',
        'size',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The owning model (e.g. SessionLog, future: Npc, Location, etc.)
     */
    public function mediable()
    {
        return $this->morphTo();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * The URL to stream/download this file through MediaController.
     */
    public function url(): string
    {
        return route('media.show', $this);
    }

    /**
     * Human-readable file size, e.g. "42.3 MB".
     */
    public function formattedSize(): string
    {
        if ($this->size === null) {
            return 'Unknown size';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->size;
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1) . ' ' . $units[$i];
    }

    /**
     * Whether this file is an audio type (for rendering an <audio> player).
     */
    public function isAudio(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'audio/');
    }
}
