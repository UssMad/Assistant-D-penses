<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recus', function (Blueprint $table) {
            $table->text('message_erreur')->nullable()->after('payload_brut');
        });
    }

    public function down(): void
    {
        Schema::table('recus', function (Blueprint $table) {
            $table->dropColumn('message_erreur');
        });
    }
};
