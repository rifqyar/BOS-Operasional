<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('container_types', function (Blueprint $table) {

            $table->id();

            $table->string('code', 20)
                ->unique();

            $table->string('name', 100);

            $table->string('size', 10)
                ->nullable();

            $table->string('iso_code', 20)
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();


            $table->index('size');
            $table->index('iso_code');
            $table->index('is_active');

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('container_types');
    }
};