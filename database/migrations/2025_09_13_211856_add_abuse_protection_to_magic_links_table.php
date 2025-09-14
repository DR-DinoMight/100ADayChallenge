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
        Schema::table('magic_links', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->after('token');
            $table->string('user_agent')->nullable()->after('ip_address');
            $table->timestamp('last_attempt_at')->nullable()->after('user_agent');
            $table->integer('attempt_count')->default(1)->after('last_attempt_at');
            $table->boolean('blocked')->default(false)->after('attempt_count');
            $table->timestamp('blocked_until')->nullable()->after('blocked');
            $table->text('block_reason')->nullable()->after('blocked_until');

            $table->index(['email', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['blocked', 'blocked_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magic_links', function (Blueprint $table) {
            $table->dropIndex(['email', 'created_at']);
            $table->dropIndex(['ip_address', 'created_at']);
            $table->dropIndex(['blocked', 'blocked_until']);

            $table->dropColumn([
                'ip_address',
                'user_agent',
                'last_attempt_at',
                'attempt_count',
                'blocked',
                'blocked_until',
                'block_reason',
            ]);
        });
    }
};
