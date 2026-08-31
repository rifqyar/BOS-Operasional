<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('yard_locations', function(Blueprint $table){

            $table->id();


            $table->string('block',30);


            $table->string('slot',30)
                ->nullable();


            $table->string('tier',20)
                ->nullable();



            $table->string('location_code',100)
                ->unique();



            $table->boolean('is_occupied')
                ->default(false);



            $table->boolean('is_active')
                ->default(true);



            $table->timestamps();



            $table->index('block');
            $table->index('slot');
            $table->index('tier');
            $table->index('is_occupied');
            $table->index('is_active');


        });

    }


    public function down(): void
    {
        Schema::dropIfExists('yard_locations');
    }

};