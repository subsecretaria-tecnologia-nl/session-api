<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformationUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('information_users', function (Blueprint $table) {
						$table->id();
						$table->integer('user_id');	
						$table->dateTime('action_date');
						$table->string('token_type');
						$table->string('description');
						$table->string('modified_variables');
						$table->string('device_type');
						$table->string('browser_type');
						$table->tinyInteger('activo')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('information_users');
    }
}
