<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('name', 100);
            $table->string('direction', 3);
            $table->string('process_type', 20)->nullable();
            $table->string('autogate_hold', 1)->default('N');
            $table->string('autogate_doc_type', 50)->nullable();
            $table->string('permit_type', 3)->nullable();
            $table->string('npct1_code', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('direction');
            $table->index('process_type');
            $table->index('npct1_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};