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
						Please be informed that job is cancelled by the Client:
						<br/><br/>
						<p>
							Job Name : {{ $job['project_name'] }} <br/>
							Job Number : {{ $job['id'] }}<br/>
						</p>
						<p>
							Due to following reason(s) :<br/>
							{{ $reason }}
						</p>
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
