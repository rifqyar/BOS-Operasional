<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spks', function (Blueprint $table) {
            $table->string('document_type_id', 10)->nullable();

            $table->foreign('document_type_id')
                ->references('id')
                ->on('document_types');

            $table->index('document_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('spks', function (Blueprint $table) {
            $table->dropForeign(['document_type_id']);
            $table->dropIndex(['document_type_id']);
            $table->dropColumn('document_type_id');
        });
    }
};