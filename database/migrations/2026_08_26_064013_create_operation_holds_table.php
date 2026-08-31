<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {
        Schema::create('operation_holds', function(Blueprint $table){

            $table->id();


            $table->foreignId('operation_id')
                ->constrained('operations');


            $table->string('action',20);


            $table->string('reason',255)
                ->nullable();


            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users');


            $table->dateTime('action_at');


            $table->timestamps();


            $table->index('operation_id');
            $table->index('action');
            $table->index('user_id');
            $table->index('action_at');


        });
    }


    public function down(): void
    {
        Schema::dropIfExists('operation_holds');
    }

};