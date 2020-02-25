<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 17 Jan 2018 09:37:37 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class TaxAuthority
 * 
 * @property int $id
 * @property string $description
 *
 * @package App\Models
 */
class TaxAuthority extends Eloquent
{
	protected $table = 'tax_authority';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'id',
		'description'
	];
}
