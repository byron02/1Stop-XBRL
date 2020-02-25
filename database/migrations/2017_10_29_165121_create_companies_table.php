<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('companies', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name');
			$table->string('address1');
			$table->string('address2');
			$table->string('address3');
			$table->string('city');
			$table->string('region');
			$table->string('religion');
			$table->string('postcode', 16);
			$table->string('fax', 64);
			$table->integer('country');
			$table->string('phone', 64);
			$table->string('email');
			$table->boolean('payment_method')->default(1);
			$table->boolean('timezone');
			$table->boolean('active')->default(0);
			$table->dateTime('date_added');
			$table->integer('discount_rate')->unsigned()->default(0);
			$table->boolean('receive_email_notif')->default(1);
			$table->boolean('autosend_invoice')->default(1);
			$table->boolean('adjustment_type')->comment('1-increase , 0- discount');
			$table->integer('default_vendor');
			$table->string('pricing_reference', 8);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('companies');
	}

}
