<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->table('tickets', function (Blueprint $collection) {
            $collection->index('user_id');
            $collection->index('assigned_to');
            $collection->index(['status' => 1, 'prioridade' => -1]);
        });

        Schema::connection('mongodb')->table('messages', function (Blueprint $collection) {
            $collection->index(['ticket_id' => 1, 'created_at' => -1]);
            $collection->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->table('tickets', function (Blueprint $collection) {
            $collection->dropIndex('user_id_1');
            $collection->dropIndex('assigned_to_1');
            $collection->dropIndex('status_1_prioridade_-1');
        });

        Schema::connection('mongodb')->table('messages', function (Blueprint $collection) {
            $collection->dropIndex('ticket_id_1_created_at_-1');
            $collection->dropIndex('user_id_1');
        });
    }
};
