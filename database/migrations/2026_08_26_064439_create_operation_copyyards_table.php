<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('operation_copyyards', function(Blueprint $table){

            $table->id();


            $table->foreignId('operation_id')
                ->constrained('operations');


            $table->foreignId('location_from_id')
                ->nullable()
                ->constrained('yard_locations');


            $table->foreignId('location_to_id')
                ->nullable()
                ->constrained('yard_locations');


            $table->string('action_block',30)
                ->nullable();


            $table->string('status',30)
                ->nullable();


            $table->dateTime('copyyard_at')
                ->nullable();


            $table->timestamps();



            $table->index('operation_id');
            $table->index('location_from_id');
            $table->index('location_to_id');
            $table->index('status');
            $table->index('copyyard_at');


        });

    }


    public function down(): void
    {
        Schema::dropIfExists('operation_copyyards');
    }

};