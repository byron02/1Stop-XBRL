<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 17 Jan 2018 09:59:47 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PricingGridInfo
 * 
 * @property int $id
 * @property string $name
 *
 * @package App\Models
 */
class PricingGridInfo extends Eloquent
{
	protected $table = 'pricing_grid_info';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];
}
