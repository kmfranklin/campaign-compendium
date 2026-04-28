<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class Rule extends Model
{
    protected $fillable = ['slug', 'name', 'rule_set_id', 'body'];

    /**
     * A rule belongs to a rule set (its category).
     */
    public function ruleSet(): BelongsTo
    {
        return $this->belongsTo(RuleSet::class);
    }

    /**
     * Accessor: returns the body rendered as safe HTML from Markdown.
     *
     * Calling $rule->body_html in a view automatically invokes this.
     * The Attribute::make() pattern is the modern Laravel 9+ way to
     * define accessors — cleaner than the old getBodyHtmlAttribute() style.
     */
    protected function bodyHtml(): Attribute
    {
        return Attribute::make(
            get: fn () => (new GithubFlavoredMarkdownConverter([
                'html_input'         => 'strip',
                'allow_unsafe_links' => false,
            ]))->convert($this->body)->getContent(),
        );
    }
}
