@php
$invoices = $invoice[0];

$subtotal = $gross = $vat = 0;
@endphp

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>{{ $invoices->invoice_number }}</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<style>
		html,
		body {
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			font-size: 12px;
		}

		.logo-container {
			background: #1b2a30;
			padding: 8px;
		}

		.invoice-header>tbody>tr>td,
		.invoice-header>tbody>tr>th,
		.invoice-header>tfoot>tr>td,
		.invoice-header>tfoot>tr>th,
		.invoice-header>thead>tr>td,
		.invoice-header>thead>tr>th {
			border-top: 0;
			padding: 2px 8px;
		}

		.secondary {
			background: #ccc;
		}

		.text-medium {
			font-size: 10px;
		}

		.m-0 {
			margin: 0px;
		}

		p {
			margin-bottom: 2px;
		}

		hr {
			margin: 10px 0px;
		}

		.text-normal {
			font-size: 12px;
		}

		.text-important {
			font-size: 14px;
			font-weight: bold;
		}

		.footer-text {
			position: fixed;
			bottom: 0;
			font-size: 9px;
		}

		.text-right {
			text-align: right;
		}

		.text-left {
			text-align: right;
		}

		.float-left {
			float: left;
		}

		.float-right {
			float: right;
		}
	</style>
</head>

<body>
	<div class="container">
		<div class="row">
			<div style="width:50%;float:left">
				<img src="http://servicetrack.1stopxbrl.co.uk/public/img/xbrl_logo_2.png">
			</div>
			<div style="width:50%;float:left;text-align:right">
				<h1>TAX INVOICE</h1>
			</div>
		</div>
		<hr />
		<div class="row">
			<div class="col-lg-12">
				<div style="width:80%;float:left;position:relative;font-size:12px;">
					<p>
						<strong>Invoice Date</strong> : {{ date('d M Y', strtotime($invoices->date_imported))}}
					</p>
					<p>
						<strong>Account Number</strong> : {{ $invoices->id }}
					</p>
					<p>
						<strong>Invoice Number</strong> : {{ $invoices->invoice_number }}
					</p>
					<p>
						<strong>VAT Number</strong> :
					</p>
					<p>{{ $invoices->assign_invoice_to_project_name == 1 ? $invoices->project_name : $invoices->company_name }}</p>
					<p>{{ $invoices->address1 }}</p>
					<p>{{ $invoices->city }}</p>
					{{-- <p>{{ $invoices->address_line_3 }}</p> --}}
					<p>{{ $invoices->country }}</p>
					<p>{{ $invoices->postcode }}</p>
				</div>
				<div style="width:20%;float:left;position:relative;font-size:10px;text-align:right;">
					<p>1STOPXBRL</p>
					<p>1STOPXBRL LIMITED</p>
					<p>601 International House</p>
					<p>223 Regent Street</p>
					<p>London</p>
					<p>W1B 2QD</p>
					<p>UNITED KINGDOM</p>

				</div>
				<div class="clearfix"></div>
				<table class="table table-condensed table-bordered">
					<thead>
						<tr>
							<th colspan="3">Description of Service Supplied</th>
							<th class="text-center">Pages</th>
							<th class="text-center">Type</th>
							<th class="text-center">Client PO</th>
							<th class="text-center">Sub Total</th>
							<th class="text-center">Total(Inc. VAT)</th>
						</tr>
					</thead>
					<tbody>
						@foreach($job as $job)
						<tr>
							<td colspan="3">{{$job['project_name']}}</td>
							<td class="text-center">{{ $job['page_count'] }} page(s) ({{ $job['number_of_days']}} days)</td>
							<td class="text-center">{{ $job['type'] }}</td>
							<td class="text-center">{{ $job['purchase_order'] }}</td>
							<td class="text-center">{{$job['price']}}</td>
							<td class="text-right">{{$job['total_gbp']}}</td>
						</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr class="secondary">
							<td colspan="8"></td>
						</tr>
						<tr>
							<th colspan="6"></th>
							<th class="text-right"><small>Subtotal</small></th>
							<td class="text-right">{{ number_format($summary['subtotal'],2) }}</td>
						</tr>
						<tr>
							<th colspan="6"></th>
							<th class="text-right"><small>Total VAT 20%</small></th>
							<td class="text-right">{{ number_format($summary['vat'],2) }}</td>
						</tr>
						<tr>
							<th colspan="6"></th>
							<th class="text-right">Total GBP</th>
							<td class="text-right">{{ number_format(($summary['gross']),2) }}</td>
						</tr>
						<!-- <tr>
								<th colspan="5"></th>
								<td class="text-right">Less Amount Paid</td>
								<td class="text-right"></td>
							</tr>
							<tr>
								<th colspan="5"></th>
								<th class="text-right">AMOUNT DUE</th>
								<td class="text-right"></td>
							</tr> -->
					</tfoot>
				</table>
				<div class="clearfix"></div>

				<div class="text-normal">
					<p class="text-important">Due Date: {{ date('d M Y',strtotime($invoices->due_date)) }}</p>
					<p>Bank: Metro Bank</p>
					<p>Branch: One Southampton Row, London WC1B 5HA</p>
					<p>Acct Name: 1STOPXBRL LIMITED</p>
					<p>Sort Code: 23-05-80</p>
					<p>Account Number: 11039758</p>
					<p>IBAN: GB56MYMB23058011039758</p>
					<p>Swift: MYMBGB2L</p>
				</div>
				<div class="float-left text-normal">
					<br /><br />
					<p>Specialists in iXBRL tagging and E-Filing. </p>
					<p>Recognised by HMRC and Companies House.</p>
					<p>HMRC Vendor ID1698 - HMRC eFiling</p>
				</div>
				<div class="float-right text-normal">
					<br /><br />
					<p>Registered in England & Wales No. 8072489</p>
					<p>Vat Number 136 3220 45</p>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="footer-text">
			Company Registration No: 8072489. Registered Office: Attention: 1STOPXBRL LIMITED, 601 International House, 223 Regent Street, London, London, W1B 2QD, United Kingdom.
		</div>
	</div>
</body>

</html>