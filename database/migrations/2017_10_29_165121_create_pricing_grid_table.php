<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePricingGridTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pricing_grid', function(Blueprint $table)
		{
			$table->increments('idpricing_grid');
			$table->integer('floor_page_count')->unsigned();
			$table->integer('ceiling_page_count')->unsigned();
			$table->float('price', 10, 0);
			$table->integer('turnaround_time')->unsigned();
			$table->integer('work_type')->unsigned();
			$table->integer('taxonomy_group');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pricing_grid');
	}

}
