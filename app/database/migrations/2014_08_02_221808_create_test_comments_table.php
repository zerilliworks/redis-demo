<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('test_comments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->integer('status_id')->unsigned();

            $table->longText('body');

            $table->softDeletes();
			$table->timestamps();

            $table->foreign('user_id')->references('id')->on('test_users')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('test_statuses')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('test_comments');
	}

}
