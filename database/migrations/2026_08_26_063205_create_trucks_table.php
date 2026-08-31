<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('trucks', function(Blueprint $table){

            $table->id();


            $table->string('no_truck',50)
                ->unique();


            $table->string('no_plat',30)
                ->nullable();


            $table->string('truck_type',50)
                ->nullable();


            $table->boolean('is_active')
                ->default(true);


            $table->timestamps();



            $table->index('no_plat');
            $table->index('truck_type');
            $table->index('is_active');


        });

    }


    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }

};