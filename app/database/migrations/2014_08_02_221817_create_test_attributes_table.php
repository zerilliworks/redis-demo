<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestAttributesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('test_attributes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->morphs('entity');

            $table->string('attribute');
            $table->text('value')->nullable();

            $table->timestamps();

            $table->unique(['entity_id', 'attribute']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('test_attributes');
	}

}
