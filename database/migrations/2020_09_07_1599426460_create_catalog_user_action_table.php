<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogUserActionTable extends Migration
{
    public function up()
    {
        Schema::create('catalog_user_action', function (Blueprint $table) {

		$table->increments(id);
		$table->string('name',45);
		$table->string('description',150);

        });
    }

    public function down()
    {
        Schema::dropIfExists('catalog_user_action');
    }
}