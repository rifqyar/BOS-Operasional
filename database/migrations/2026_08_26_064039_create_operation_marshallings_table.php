<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('operation_marshallings', function(Blueprint $table){

            $table->id();


            $table->foreignId('operation_id')
                ->constrained('operations');


            $table->foreignId('job_slip_id')
                ->nullable()
                ->constrained('job_slips');


            $table->string('marshalling_type',20);


            $table->foreignId('location_from_id')
                ->nullable()
                ->constrained('yard_locations');


            $table->foreignId('location_to_id')
                ->nullable()
                ->constrained('yard_locations');


            $table->string('status',30)
                ->nullable();


            $table->dateTime('started_at')
                ->nullable();


            $table->dateTime('finished_at')
                ->nullable();


            $table->timestamps();


            $table->index('operation_id');
            $table->index('job_slip_id');
            $table->index('marshalling_type');
            $table->index('location_from_id');
            $table->index('location_to_id');
            $table->index('status');

        });

    }


    public function down(): void
    {
        Schema::dropIfExists('operation_marshallings');
    }

};