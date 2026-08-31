<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {
        Schema::create('operation_reefers', function(Blueprint $table){

            $table->id();


            $table->foreignId('operation_id')
                ->constrained('operations');


            $table->dateTime('plugin_at')
                ->nullable();


            $table->dateTime('unplug_at')
                ->nullable();


            $table->string('status',30)
                ->nullable();


            $table->timestamps();


            $table->index('operation_id');
            $table->index('status');
            $table->index('plugin_at');
            $table->index('unplug_at');

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('operation_reefers');
    }

};