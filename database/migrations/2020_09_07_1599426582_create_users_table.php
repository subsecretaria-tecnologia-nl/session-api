<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

					$table->increments(id);
					$table->string('username',45);
					$table->string('email',150);
					$table->string('password',250);
					$table->integer('role_id',11);
					$table->string('name',45);
					$table->string('mothers_surname',45);
					$table->string('fathers_surname',45);
					$table->string('curp',20);
					$table->string('rfc',15);
					$table->string('phone',15);
					$table->integer('status')->default(1);
					$table->integer('created_by',11)->nullable()->default('NULL');
					$table->timestamp('created_at')->default('CURRENT_TIMESTAMP');
					$table->timestamp('updated_at')->nullable();
					$table->timestamp('deleted_at')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}