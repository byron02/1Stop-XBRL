<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 18 Jan 2018 02:35:23 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Company
 * 
 * @property int $id
 * @property string $name
 * @property string $address1
 * @property string $address2
 * @property string $address3
 * @property string $city
 * @property string $region
 * @property string $religion
 * @property string $postcode
 * @property string $fax
 * @property int $country
 * @property string $phone
 * @property string $email
 * @property int $payment_method
 * @property int $timezone
 * @property int $active
 * @property \Carbon\Carbon $date_added
 * @property int $discount_rate
 * @property int $receive_email_notif
 * @property bool $autosend_invoice
 * @property bool $adjustment_type
 * @property int $default_vendor
 * @property string $pricing_reference
 * @property int $default_tax_authority_id
 * @property int $pricing_grid
 * @property string $default_tax_reference
 * @property bool $assign_invoice_to_project_name
 *
 * @package App\Models
 */
class Company extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'country' => 'int',
		'payment_method' => 'int',
		'timezone' => 'int',
		'active' => 'int',
		'discount_rate' => 'int',
		'receive_email_notif' => 'int',
		'autosend_invoice' => 'bool',
		'adjustment_type' => 'bool',
		'default_vendor' => 'int',
		'default_tax_authority_id' => 'int',
		'pricing_grid' => 'int',
		'assign_invoice_to_project_name' => 'bool'
	];

	protected $dates = [
		'date_added'
	];

	protected $fillable = [
		'name',
		'address1',
		'address2',
		'address3',
		'city',
		'region',
		'religion',
		'postcode',
		'fax',
		'country',
		'phone',
		'email',
		'payment_method',
		'timezone',
		'active',
		'date_added',
		'discount_rate',
		'receive_email_notif',
		'autosend_invoice',
		'adjustment_type',
		'default_vendor',
		'pricing_reference',
		'default_tax_authority_id',
		'pricing_grid',
		'default_tax_reference',
		'assign_invoice_to_project_name'
	];



}
