<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('user_history', function (Blueprint $table) {

		$table->increments(id);
		$table->text('comment');
		$table->text('old_data');
		$table->text('new_data');
		$table->integer('created_by',);
		$table->timestamp('created_at')->default('CURRENT_TIMESTAMP');

        });
    }

    public function down()
    {
        Schema::dropIfExists('user_history');
    }
}