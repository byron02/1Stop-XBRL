<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CancelledJob
 * 
 * @property int $id
 * @property int $job_id
 * @property string $reason
 * @property int $cancelled_by
 * @property \Carbon\Carbon $date_cancelled
 *
 * @package App\Models
 */
class CancelledJob extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'job_id' => 'int',
		'cancelled_by' => 'int'
	];

	protected $dates = [
		'date_cancelled'
	];

	protected $fillable = [
		'job_id',
		'reason',
		'cancelled_by',
		'date_cancelled'
	];
}
