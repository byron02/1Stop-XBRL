<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class TaggingLevel
 * 
 * @property int $id
 * @property string $name
 *
 * @package App\Models
 */
class TaggingLevel extends Eloquent
{
	public $timestamps = false;

	protected $fillable = [
		'name'
	];
}
