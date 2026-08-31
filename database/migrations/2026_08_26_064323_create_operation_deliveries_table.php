<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('operation_deliveries', function(Blueprint $table){

            $table->id();


            $table->foreignId('operation_id')
                ->constrained('operations');


            $table->foreignId('truck_id')
                ->nullable()
                ->constrained('trucks');


            $table->foreignId('gate_id')
                ->nullable()
                ->constrained('gates');


            $table->dateTime('truck_in_at')
                ->nullable();


            $table->dateTime('chassis_at')
                ->nullable();


            $table->dateTime('inspect_at')
                ->nullable();


            $table->dateTime('gate_out_at')
                ->nullable();


            $table->string('status',30)
                ->nullable();


            $table->timestamps();



            $table->index('operation_id');
            $table->index('truck_id');
            $table->index('gate_id');
            $table->index('status');

        });

    }


    public function down(): void
    {
        Schema::dropIfExists('operation_deliveries');
    }

};