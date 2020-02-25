<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 29 Oct 2017 17:02:36 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class EmailHistory
 * 
 * @property int $id
 * @property string $type
 * @property \Carbon\Carbon $date_sent
 * @property string $email_recipient
 * @property string $email_cc
 * @property string $email_attachments
 *
 * @package App\Models
 */
class EmailHistory extends Eloquent
{
	protected $table = 'email_history';
	public $timestamps = false;

	protected $dates = [
		'date_sent'
	];

	protected $fillable = [
		'type',
		'date_sent',
		'email_recipient',
		'email_cc',
		'email_attachments'
	];
}
