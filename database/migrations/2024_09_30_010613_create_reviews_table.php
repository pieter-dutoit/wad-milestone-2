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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->text('text')->default('');
            $table->boolean('complete')->default(false);
            $table->boolean('unavailable')->default(false);
            $table->boolean('reported')->default(false);
            $table->unsignedBigInteger('reviewee_id')->nullable();
            $table->foreign('reviewee_id')->references('id')->on('users');
            $table->unsignedBigInteger('submission_id');
            $table->foreign('submission_id')->references('id')->on('submissions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
