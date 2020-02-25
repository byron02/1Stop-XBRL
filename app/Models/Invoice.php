<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Invoice
 * 
 * @property int $id
 * @property string $invoice_number
 * @property int $job_id
 * @property int $quantity
 * @property float $rate
 * @property float $total
 * @property int $is_imported_to_xero
 * @property \Carbon\Carbon $date_imported
 *
 *
 * @package App\Models
 */
class Invoice extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'job_id' => 'int',
		'quantity' => 'int',
		'rate' => 'float',
		'total' => 'float',
		'is_imported_to_xero' => 'int'
	];

	protected $dates = [
		'date_imported'
	];

	protected $fillable = [
		'invoice_number',
		'job_id',
		'quantity',
		'rate',
		'total',
		'is_imported_to_xero',
		'date_imported'
	];
}
