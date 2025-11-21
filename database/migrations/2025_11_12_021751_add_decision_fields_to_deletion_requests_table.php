<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('deletion_requests')) return;

        Schema::table('deletion_requests', function (Blueprint $table) {
            // decided_by (FK ke users) — tambahkan hanya jika belum ada
            if (!Schema::hasColumn('deletion_requests', 'decided_by')) {
                // pilih salah satu sesuai versi Laravel
                if (method_exists($table, 'foreignId')) {
                    $table->foreignId('decided_by')
                        ->nullable()
                        ->constrained('users')
                        ->nullOnDelete()
                        ->after('status');
                } else {
                    // fallback lama
                    $table->unsignedBigInteger('decided_by')->nullable()->after('status');
                    $table->foreign('decided_by')->references('id')->on('users')->onDelete('set null');
                }
            }

            if (!Schema::hasColumn('deletion_requests', 'decided_at')) {
                $table->timestamp('decided_at')->nullable()->after('decided_by');
            }

            if (!Schema::hasColumn('deletion_requests', 'decision_note')) {
                $table->text('decision_note')->nullable()->after('decided_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('deletion_requests')) return;

        Schema::table('deletion_requests', function (Blueprint $table) {
            if (Schema::hasColumn('deletion_requests', 'decision_note')) {
                $table->dropColumn('decision_note');
            }
            if (Schema::hasColumn('deletion_requests', 'decided_at')) {
                $table->dropColumn('decided_at');
            }
            if (Schema::hasColumn('deletion_requests', 'decided_by')) {
                // untuk Laravel 9+: dropConstrainedForeignId
                if (method_exists($table, 'dropConstrainedForeignId')) {
                    $table->dropConstrainedForeignId('decided_by');
                } else {
                    $table->dropForeign(['decided_by']);
                    $table->dropColumn('decided_by');
                }
            }
        });
    }
};
