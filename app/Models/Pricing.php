<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 18 Jan 2018 07:01:17 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Pricing
 * 
 * @property int $id
 * @property int $pages
 * @property float $price
 * @property int $type
 * @property \Carbon\Carbon $created_at
 *
 * @package App\Models
 */
class Pricing extends Eloquent
{
	protected $table = 'pricing';
	public $timestamps = false;

	protected $casts = [
		'pages' => 'int',
		'price' => 'float',
		'type' => 'int'
	];

	protected $fillable = [
		'pages',
		'price',
		'type'
	];
}
