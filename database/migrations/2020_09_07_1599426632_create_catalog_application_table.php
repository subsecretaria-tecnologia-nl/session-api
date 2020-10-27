<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogApplicationTable extends Migration
{
    public function up()
    {
        Schema::create('catalog_application', function (Blueprint $table) {
					$table->increments("id");
					$table->string('name',45);
					$table->string('description',150);
					$table->string('url',150);
					$table->integer('access_type_id');

        });
    }

    public function down()
    {
        Schema::dropIfExists('catalog_application');
    }
}