<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInquiriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inquiries', function(Blueprint $table)
		{
			$table->increments('inquiry_id');
			$table->string('fname', 155);
			$table->string('lname', 155);
			$table->string('title');
			$table->string('email');
			$table->string('phone', 15);
			$table->string('country', 45);
			$table->text('comment', 65535);
			$table->integer('status')->unsigned()->comment('for future purposes');
			$table->boolean('delete_flg')->default(0);
			$table->dateTime('date_submitted');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('inquiries');
	}

}
