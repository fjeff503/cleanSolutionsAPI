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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id('idRoom');
            $table->string('name');
            $table->string('description')->nullable();
            $table->dateTime('lastCleaning');
            //hacer name unico pero en cada level
            $table->unique(['name', 'idLevel']);
            //relacion level
            $table->unsignedBigInteger('idLevel');
            $table->foreign('idLevel')->references('idLevel')->on('levels')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
