<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
						$table->id();
            $table->integer('user_id');						
						$table->text('payload');
						$table->dateTime('login_datetime');
						$table->dateTime('logout_datetime');
						$table->string('token_type');
						$table->string('session_lifetime');
						$table->string('device_type');
						$table->string('browser_type');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }
}
