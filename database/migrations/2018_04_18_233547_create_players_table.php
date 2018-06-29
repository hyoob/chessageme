<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->increments('id');
            $table->string('userId', 70);
            $table->string('username', 70);
            $table->string('title', 50)->nullable();
            $table->boolean('online')->nullable();
            $table->string('playing', 70)->nullable();
            $table->boolean('streaming')->nullable();
            $table->bigInteger('createdAt');
            $table->bigInteger('seenAt');
            $table->boolean('patron')->nullable();
            $table->boolean('disabled')->nullable();
            $table->boolean('engine')->nullable();
            $table->unsignedInteger('playTime')->nullable();
            $table->integer('countAll')->nullable();
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
        Schema::dropIfExists('players');
    }
}
