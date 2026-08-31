<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {
        Schema::create('containers', function(Blueprint $table){

            $table->id();


            $table->string('no_cont',20)
                ->unique();


            $table->foreignId('container_type_id')
                ->nullable()
                ->constrained('container_types');


            $table->foreignId('current_status_id')
                ->nullable()
                ->constrained('container_statuses');


            $table->foreignId('current_location_id')
                ->nullable()
                ->constrained('yard_locations');


            $table->string('no_seal',100)
                ->nullable();


            $table->boolean('fl_dg')
                ->default(false);


            $table->boolean('fl_oog')
                ->default(false);


            $table->string('imo',30)
                ->nullable();


            $table->boolean('is_active')
                ->default(true);


            $table->timestamps();



            $table->index('container_type_id');
            $table->index('current_status_id');
            $table->index('current_location_id');
            $table->index('is_active');

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('containers');
    }

};