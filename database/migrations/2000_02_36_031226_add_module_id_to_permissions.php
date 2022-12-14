<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModuleIdToPermissions extends Migration
{
   
    public function up()
    {
       
        Schema::table('permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('module_id');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });
    }

    public function down()
    {
        
        Schema::table('permissions', function (Blueprint $table) {
            //
        });
    }
}
