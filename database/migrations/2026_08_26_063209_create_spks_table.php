<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('spks', function(Blueprint $table){

            $table->id();



            $table->string('no_spk',100)
                ->unique();



            $table->string('no_dok',100)
                ->nullable();



            $table->dateTime('tgl_dok')
                ->nullable();



            $table->string('status',30)
                ->nullable();



            $table->timestamps();



            $table->index('no_dok');
            $table->index('status');


        });

    }


    public function down(): void
    {
        Schema::dropIfExists('spks');
    }

};