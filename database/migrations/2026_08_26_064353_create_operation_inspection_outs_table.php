<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('operation_inspection_outs', function(Blueprint $table){

            $table->id();


            $table->foreignId('operation_id')
                ->constrained('operations');


            $table->foreignId('delivery_id')
                ->nullable()
                ->constrained('operation_deliveries');


            $table->string('seal_condition',30)
                ->nullable();


            $table->string('no_seal',100)
                ->nullable();


            $table->foreignId('container_condition_id')
                ->nullable()
                ->constrained('container_conditions');


            $table->foreignId('inspected_by')
                ->nullable()
                ->constrained('users');


            $table->dateTime('inspected_at')
                ->nullable();


            $table->string('status',30)
                ->nullable();


            $table->text('note')
                ->nullable();


            $table->timestamps();



            $table->index('operation_id');
            $table->index('delivery_id');
            $table->index('container_condition_id');
            $table->index('inspected_by');
            $table->index('status');

        });

    }


    public function down(): void
    {
        Schema::dropIfExists('operation_inspection_outs');
    }

};