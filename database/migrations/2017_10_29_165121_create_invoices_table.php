<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoices', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('invoice_number', 75)->index('invoice_number');
			$table->integer('job_id')->index('job_id');
			$table->integer('quantity');
			$table->float('rate', 10, 0);
			$table->float('total', 10, 0);
			$table->boolean('is_imported_to_xero')->default(0);
			$table->dateTime('date_imported');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('invoices');
	}

}
