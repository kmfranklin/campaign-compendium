<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        Schema::table('quests', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        $this->backfillTableSlugs('spells', 'name');
        $this->backfillTableSlugs('creatures', 'name');
        $this->backfillTableSlugs('items', 'name');
        $this->backfillTableSlugs('campaigns', 'name');
        $this->backfillTableSlugs('quests', 'title');
    }

    public function down(): void
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->dropUnique('quests_slug_unique');
            $table->dropColumn('slug');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropUnique('campaigns_slug_unique');
            $table->dropColumn('slug');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropUnique('items_slug_unique');
            $table->dropColumn('slug');
        });
    }

    private function backfillTableSlugs(string $table, string $sourceColumn): void
    {
        $rows = DB::table($table)
            ->select('id', $sourceColumn)
            ->orderBy('id')
            ->get();

        $used = [];

        foreach ($rows as $row) {
            $sourceValue = (string) ($row->{$sourceColumn} ?? '');
            $base = Str::slug($sourceValue);

            if ($base === '') {
                $base = Str::singular($table) . '-' . $row->id;
            }

            $slug = $base;
            $suffix = 2;

            while (in_array($slug, $used, true)) {
                $slug = "{$base}-{$suffix}";
                $suffix++;
            }

            DB::table($table)
                ->where('id', $row->id)
                ->update(['slug' => $slug]);

            $used[] = $slug;
        }
    }
};
