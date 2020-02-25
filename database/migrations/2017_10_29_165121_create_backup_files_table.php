<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBackupFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('backup_files', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('file_name');
			$table->date('date_created');
			$table->integer('type');
			$table->string('date_to', 20)->nullable();
			$table->string('date_from', 20)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('backup_files');
	}

}
