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
        Schema::create('request__cutis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->nullable();
            $table->foreignId('approved_by_id')->nullable();
            $table->foreignId('notification_id');
            $table->date('start');
            $table->date('end');
            $table->boolean('is_approved')->nullable();
            $table->string('type');
            $table->text('desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request__cutis');
    }
};
