<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class JobsSourceFile
 * 
 * @property int $id
 * @property int $job_id
 * @property string $file_name
 * @property string $server_filename
 * @property int $page_count
 * @property \Carbon\Carbon $date_uploaded
 * @property int $uploaded_by
 * @property int $type
 * @property bool $is_removed
 * @property int $tax_computed
 *
 * @package App\Models
 */
class JobsSourceFile extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'job_id' => 'int',
		'page_count' => 'int',
		'uploaded_by' => 'int',
		'type' => 'int',
		'is_removed' => 'bool',
		'tax_computed' => 'int'
	];

	protected $dates = [
		'date_uploaded'
	];

	protected $fillable = [
		'job_id',
		'file_name',
		'server_filename',
		'page_count',
		'date_uploaded',
		'uploaded_by',
		'type',
		'is_removed',
		'tax_computed'
	];
}
