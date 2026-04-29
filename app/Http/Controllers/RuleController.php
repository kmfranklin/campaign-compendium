<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use App\Models\RuleSet;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
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
        $ruleSets = RuleSet::with(['rules:id,name,rule_set_id'])
            ->withCount('rules')
            ->orderBy('name')
            ->get();

        $conditions = Condition::select('id', 'name')->orderBy('name')->get();

        return view('rules.index', [
            'ruleSets'       => $ruleSets,
            'conditionCount' => $conditions->count(),
            'searchEntries'  => $this->buildSearchEntries($conditions, $ruleSets),
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
        // Rules must be eager-loaded so buildSearchEntries can map them.
        $allRuleSets = RuleSet::with(['rules:id,name,rule_set_id'])->orderBy('name')->get();
        $entries     = Condition::orderBy('name')->get();

        return view('rules.show', [
            'title'         => 'Conditions',
            'description'   => 'Status effects that alter a creature\'s capabilities.',
            'descHtml'      => null,  // conditions have no long-form desc to render
            'entries'       => $entries,
            'currentSlug'   => 'conditions',
            'allRuleSets'   => $allRuleSets,
            'searchEntries' => $this->buildSearchEntries($entries, $allRuleSets),
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

        // We need all rulesets with their rules eager-loaded for the global
        // search entries, plus all conditions. The rules select is kept lean —
        // only the three columns needed to build search URLs.
        $allRuleSets   = RuleSet::with(['rules:id,name,rule_set_id'])->orderBy('name')->get();
        $allConditions = Condition::select('id', 'name')->orderBy('name')->get();

        return view('rules.show', [
            'title'         => $ruleSet->name,
            'description'   => $ruleSet->desc,
            'descHtml'      => $ruleSet->desc_html,  // rendered Markdown for content-only sections
            'entries'       => $ruleSet->rules,
            'currentSlug'   => $ruleSet->slug,
            'allRuleSets'   => $allRuleSets,
            'searchEntries' => $this->buildSearchEntries($allConditions, $allRuleSets),
        ]);
    }

    /**
     * Build the flat search-entries array used by Alpine on every rules page.
     *
     * Each entry is [ name, section, url ] — just enough for the dropdown to
     * display a grouped result list and navigate on click. Body text is
     * intentionally excluded to keep the JS payload small.
     *
     * Extracted as a private method so index(), conditions(), and show() all
     * produce identical data without duplicating the mapping logic.
     */
    private function buildSearchEntries(
        EloquentCollection $conditions,
        EloquentCollection $ruleSets
    ): Collection {
        return $conditions
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
    }
}
