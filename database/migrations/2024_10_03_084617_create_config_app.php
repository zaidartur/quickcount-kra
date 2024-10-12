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
        Schema::create('config_app', function (Blueprint $table) {
            $table->id();
            $table->string('app_name');
            $table->integer('app_tahun');
            $table->string('app_logo')->nullable();
            $table->string('app_address')->nullable();
            $table->string('app_pemilik', '100')->nullable();
            $table->string('app_website', '100')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_app');
    }
};
