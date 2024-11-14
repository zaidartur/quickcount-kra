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
        Schema::create('data_voting', function (Blueprint $table) {
            $table->id();
            $table->string('uuid_vote', '40')->unique();
            $table->integer('dpt_id')->unique();
            $table->string('kec_id', '10');
            $table->string('desakel_id');
            $table->string('desakel_name')->nullable();
            $table->integer('no_tps');
            $table->text('vote_sah');
            $table->string('vote_tidaksah');
            $table->integer('tahun_vote');
            $table->integer('total_vote')->default(0);
            $table->string('user', '40');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_voting');
    }
};
