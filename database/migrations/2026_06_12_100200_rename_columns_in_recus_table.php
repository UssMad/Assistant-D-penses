<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recus', function (Blueprint $table) {
            $table->renameColumn('texte_source', 'texte_brut');
            $table->renameColumn('payload_brut', 'payload_ia');
        });
    }

    public function down(): void
    {
        Schema::table('recus', function (Blueprint $table) {
            $table->renameColumn('texte_brut', 'texte_source');
            $table->renameColumn('payload_ia', 'payload_brut');
        });
    }
};
