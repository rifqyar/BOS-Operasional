<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {
        Schema::create('spk_containers', function(Blueprint $table){

            $table->id();


            $table->foreignId('spk_id')
                ->constrained('spks');


            $table->foreignId('container_id')
                ->constrained('containers');


            $table->string('status',30)
                ->nullable();


            $table->boolean('fl_hold')
                ->default(false);


            $table->boolean('fl_warna_hold')
                ->default(false);


            $table->boolean('fl_send_npct1')
                ->default(false);


            $table->timestamps();



            $table->index('spk_id');
            $table->index('container_id');
            $table->index(['spk_id','container_id']);
            $table->index(['container_id','status']);

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('spk_containers');
    }

};