<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {
        Schema::create('operation_inspections', function(Blueprint $table){

            $table->id();


            $table->foreignId('operation_id')
                ->constrained('operations');


            $table->foreignId('job_slip_id')
                ->nullable()
                ->constrained('job_slips');


            $table->foreignId('behandlein_id')
                ->nullable()
                ->constrained('operation_behandleins');


            $table->foreignId('equipment_id')
                ->nullable()
                ->constrained('equipments');


            $table->foreignId('operator_id')
                ->nullable()
                ->constrained('users');


            $table->string('no_seal',100)
                ->nullable();


            $table->foreignId('container_type_id')
                ->nullable()
                ->constrained('container_types');


            $table->dateTime('started_at')
                ->nullable();


            $table->dateTime('finished_at')
                ->nullable();


            $table->string('status',30)
                ->nullable();


            $table->text('note')
                ->nullable();


            $table->timestamps();



            $table->index('operation_id');
            $table->index('job_slip_id');
            $table->index('behandlein_id');
            $table->index('equipment_id');
            $table->index('operator_id');
            $table->index('container_type_id');
            $table->index('status');

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('operation_inspections');
    }

};