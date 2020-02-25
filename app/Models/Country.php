<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Country
 * 
 * @property int $id
 * @property string $name
 * @property bool $is_eu_based
 *
 * @package App\Models
 */
class Country extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'is_eu_based' => 'bool'
	];

	protected $fillable = [
		'name',
		'is_eu_based'
	];
}
