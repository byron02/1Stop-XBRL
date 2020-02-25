<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobsSourceFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jobs_source_files', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('job_id');
			$table->string('file_name');
			$table->string('server_filename');
			$table->integer('page_count');
			$table->dateTime('date_uploaded');
			$table->integer('uploaded_by');
			$table->integer('type')->comment('1=input,2=output,3=completed,4=revisions');
			$table->boolean('is_removed')->default(0);
			$table->boolean('tax_computed')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('jobs_source_files');
	}

}
