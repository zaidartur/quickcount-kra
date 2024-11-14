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
        Schema::create('data_dpt', function (Blueprint $table) {
            $table->id();
            $table->string('kotakab_id', '6');
            $table->string('kec_id', '10');
            $table->string('desakel_id', '10');
            $table->string('full_id', '10');
            $table->string('kpu_id', '10');
            $table->integer('no_tps');
            $table->integer('tahun_dpt');
            $table->integer('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_dpt');
    }
};
