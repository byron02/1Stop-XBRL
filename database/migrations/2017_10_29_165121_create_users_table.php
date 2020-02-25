<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('username', 128);
			$table->string('password', 512);
			$table->string('first_name', 64);
			$table->string('last_name', 64);
			$table->string('job_title', 128);
			$table->integer('company_id');
			$table->string('address_line_1', 256);
			$table->string('address_line_2', 256)->nullable();
			$table->string('address_line_3', 256)->nullable();
			$table->string('city', 64);
			$table->integer('country');
			$table->string('post_code', 32);
			$table->string('telephone_number', 32);
			$table->string('mobile_number', 64);
			$table->string('email', 512);
			$table->boolean('payment_method')->defaul(0); //FIXME: might need extra logic
			$table->boolean('timezone');
			$table->dateTime('last_login');
			$table->string('last_login_ip', 128);
			$table->boolean('status')->default(0);
			$table->boolean('role_id')->default(2);
			$table->string('ip_address', 20);
            $table->rememberToken();
            $table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
