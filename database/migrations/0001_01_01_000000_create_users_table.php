<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reff_user', function (Blueprint $table) {
            /*
             * Primary key.
             *
             * Gunakan unsignedBigInteger agar kompatibel dengan standar
             * primary key Laravel modern.
             */
            $table->unsignedBigInteger('ID')->autoIncrement();

            /*
             * Informasi akun.
             */
            $table->string('USER_NAME', 100);
            $table->string('PASS', 255);
            $table->string('NAMA', 150);
            $table->string('NOTELP', 30)->nullable();
            $table->string('EMAIL', 150)->nullable();

            /*
             * Referensi organisasi/lokasi.
             *
             * Dibuat string karena kode biasanya dapat mengandung angka,
             * huruf, leading zero, atau karakter khusus.
             */
            $table->string('KD_GA', 50)->nullable();
            $table->string('KD_TPS', 50)->nullable();
            $table->string('KD_GUDANG', 50)->nullable();
            $table->string('KD_GROUP', 50)->nullable();

            /*
             * Status dan otorisasi.
             */
            $table->string('STATUS', 20)
                ->default('ACTIVE')
                ->comment('Contoh: ACTIVE, INACTIVE, BLOCKED');

            $table->string('ROLE', 50)->nullable();

            /*
             * Informasi aktivitas.
             */
            $table->dateTime('LAST_LOGIN')->nullable();
            $table->timestamp('WK_REKAM')->useCurrent();

            /*
             * Informasi tambahan.
             *
             * NPWP disimpan sebagai string karena bukan nilai numerik
             * untuk operasi matematika dan dapat memiliki leading zero.
             */
            $table->string('NPWP', 30)->nullable();

            /*
             * Password sistem BOS baru.
             * Panjang 255 cukup untuk hash bcrypt maupun Argon2.
             */
            $table->string('PASS_BOSBARU', 255)->nullable();

            /*
             * Index dan constraint.
             */
            $table->unique('USER_NAME', 'uk_reff_user_username');
            $table->unique('EMAIL', 'uk_reff_user_email');

            $table->index('KD_GA', 'idx_reff_user_kd_ga');
            $table->index('KD_TPS', 'idx_reff_user_kd_tps');
            $table->index('KD_GUDANG', 'idx_reff_user_kd_gudang');
            $table->index('KD_GROUP', 'idx_reff_user_kd_group');
            $table->index('STATUS', 'idx_reff_user_status');
            $table->index('ROLE', 'idx_reff_user_role');
            $table->index('LAST_LOGIN', 'idx_reff_user_last_login');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reff_user');
        Schema::dropIfExists('sessions');
    }
};
