<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyesuaikan skema dengan PRD:
 * - user_id nullable agar fitur bisa dipakai tanpa akun (mode tamu).
 * - guest_token menandai catatan milik tamu (diambil dari cookie anonim).
 * - mood_logs.logged_at menyimpan waktu pencatatan (bisa disesuaikan manual).
 * - tabel breathing_logs untuk riwayat latihan napas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mood_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('guest_token', 64)->nullable()->index()->after('user_id');
            $table->timestamp('logged_at')->nullable()->after('logged_date');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('guest_token', 64)->nullable()->index()->after('user_id');
        });

        Schema::create('breathing_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('guest_token', 64)->nullable()->index();
            $table->string('rhythm_name', 100);
            $table->unsignedSmallInteger('cycles');
            $table->string('duration', 30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breathing_logs');

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex(['guest_token']);
            $table->dropColumn('guest_token');
        });

        Schema::table('mood_logs', function (Blueprint $table) {
            $table->dropIndex(['guest_token']);
            $table->dropColumn(['guest_token', 'logged_at']);
        });
    }
};
