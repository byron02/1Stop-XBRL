<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jobs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('company');
			$table->integer('department');
			$table->string('project_name');
			$table->string('purchase_order');
			$table->integer('order_by');
			$table->integer('user_id');
			$table->integer('work_type');
			$table->integer('turnaround');
			$table->date('due_date');
			$table->integer('action');
			$table->integer('output');
			$table->float('computed_price', 10, 0);
			$table->float('tax_computation_price', 10, 0)->default(0);
			$table->float('quoted_price', 10, 0);
			$table->integer('total_pages_submitted');
			$table->string('companies_house_registration_no', 50);
			$table->integer('taxonomy');
			$table->integer('tagging_level');
			$table->boolean('entity_dormant');
			$table->date('year_end');
			$table->date('date_of_director_report');
			$table->date('date_of_auditor_report');
			$table->date('approval_of_accounts_date');
			$table->string('name_of_director_approving_accounts');
			$table->string('name_of_director_signing');
			$table->boolean('live_test_service');
			$table->boolean('status')->default(0)->index('status');
			$table->integer('vendor_id')->default(0);
			$table->date('date_added');
			$table->float('adjust_price', 10, 0);
			$table->boolean('is_paid')->default(0);
			$table->dateTime('transaction_date');
			$table->string('utr_number');
			$table->string('ixbrl_tag_file')->nullable()->default('0');
			$table->boolean('has_sent_payment_notif')->default(0);
			$table->boolean('has_sent_duedate_notif_day_before')->default(0);
			$table->boolean('has_sent_duedate_notif_now')->default(0);
			$table->boolean('is_invoiced')->default(0);
			$table->float('original_price', 10, 0);
			$table->float('tax_computation_origianl_price', 10, 0);
			$table->date('last_reminder_sent_due_date');
			$table->date('last_reminder_sent_payment');
			$table->integer('xbrl_file');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('jobs');
	}

}
