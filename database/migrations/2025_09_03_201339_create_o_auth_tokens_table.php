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
        Schema::create('o_auth_tokens', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('provider');
            $table->text('access_token');
            $table->text('refresh_token');
            $table->text('expires_at');

            $table->foreignId('user_id')->constrained()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_auth_tokens');
    }
};
