<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRelationshipsTable extends Migration
{
    public function up()
    {
        Schema::create('user_relationships', function (Blueprint $table) {

		$table->integer('super_admin_id',11);
		$table->string('user_id',11);

        });
    }

    public function down()
    {
        Schema::dropIfExists('user_relationships');
    }
}