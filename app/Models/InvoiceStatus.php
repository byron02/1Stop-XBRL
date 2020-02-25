<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class InvoiceStatus
 * 
 * @property int $id
 * @property string $invoice_number
 * @property int $company_id
 * @property \Carbon\Carbon $date_created
 * @property \Carbon\Carbon $date_paid
 * @property int $status
 * @property string $invoice
 *
 * @package App\Models
 */
class InvoiceStatus extends Eloquent
{
	protected $table = 'invoice_status';
	public $timestamps = false;

	protected $casts = [
		'company_id' => 'int',
		'status' => 'int'
	];

	protected $dates = [
		'date_created',
		'date_paid'
	];

	protected $fillable = [
		'invoice_number',
		'company_id',
		'date_created',
		'date_paid',
		'status',
		'invoice'
	];
}
