<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
   
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('city_id');
            $table->integer('category_id');
            $table->string('name');
            $table->string('address');
            $table->string('latitude');
            $table->string('langitude');
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
