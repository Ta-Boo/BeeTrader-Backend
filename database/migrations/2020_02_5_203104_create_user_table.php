<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('address_id');
            $table->unsignedInteger('state_id');

            $table->string('first_name',30);
            $table->string('last_name',30);
            $table->integer('is_admin');
            $table->string('email',45)->unique();
            $table->string('phone_number',15)->nullable();
            $table->string('image')->nullable();
            $table->date('registered_at');
            $table->string('password',255);

            $table->foreign('address_id')
                ->references('id')
                ->on('address')
                ->onDelete('cascade');
            $table->foreign('state_id')
                ->references('id')
                ->on('user_state')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
