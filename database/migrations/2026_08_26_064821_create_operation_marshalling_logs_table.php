<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('operation_marshalling_logs', function(Blueprint $table){

            $table->id();


            $table->foreignId('operation_id')
                ->constrained('operations');


            $table->foreignId('marshalling_id')
                ->nullable()
                ->constrained('operation_marshallings');


            $table->string('action',50);

            $table->string('status',30);


            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users');


            $table->text('note')
                ->nullable();


            $table->json('data_before')
                ->nullable();


            $table->json('data_after')
                ->nullable();


            $table->string('ip_address',45)
                ->nullable();


            $table->timestamp('created_at');


            $table->index('operation_id');
            $table->index('marshalling_id');
            $table->index('user_id');

        });

    }


    public function down(): void
    {
        Schema::dropIfExists('operation_marshalling_logs');
    }

};