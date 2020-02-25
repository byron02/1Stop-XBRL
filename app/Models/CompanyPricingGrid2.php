<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CompanyPricingGrid2
 * 
 * @property int $id
 * @property int $company_id
 * @property bool $delete_flag
 *
 * @package App\Models
 */
class CompanyPricingGrid2 extends Eloquent
{
	protected $table = 'company_pricing_grid2';
	public $timestamps = false;

	protected $casts = [
		'company_id' => 'int',
		'delete_flag' => 'bool'
	];

	protected $fillable = [
		'company_id',
		'delete_flag'
	];
}
