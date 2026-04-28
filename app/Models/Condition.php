<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class Condition extends Model
{
    protected $fillable = ['slug', 'name', 'body'];

    /**
     * Accessor: returns the body rendered as safe HTML from Markdown.
     * Identical pattern to Rule — any model with Markdown content
     * gets the same treatment.
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
