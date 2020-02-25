<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('job_comments', function(Blueprint $table)
		{
			$table->integer('job_id');
			$table->text('comment', 65535);
			$table->string('action');
			$table->dateTime('date_added');
			$table->integer('tags');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('job_comments');
	}

}
