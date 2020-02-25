<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Timezone
 * 
 * @property int $id
 * @property string $name
 * @property string $offset
 * @property string $code
 * @property string $image
 * @property string $state
 *
 * @package App\Models
 */
class Timezone extends Eloquent
{
	public $timestamps = false;

	protected $fillable = [
		'name',
		'offset',
		'code',
		'image',
		'state'
	];
}
