<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {
        Schema::create('job_slips', function(Blueprint $table){

            $table->id();


            $table->foreignId('spk_container_id')
                ->constrained('spk_containers');


            $table->string('no_job',100)
                ->nullable();


            $table->string('job_type',50)
                ->nullable();


            $table->string('status',30)
                ->nullable();



            $table->foreignId('location_from_id')
                ->nullable()
                ->constrained('yard_locations');


            $table->foreignId('location_to_id')
                ->nullable()
                ->constrained('yard_locations');


            $table->timestamps();



            $table->index('spk_container_id');
            $table->index('no_job');
            $table->index('job_type');
            $table->index('status');
            $table->index('location_from_id');
            $table->index('location_to_id');

            $table->index([
                'spk_container_id',
                'status'
            ]);

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('job_slips');
    }

};