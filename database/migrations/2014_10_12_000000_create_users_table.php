<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Tương đương bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY
            $table->string('name', 100); // Đã đổi từ username
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->string('role');
            $table->string('google_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('avatar', 500)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
