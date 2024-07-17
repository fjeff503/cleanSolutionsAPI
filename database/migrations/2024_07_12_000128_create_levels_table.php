<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id('idLevel');
            $table->string('name');
            $table->string('description')->nullable();
            //hacer name unico pero en cada building
            $table->unique(['name', 'idBuilding']);
            //relacion building
            $table->unsignedBigInteger('idBuilding');
            $table->foreign('idBuilding')->references('idBuilding')->on('buildings')->onUpdate('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
