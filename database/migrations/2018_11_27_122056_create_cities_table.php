<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
            $table->string('name2');
            $table->text('name3');
            $table->double('latitude');
            $table->double('longitude');
            $table->string('country');
            $table->integer('num1')->nullable();
            $table->integer('num2')->nullable();
            $table->integer('num3')->nullable();
            $table->string('zone');
            $table->timestamp('date');
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
        Schema::dropIfExists('cities');
    }
}
