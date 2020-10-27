<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotaryOffices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notary_offices', function (Blueprint $table) {
            $table->integer("id")->autoIncrement();
            $table->string("notary_number");
            $table->integer("titular_id");
            $table->integer("substitute_id")->default(null);
            $table->text("phone");
            $table->text("fax");
            $table->text("email");
            $table->text("street");
            $table->text("number");
            $table->text("outdoor-number")->default(null);
            $table->text("district");
            $table->text("federal_entity_id");
            $table->text("city_id");
            $table->integer("comunity_id");
            $table->text("zip");
            $table->longText("sat_constancy_file");
            $table->longText("notary_constancy_file");
            $table->integer("status")->default(1);
            $table->integer("created_by")->default(null);
            $table->timestamps();
            // $table->foreign("titular_id")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notary_offices');
    }
}
