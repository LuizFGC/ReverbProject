<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('cargo', 'cargo_id');
            $table->renameColumn('departamento', 'departamento_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('cargo_id', 'cargo');
            $table->renameColumn('departamento_id', 'departamento');
        });
    }
};
