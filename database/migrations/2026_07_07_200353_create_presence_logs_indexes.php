<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mongodb')->table('presence_logs', function (Blueprint $collection) {
            $collection->index('user_id', 'idx_presence_user_id');
            $collection->index(['user_id' => 1, 'created_at' => -1], 'idx_presence_user_created');
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->table('presence_logs', function (Blueprint $collection) {
            $collection->dropIndex('idx_presence_user_id');
            $collection->dropIndex('idx_presence_user_created');
        });
    }
};
