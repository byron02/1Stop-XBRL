<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Turnaround
 * 
 * @property int $id
 * @property string $name
 * @property int $number_of_days
 * @property bool $is_active
 *
 * @package App\Models
 */
class Turnaround extends Eloquent
{
	protected $table = 'turnaround';
	public $timestamps = false;

	protected $casts = [
		'number_of_days' => 'int',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'name',
		'number_of_days',
		'is_active'
	];
}
