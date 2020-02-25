<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AutomationConfig
 * 
 * @property string $key
 * @property bool $is_active
 * @property string $last_update_date
 * @property bool $is_deleted
 *
 * @package App\Models
 */
class AutomationConfig extends Eloquent
{
	protected $table = 'automation_config';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'is_active' => 'bool',
		'is_deleted' => 'bool'
	];

	protected $fillable = [
		'key',
		'is_active',
		'last_update_date',
		'is_deleted'
	];
}
