<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('operation_chassis', function(Blueprint $table){

            $table->id();


            $table->foreignId('operation_id')
                ->constrained('operations');


            $table->foreignId('truck_id')
                ->nullable()
                ->constrained('trucks');


            $table->foreignId('location_id')
                ->nullable()
                ->constrained('yard_locations');


            $table->dateTime('chassis_at')
                ->nullable();


            $table->string('status',30)
                ->nullable();


            $table->timestamps();



            $table->index('operation_id');
            $table->index('truck_id');
            $table->index('location_id');
            $table->index('status');
            $table->index('chassis_at');

        });

    }


    public function down(): void
    {
        Schema::dropIfExists('operation_chassis');
    }

};