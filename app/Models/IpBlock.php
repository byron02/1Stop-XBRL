<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class IpBlock
 * 
 * @property int $id
 * @property string $ip_address
 * @property int $is_active
 *
 * @package App\Models
 */
class IpBlock extends Eloquent
{
	protected $table = 'ip_block';
	public $timestamps = false;

	protected $casts = [
		'is_active' => 'int'
	];

	protected $fillable = [
		'ip_address',
		'is_active'
	];
}
