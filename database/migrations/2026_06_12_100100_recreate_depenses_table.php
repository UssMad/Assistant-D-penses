<?php

use App\Enums\CategorieDepense;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('depenses');

        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recu_id')->constrained()->onDelete('cascade');
            $table->string('libelle');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->string('categorie');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');

        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('montant', 10, 2)->nullable();
            $table->string('devise', 3)->default('EUR');
            $table->dateTime('date_achat')->nullable();
            $table->string('description')->nullable();
            $table->string('categorie')->nullable();
            $table->timestamps();
        });
    }
};
