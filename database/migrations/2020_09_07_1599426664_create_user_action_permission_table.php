<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActionPermissionTable extends Migration
{
    public function up()
    {
        Schema::create('user_action_permission', function (Blueprint $table) {
					$table->integer('user_id');
					$table->string('action_id',11);

        });
    }

    public function down()
    {
        Schema::dropIfExists('user_action_permission');
    }
}