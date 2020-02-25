<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PricingGrid
 * 
 * @property int $idpricing_grid
 * @property int $floor_page_count
 * @property int $ceiling_page_count
 * @property float $price
 * @property int $turnaround_time
 * @property int $work_type
 * @property int $taxonomy_group
 *
 * @package App\Models
 */
class PricingGrid extends Eloquent
{
	protected $table = 'pricing_grid';
	protected $primaryKey = 'idpricing_grid';
	public $timestamps = false;

	protected $casts = [
		'floor_page_count' => 'int',
		'ceiling_page_count' => 'int',
		'price' => 'float',
		'turnaround_time' => 'int',
		'work_type' => 'int',
		'taxonomy_group' => 'int'
	];

	protected $fillable = [
		'floor_page_count',
		'ceiling_page_count',
		'price',
		'turnaround_time',
		'work_type',
		'taxonomy_group'
	];
}
