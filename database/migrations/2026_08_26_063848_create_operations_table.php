<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('operations', function(Blueprint $table){

            $table->id();


            $table->foreignId('spk_id')
                ->nullable()
                ->constrained('spks');


            $table->foreignId('container_id')
                ->nullable()
                ->constrained('containers');


            $table->string('current_process',50);


            $table->string('status',30)
                ->default('PROCESS');


            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users');


            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users');


            $table->dateTime('started_at')
                ->nullable();


            $table->dateTime('finished_at')
                ->nullable();


            $table->timestamps();



            $table->index('spk_id');
            $table->index('container_id');
            $table->index('current_process');
            $table->index('status');

            $table->index('created_by');
            $table->index('updated_by');


            $table->index([
                'spk_id',
                'container_id'
            ]);


            $table->index([
                'container_id',
                'status'
            ]);


            $table->index([
                'current_process',
                'status'
            ]);

        });

    }



    public function down(): void
    {
        Schema::dropIfExists('operations');
    }

};