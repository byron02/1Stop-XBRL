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
							Hi {{ $first_name.' '.$last_name }},
							<br/><br/>
							Your account has been activated. You may now <a href="http://servicetrack.1stopxbrl.co.uk">login</a> to access you account.
							
						</p>
					This email sent out by ServiceTrack at {{ date('d-m-Y') }}
					</div>
					<div style="text-align:right;font-size:12px;color:#95a5a6;margin-bottom:15px;padding:16px">1STOPXBRL Limited | Service Track &copy; 2011 All Rights Reserved</div>
					<div style="clear:both;"></div>
				</div>
			</div>
		</body>
	</html>	
