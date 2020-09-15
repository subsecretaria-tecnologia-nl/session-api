<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTokenSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('user_token_sessions', function (Blueprint $table) {
						$table->increments('id');
						$table->integer('token_id',);
						$table->integer('quantity',);
						$table->timestamp('created_at')->default('CURRENT_TIMESTAMP');
						$table->timestamp('updated_at')->nullable()->default('CURRENT_TIMESTAMP');

        });
    }

    public function down()
    {
        Schema::dropIfExists('user_token_sessions');
    }
}