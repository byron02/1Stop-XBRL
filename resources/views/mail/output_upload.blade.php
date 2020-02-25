<!DOCTYPE html>
	<html>
		<head>
		</head>
		<body>
			<div style="background:#bdc3c7;font: normal normal normal 13px/1.334 Tahoma, Geneva, sans-serif;padding:30px;">
				<div style="width:600px;background:#1b2a30;margin:auto;">
					<div style="padding:16px;margin:0">
						<img src="http://23.96.8.125/public/img/servicetrack-logo-hq.png" style="margin:auto;width:100px"/>
					</div>
					<div style="padding: 16px;background: #fff;">
					<p style="font-size:12px;">
						Please be informed that vendor has uploaded files to complete Service Track Job #{{$job['id']}}
						<br/><br/>
						<p>
							Name : {{ $job['project_name'] }} <br/>
							Service Track ID : {{ $job['id'] }}<br/>
							Client Reference : {{ $job['purchase_order'] }}<br/>
							Due Date : {{ $job['due_date'] }}<br/>
							
							Files :
							<table border="1" style="width:100%;border:1px solid #000;border-collapse: collapse;">
								<thead>
									<tr>
										<th>File Name</th>
										<th>Number of Pages</th>
									</tr>
								</thead>
								<tbody>
									@foreach($job_source as $each)
									<tr>
										<td>{{$each}}</td>
										<td>-</td>
									</tr>
									@endforeach
								</tbody>
							</table><br/>
						</p>
						<br/>
						Please verify the uploaded files and update the job status to "Completed."
						<br/>
						<br/>
							Best regards,<br/>
							The 1StopXBRL Team
						
					</p>
					This email sent out by ServiceTrack at {{ date('d-m-Y') }}
					</div>
					<div style="text-align:right;font-size:12px;color:#95a5a6;margin-bottom:15px;padding:16px">1STOPXBRL Limited | Service Track &copy; 2011 All Rights Reserved</div>
					<div style="clear:both;"></div>
				</div>
			</div>
		</body>
	</html>	
