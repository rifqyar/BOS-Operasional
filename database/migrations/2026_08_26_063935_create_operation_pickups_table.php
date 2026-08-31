<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operation_pickups', function(Blueprint $table){

            $table->id();


            $table->foreignId('operation_id')
                ->constrained('operations');


            $table->foreignId('truck_id')
                ->nullable()
                ->constrained('trucks');


            $table->string('status',30)
                ->nullable();


            $table->dateTime('pickup_at')
                ->nullable();


            $table->timestamps();


            $table->index('operation_id');
            $table->index('truck_id');
            $table->index('status');
            $table->index('pickup_at');

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('operation_pickups');
    }
};