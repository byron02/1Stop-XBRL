<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class JobComment
 * 
 * @property int $job_id
 * @property string $comment
 * @property string $action
 * @property \Carbon\Carbon $date_added
 * @property int $tags
 *
 * @package App\Models
 */
class JobComment extends Eloquent
{
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'job_id' => 'int',
		'tags' => 'int'
	];

	protected $dates = [
		'date_added'
	];

	protected $fillable = [
		'job_id',
		'comment',
		'action',
		'date_added',
		'tags'
	];
}
