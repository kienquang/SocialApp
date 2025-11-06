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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->string('type', 50);
            $table->text('data');
            $table->timestamp('created_at')->useCurrent();
=======
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type', 50);
            $table->text('data');
            $table->boolean('is_read')->default(0);
            $table->timestamps();
>>>>>>> origin/kienBranch
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
