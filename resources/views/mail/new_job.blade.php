<!DOCTYPE html>
	<html>
		<head>
		</head>
		<body>
			<div style="background:#bdc3c7;font: normal normal normal 13px/1.334 Tahoma, Geneva, sans-serif;padding:30px;">
				<div style="width:600px;background:#1b2a30;margin:auto;">
					<div style="padding:16px;margin:0">
						<img src="http://servicetrack.1stopxbrl.co.uk/public/img/servicetrack-logo-hq.png" style="margin:auto;width:100px"/>
					</div>
					<div style="padding: 16px;background: #fff;">
					<p style="font-size:12px;">
						Please be informed that your job has been submitted to Service Track:
						<br/><br/>
						<p>
							Name : {{ $job['project_name'] }} <br/>
							Service Track ID : {{ $job_id }}<br/>
							Client Reference : {{ $job['purchase_order'] }}<br/>
							Pages : {{ $job['total_pages_submitted'] }}<br/><br/>
							Due Date : {{ $job['due_date'] }}<br/>
							Computed Price : {{ $job['computed_price'] }}<br/>
							Quoted Price: {{ $job['quoted_price'] }}<br/><br/>
							Uploaded Files
							<table border="1" style="width:100%;border:1px solid #000;border-collapse: collapse;">
								<thead>
									<tr>
										<th>File Name</th>
										<th>Number of Pages</th>
									</tr>
								</thead>
								<tbody>
									@foreach($source_files['file_name'] as $k => $each)
									<tr>
										<td>{{$each}}</td>
										<td>{{$source_files['pages'][$k]}}</td>
									</tr>
									@endforeach
								</tbody>
							</table><br/>
						</p>
						<br/>
						Please review invoice for immediate payment.
						<br/>
						<br/>
							Best regards,<br/>
							The 1StopXBRL Team
						
					</p>
					</div>
					<div style="text-align:right;font-size:12px;color:#95a5a6;margin-bottom:15px;padding:16px">1STOPXBRL Limited | Service Track &copy; 2011 All Rights Reserved</div>
					<div style="clear:both;"></div>
				</div>
			</div>
		</body>
	</html>	
