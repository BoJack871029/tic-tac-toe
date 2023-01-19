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
        Schema::create('moves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('game_id');
            $table->unsignedSmallInteger('player');
            $table->unsignedSmallInteger('row');
            $table->unsignedSmallInteger('col');
            $table->timestamps();

            $table->foreign('game_id')->references('id')->on('games')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->unique(
                [
                    'game_id', 'row', 'col'
                ],
                'unique_move_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moves');
    }
};
