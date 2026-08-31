<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {
        Schema::create('job_details', function(Blueprint $table){

            $table->id();


            $table->foreignId('job_slip_id')
                ->constrained('job_slips');


            $table->foreignId('equipment_id')
                ->nullable()
                ->constrained('equipments');


            $table->foreignId('operator_id')
                ->nullable()
                ->constrained('users');


            $table->string('status',30)
                ->nullable();


            $table->timestamps();



            $table->index('job_slip_id');
            $table->index('equipment_id');
            $table->index('operator_id');
            $table->index('status');

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('job_details');
    }

};