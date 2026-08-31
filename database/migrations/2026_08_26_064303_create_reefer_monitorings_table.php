<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('reefer_monitorings', function(Blueprint $table){

            $table->id();


            $table->foreignId('reefer_operation_id')
                ->constrained('operation_reefers');


            $table->decimal('temperature',8,2)
                ->nullable();


            $table->decimal('set_temperature',8,2)
                ->nullable();


            $table->decimal('voltage',10,2)
                ->nullable();


            $table->decimal('ampere',10,2)
                ->nullable();


            $table->string('alarm',255)
                ->nullable();


            $table->text('note')
                ->nullable();


            $table->foreignId('monitored_by')
                ->nullable()
                ->constrained('users');


            $table->dateTime('monitored_at');


            $table->timestamps();



            $table->index('reefer_operation_id');
            $table->index('monitored_by');
            $table->index('monitored_at');

        });

    }


    public function down(): void
    {
        Schema::dropIfExists('reefer_monitorings');
    }

};