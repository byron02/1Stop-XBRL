<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceStatusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoice_status', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('invoice_number', 75)->index('invoice_number');
			$table->integer('company_id')->index('company_id');
			$table->dateTime('date_created');
			$table->dateTime('date_paid');
			$table->integer('status')->comment('0=not paid,1=paid');
			$table->string('invoice');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('invoice_status');
	}

}
