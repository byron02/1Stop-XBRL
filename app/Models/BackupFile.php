<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BackupFile
 * 
 * @property int $id
 * @property string $file_name
 * @property \Carbon\Carbon $date_created
 * @property int $type
 * @property string $date_to
 * @property string $date_from
 *
 * @package App\Models
 */
class BackupFile extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'type' => 'int'
	];

	protected $dates = [
		'date_created'
	];

	protected $fillable = [
		'file_name',
		'date_created',
		'type',
		'date_to',
		'date_from'
	];
}
