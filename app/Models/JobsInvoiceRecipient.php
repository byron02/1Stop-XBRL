<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class JobsInvoiceRecipient
 * 
 * @property int $id
 * @property int $company_id
 * @property string $company_name
 * @property string $full_name
 * @property string $address_line_1
 * @property string $address_line_2
 * @property string $address_line_3
 * @property string $city
 * @property int $country
 * @property string $post_code
 * @property string $telephone_number
 * @property string $mobile_number
 * @property string $job_title
 * @property string $email
 *
 * @package App\Models
 */
class JobsInvoiceRecipient extends Eloquent
{
	protected $table = 'jobs_invoice_recipient';
	public $timestamps = false;

	protected $casts = [
		'company_id' => 'int',
		'country' => 'int'
	];

	protected $fillable = [
		'company_id',
		'company_name',
		'full_name',
		'address_line_1',
		'address_line_2',
		'address_line_3',
		'city',
		'country',
		'post_code',
		'telephone_number',
		'mobile_number',
		'job_title',
		'email'
	];
}
