<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('operation_reefer_monitoring_logs', function(Blueprint $table){

            $table->id();


            $table->foreignId('operation_id')
                ->constrained('operations');


            $table->foreignId('reefer_operation_id')
                ->nullable()
                ->constrained('operation_reefers');


            $table->foreignId('reefer_monitoring_id')
                ->nullable()
                ->constrained('reefer_monitorings');


            $table->string('action',50);


            $table->string('status',30);


            $table->decimal('temperature',8,2)
                ->nullable();


            $table->decimal('set_temperature',8,2)
                ->nullable();


            $table->decimal('voltage',10,2)
                ->nullable();


            $table->decimal('ampere',10,2)
                ->nullable();


            $table->string('alarm',255)
                ->nullable();


            $table->text('note')
                ->nullable();


            $table->foreignId('monitored_by')
                ->nullable()
                ->constrained('users');


            $table->dateTime('monitored_at')
                ->nullable();


            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users');


            $table->string('ip_address',45)
                ->nullable();


            $table->timestamp('created_at');


            $table->index('operation_id');
            $table->index('reefer_operation_id');
            $table->index('reefer_monitoring_id');

        });

    }


    public function down(): void
    {
        Schema::dropIfExists('operation_reefer_monitoring_logs');
    }

};