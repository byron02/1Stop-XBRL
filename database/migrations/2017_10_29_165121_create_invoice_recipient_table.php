<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceRecipientTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoice_recipient', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('company_id');
			$table->string('company_name', 75);
			$table->string('fullname');
			$table->text('address_line_1', 65535);
			$table->text('address_line_2', 65535);
			$table->text('address_line_3', 65535);
			$table->string('city', 75);
			$table->integer('country');
			$table->string('post_code', 25);
			$table->string('telephone_number', 25);
			$table->string('mobile_number', 25);
			$table->string('job_title');
			$table->string('email_address');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('invoice_recipient');
	}

}
