<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ClientsDepartment
 * 
 * @property int $id
 * @property int $company_id
 * @property int $client_admin_id
 * @property string $department_name
 * @property string $contact_first_name
 * @property string $contact_last_name
 * @property int $main_phone_no
 * @property string $email
 * @property \Carbon\Carbon $year_end
 * @property \Carbon\Carbon $date_added
 *
 * @package App\Models
 */
class ClientsDepartment extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'company_id' => 'int',
		'client_admin_id' => 'int',
		'main_phone_no' => 'int'
	];

	protected $dates = [
		'year_end',
		'date_added'
	];

	protected $fillable = [
		'company_id',
		'client_admin_id',
		'department_name',
		'contact_first_name',
		'contact_last_name',
		'main_phone_no',
		'email',
		'year_end',
		'date_added'
	];
}
