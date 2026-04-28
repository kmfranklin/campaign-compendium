<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use App\Models\RuleSet;
use Illuminate\View\View;

class RuleController extends Controller
{
    /**
     * Overview page: a card grid of all rule categories.
     *
     * withCount('rules') adds a rules_count attribute to each RuleSet
     * without loading the actual rule records — efficient for a summary view.
     */
    public function index(): View
    {
        // Load rule names + IDs alongside the count — both used on this page.
        // We select only the columns needed for search (no body text) to keep
        // the payload light. rule_set_id must be included for the relationship
        // to correctly match rules back to their parent RuleSet.
        $ruleSets = RuleSet::with(['rules:id,name,rule_set_id,slug'])
            ->withCount('rules')
            ->orderBy('name')
            ->get();

        $conditions = Condition::select('id', 'name', 'slug')->orderBy('name')->get();

        // Build a flat array of every searchable entry with its section name
        // and pre-computed URL (including the #anchor). We do this in PHP so
        // the Blade view can pass it straight to Alpine via @js() without any
        // additional computation in JavaScript.
        $searchEntries = $conditions
            ->map(fn ($c) => [
                'name'    => $c->name,
                'section' => 'Conditions',
                'url'     => route('rules.conditions') . '#entry-' . $c->id,
            ])
            ->concat(
                $ruleSets->flatMap(fn ($rs) => $rs->rules->map(fn ($r) => [
                    'name'    => $r->name,
                    'section' => $rs->name,
                    'url'     => route('rules.show', $rs->slug) . '#entry-' . $r->id,
                ]))
            )
            ->values();

        return view('rules.index', [
            'ruleSets'       => $ruleSets,
            'conditionCount' => $conditions->count(),
            'searchEntries'  => $searchEntries,
        ]);
    }

    /**
     * The Conditions section — treated as its own first-class section
     * even though Conditions live in a separate table from Rules.
     *
     * We normalise the data into the same shape that show.blade.php expects
     * ($entries, $title, $description, $currentSlug) so we can share one view.
     */
    public function conditions(): View
    {
        $allRuleSets = RuleSet::orderBy('name')->get();
        $entries     = Condition::orderBy('name')->get();

        return view('rules.show', [
            'title'       => 'Conditions',
            'description' => 'Status effects that alter a creature\'s capabilities.',
            'descHtml'    => null,  // conditions have no long-form desc to render
            'entries'     => $entries,
            'currentSlug' => 'conditions',
            'allRuleSets' => $allRuleSets,
        ]);
    }

    /**
     * Individual ruleset section page.
     *
     * {ruleSet:slug} in the route uses implicit model binding keyed on the
     * slug column — Laravel automatically queries WHERE slug = {value} and
     * returns a 404 if nothing matches.
     *
     * load('rules') is like with() but called after the model is already
     * fetched — fine here since we only have one RuleSet to deal with.
     */
    public function show(RuleSet $ruleSet): View
    {
        $ruleSet->load('rules');
        $allRuleSets = RuleSet::orderBy('name')->get();

        return view('rules.show', [
            'title'       => $ruleSet->name,
            'description' => $ruleSet->desc,
            'descHtml'    => $ruleSet->desc_html,  // rendered Markdown for content-only sections
            'entries'     => $ruleSet->rules,
            'currentSlug' => $ruleSet->slug,
            'allRuleSets' => $allRuleSets,
        ]);
    }
}
