<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class RuleSet extends Model
{
    protected $fillable = ['slug', 'name', 'desc'];

    /**
     * A rule set has many individual rules.
     * Ordered alphabetically by name for consistent display.
     */
    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class)->orderBy('name');
    }

    /**
     * Accessor: renders the desc field as HTML from Markdown.
     *
     * Some rule sets have all their content in desc (no child Rule records),
     * so we need to be able to render it as rich HTML on the show page.
     * Returns null if there is no desc so the view can check safely.
     */
    protected function descHtml(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->desc
                ? (new GithubFlavoredMarkdownConverter([
                    'html_input'         => 'strip',
                    'allow_unsafe_links' => false,
                ]))->convert($this->desc)->getContent()
                : null,
        );
    }

    /**
     * Accessor: returns the desc as clean plain text with Markdown syntax stripped.
     *
     * Used for card previews on the index page where we want readable text
     * without asterisks, hashes, or other Markdown symbols showing literally.
     * Str::of() returns a fluent Stringable — we chain replacements and
     * call toString() at the end to get a plain PHP string back.
     */
    protected function descPlain(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->desc) return null;

                return Str::of($this->desc)
                    ->replaceMatches('/\*{1,3}([^*\n]+)\*{1,3}/', '$1') // ***bold italic***, **bold**, *italic*
                    ->replaceMatches('/_{1,2}([^_\n]+)_{1,2}/', '$1')   // __bold__, _italic_
                    ->replaceMatches('/#{1,6}\s+/', '')                  // ## Headings
                    ->replaceMatches('/\[([^\]]+)\]\([^)]+\)/', '$1')   // [link text](url)
                    ->replaceMatches('/`[^`]+`/', '')                    // `inline code`
                    ->replaceMatches('/\n{2,}/', ' ')                   // paragraph breaks → space
                    ->replaceMatches('/\n/', ' ')                       // line breaks → space
                    ->replaceMatches('/\s{2,}/', ' ')                   // collapse whitespace
                    ->trim()
                    ->toString();
            }
        );
    }
}
