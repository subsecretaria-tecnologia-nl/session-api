<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserApplicationAccessTable extends Migration
{
    public function up()
    {
        Schema::create('user_application_access', function (Blueprint $table) {

		$table->integer('user_id',11);
		$table->string('access_id',11);

        });
    }

    public function down()
    {
        Schema::dropIfExists('user_application_access');
    }
}