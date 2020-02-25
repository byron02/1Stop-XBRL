<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 18 Jan 2018 06:12:57 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Job
 * 
 * @property int $id
 * @property int $company
 * @property int $department
 * @property string $project_name
 * @property string $purchase_order
 * @property int $order_by
 * @property int $user_id
 * @property int $work_type
 * @property int $turnaround
 * @property \Carbon\Carbon $due_date
 * @property int $action
 * @property int $output
 * @property float $computed_price
 * @property float $tax_computation_price
 * @property float $quoted_price
 * @property int $total_pages_submitted
 * @property string $companies_house_registration_no
 * @property int $taxonomy
 * @property int $tagging_level
 * @property int $entity_dormant
 * @property \Carbon\Carbon $year_end
 * @property \Carbon\Carbon $date_of_director_report
 * @property \Carbon\Carbon $date_of_auditor_report
 * @property \Carbon\Carbon $approval_of_accounts_date
 * @property string $name_of_director_approving_accounts
 * @property string $name_of_director_signing
 * @property int $live_test_service
 * @property int $status
 * @property int $vendor_id
 * @property \Carbon\Carbon $date_added
 * @property float $adjust_price
 * @property bool $is_paid
 * @property \Carbon\Carbon $transaction_date
 * @property string $utr_number
 * @property string $ixbrl_tag_file
 * @property int $has_sent_payment_notif
 * @property int $has_sent_duedate_notif_day_before
 * @property int $has_sent_duedate_notif_now
 * @property bool $is_invoiced
 * @property float $original_price
 * @property float $tax_computation_origianl_price
 * @property \Carbon\Carbon $last_reminder_sent_due_date
 * @property \Carbon\Carbon $last_reminder_sent_payment
 * @property int $xbrl_file
 * @property int $tax_authority_id
 * @property string $tax_reference
 * @property int $invoice
 *
 * @package App\Models
 */
class Job extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'company' => 'int',
		'department' => 'int',
		'order_by' => 'int',
		'user_id' => 'int',
		'work_type' => 'int',
		'turnaround' => 'int',
		'action' => 'int',
		'output' => 'int',
		'computed_price' => 'float',
		'tax_computation_price' => 'float',
		'quoted_price' => 'float',
		'total_pages_submitted' => 'int',
		'taxonomy' => 'int',
		'tagging_level' => 'int',
		'entity_dormant' => 'int',
		'live_test_service' => 'int',
		'status' => 'int',
		'vendor_id' => 'int',
		'adjust_price' => 'float',
		'is_paid' => 'bool',
		'has_sent_payment_notif' => 'int',
		'has_sent_duedate_notif_day_before' => 'int',
		'has_sent_duedate_notif_now' => 'int',
		'is_invoiced' => 'bool',
		'original_price' => 'float',
		'tax_computation_origianl_price' => 'float',
		'xbrl_file' => 'int',
		'tax_authority_id' => 'int',
		'invoice' => 'int'
	];

	protected $dates = [
		'due_date',
		'year_end',
		'date_of_director_report',
		'date_of_auditor_report',
		'approval_of_accounts_date',
		'date_added',
		'transaction_date',
		'last_reminder_sent_due_date',
		'last_reminder_sent_payment'
	];

	protected $fillable = [
		'company',
		'department',
		'project_name',
		'purchase_order',
		'order_by',
		'user_id',
		'work_type',
		'turnaround',
		'due_date',
		'action',
		'output',
		'computed_price',
		'tax_computation_price',
		'quoted_price',
		'total_pages_submitted',
		'companies_house_registration_no',
		'taxonomy',
		'tagging_level',
		'entity_dormant',
		'year_end',
		'date_of_director_report',
		'date_of_auditor_report',
		'approval_of_accounts_date',
		'name_of_director_approving_accounts',
		'name_of_director_signing',
		'live_test_service',
		'status',
		'vendor_id',
		'date_added',
		'adjust_price',
		'is_paid',
		'transaction_date',
		'utr_number',
		'ixbrl_tag_file',
		'has_sent_payment_notif',
		'has_sent_duedate_notif_day_before',
		'has_sent_duedate_notif_now',
		'is_invoiced',
		'original_price',
		'tax_computation_origianl_price',
		'last_reminder_sent_due_date',
		'last_reminder_sent_payment',
		'xbrl_file',
		'tax_authority_id',
		'tax_reference',
		'invoice'
	];
}
