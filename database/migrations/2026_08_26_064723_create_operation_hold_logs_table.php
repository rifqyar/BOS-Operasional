<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('operation_hold_logs', function(Blueprint $table){

            $table->id();


            $table->foreignId('operation_id')
                ->constrained('operations');


            $table->foreignId('hold_id')
                ->nullable()
                ->constrained('operation_holds');


            $table->string('action',20);


            $table->string('status',30);


            $table->string('reason',255)
                ->nullable();


            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users');


            $table->dateTime('action_at')
                ->nullable();


            $table->json('data_before')
                ->nullable();


            $table->json('data_after')
                ->nullable();


            $table->string('ip_address',45)
                ->nullable();


            $table->timestamp('created_at');


            $table->index('operation_id');
            $table->index('hold_id');
            $table->index('user_id');

        });

    }


    public function down(): void
    {
        Schema::dropIfExists('operation_hold_logs');
    }

};