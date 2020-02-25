<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class VendorsPageAssigned
 * 
 * @property int $id
 * @property int $user_id
 * @property string $page_range
 * @property bool $is_active
 *
 * @package App\Models
 */
class VendorsPageAssigned extends Eloquent
{
	protected $table = 'vendors_page_assigned';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'user_id',
		'page_range',
		'is_active'
	];
}
