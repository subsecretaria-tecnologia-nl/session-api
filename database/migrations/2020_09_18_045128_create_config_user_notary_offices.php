<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigUserNotaryOffices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_user_notary_offices', function (Blueprint $table) {
            $table->integer("user_id");
            $table->integer("notary_office_id");
            $table->unique("user_id");
            // $table->foreign("user_id")->references("id")->on("users");
            // $table->foreign("notary_office_id")->references("id")->on("notary_offices");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config_user_notary_offices');
    }
}
