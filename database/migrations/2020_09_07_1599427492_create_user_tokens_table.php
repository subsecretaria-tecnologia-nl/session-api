<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTokensTable extends Migration
{
    public function up()
    {
        Schema::create('user_tokens', function (Blueprint $table) {
					$table->increments("id");
					$table->integer('user_id');
					$table->string('token',250);
					$table->integer('token_type_id');
					$table->integer('created_by');
					$table->timestamp('valid_until')->nullable();
					$table->timestamp('closed_at')->nullable();
					$table->timestamp('created_at');
					$table->timestamp('updated_at')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('user_tokens');
    }
}