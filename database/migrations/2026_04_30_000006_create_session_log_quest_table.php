<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_log_quest', function (Blueprint $table) {
            $table->foreignId('session_log_id')
                  ->constrained('session_logs')
                  ->cascadeOnDelete();

            $table->foreignId('quest_id')
                  ->constrained('quests')
                  ->cascadeOnDelete();

            $table->timestamps();

            $table->primary(['session_log_id', 'quest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_log_quest');
    }
};
