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
						Please be informed that your ServiceTrack job has been completed and is ready for sign off:
						<br/><br/>
						<p>
							Name : {{ $job['project_name'] }} <br/>
							Service Track ID : {{ $job['id'] }}<br/>
							Client Reference : {{ $company_name }}<br/>
							Due date : {{ $job['due_date'] }}
						</p>
						<br/>
						Link : {{ $link }}
						<p>
							Your Package Contains 2 Files;<br/>
							1.) .html = this file is your ixbrl file that needs submitting to the HMRC we strongly advice you DO NOT open this file by double clicking on it.(see note) <br/>
							2.) .xlsx = this file is for you to review and comment if necessary on our tagging desicions and should be used to resubmit revisions.
						</p>
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
