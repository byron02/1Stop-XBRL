<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_history', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('type');
			$table->dateTime('date_sent');
			$table->string('email_recipient');
			$table->string('email_cc')->nullable();
			$table->string('email_attachments');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('email_history');
	}

}
