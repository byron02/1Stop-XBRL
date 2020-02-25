<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class TaxonomyGroup
 * 
 * @property int $id
 * @property string $group
 *
 * @package App\Models
 */
class TaxonomyGroup extends Eloquent
{
	protected $table = 'taxonomy_group';
	public $timestamps = false;

	protected $fillable = [
		'group'
	];
}
