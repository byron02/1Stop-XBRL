<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientsDepartmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clients_departments', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('company_id');
			$table->integer('client_admin_id');
			$table->string('department_name');
			$table->string('contact_first_name');
			$table->string('contact_last_name');
			$table->integer('main_phone_no');
			$table->string('email_address');
			$table->date('year_end');
			$table->dateTime('date_added');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('clients_departments');
	}

}
