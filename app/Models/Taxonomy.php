<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Taxonomy
 * 
 * @property int $id
 * @property string $name
 * @property int $group
 *
 * @package App\Models
 */
class Taxonomy extends Eloquent
{
	protected $table = 'taxonomy';
	public $timestamps = false;

	protected $casts = [
		'group' => 'int'
	];

	protected $fillable = [
		'name',
		'group'
	];
}
