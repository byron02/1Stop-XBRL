<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCancelledJobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cancelled_jobs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('job_id');
			$table->text('reason', 65535);
			$table->integer('cancelled_by');
			$table->date('date_cancelled');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cancelled_jobs');
	}

}
