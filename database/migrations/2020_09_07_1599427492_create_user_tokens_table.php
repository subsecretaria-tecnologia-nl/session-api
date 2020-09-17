<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTokensTable extends Migration
{
    public function up()
    {
        Schema::create('user_tokens', function (Blueprint $table) {
					$table->increments(id);
					$table->integer('user_id',11);
					$table->string('token',250);
					$table->integer('token_type_id',11);
					$table->integer('created_by',11);
					$table->timestamp('valid_until')->nullable();
					$table->timestamp('closed_at')->nullable();
					$table->timestamp('created_at')->default('CURRENT_TIMESTAMP');
					$table->timestamp('updated_at')->nullable()->default('CURRENT_TIMESTAMP');

        });
    }

    public function down()
    {
        Schema::dropIfExists('user_tokens');
    }
}