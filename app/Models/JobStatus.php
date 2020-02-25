<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class JobStatus
 * 
 * @property int $id
 * @property string $name
 *
 * @package App\Models
 */
class JobStatus extends Eloquent
{
	protected $table = 'job_status';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];
}
