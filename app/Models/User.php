<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * 
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $job_title
 * @property int $company_id
 * @property string $address_line_1
 * @property string $address_line_2
 * @property string $address_line_3
 * @property string $city
 * @property int $country
 * @property string $post_code
 * @property string $telephone_number
 * @property string $mobile_number
 * @property string $email
 * @property int $payment_method
 * @property int $timezone
 * @property \Carbon\Carbon $last_login
 * @property string $last_login_ip
 * @property int $status
 * @property int $role_id
 * @property string $ip_address
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use Notifiable;

	public $timestamps = false;

	protected $casts = [
		'company_id' => 'int',
		'country' => 'int',
		'payment_method' => 'int',
		'timezone' => 'int',
		'status' => 'int',
		'role_id' => 'int'
	];

	protected $dates = [
		'last_login'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'username',
		'password',
		'first_name',
		'last_name',
		'job_title',
		'company_id',
		'address_line_1',
		'address_line_2',
		'address_line_3',
		'city',
		'country',
		'post_code',
		'telephone_number',
		'mobile_number',
		'email',
		'payment_method',
		'timezone',
		'last_login',
		'last_login_ip',
		'status',
		'role_id',
		'ip_address'
	];
}
