<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Inquiry
 * 
 * @property int $inquiry_id
 * @property string $fname
 * @property string $lname
 * @property string $title
 * @property string $email
 * @property string $phone
 * @property string $country
 * @property string $comment
 * @property int $status
 * @property bool $delete_flg
 * @property \Carbon\Carbon $date_submitted
 *
 * @package App\Models
 */
class Inquiry extends Eloquent
{
	protected $primaryKey = 'inquiry_id';
	public $timestamps = false;

	protected $casts = [
		'status' => 'int',
		'delete_flg' => 'bool'
	];

	protected $dates = [
		'date_submitted'
	];

	protected $fillable = [
		'fname',
		'lname',
		'title',
		'email',
		'phone',
		'country',
		'comment',
		'status',
		'delete_flg',
		'date_submitted'
	];
}
