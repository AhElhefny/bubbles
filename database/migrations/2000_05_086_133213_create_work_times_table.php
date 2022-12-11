<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkTimesTable extends Migration
{
   
    public function up()
    {
        
        Schema::create('work_times', function (Blueprint $table) {

            $table->id();
            $table->string("start_time");
            $table->string("end_time");
            $table->integer('user_id');
            $table->timestamps();

        });
    }

 
    public function down()
    {
         Schema::dropIfExists('work_times');
    }
}
