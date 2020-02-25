<!DOCTYPE html>
	<html>
		<head>
		</head>
		<body>
			<div style="background:#bdc3c7;font: normal normal normal 13px/1.334 Tahoma, Geneva, sans-serif;padding:30px;">
				<div style="width:600px;background:white;margin:auto;padding:16px">
					<h2 style="color:#3c6382">{{$user_subject}}</h2>
					<hr style="border-top-color:#27ae60">
					<p style="font-size:12px;">
						{{ $user_message }}
						<br/>
						<br/>
							Best regards,<br/>
							{{ $user_name.' '.$user_lastname }}
						
					</p>
					<hr style="border-top-color:#27ae60;margin-top:30px;">
					<div style="text-align:right;font-size:12px;color:#95a5a6;margin-bottom:15px;">1STOPXBRL Limited | Service Track &copy; 2011 All Rights Reserved</div>
					<div style="clear:both;"></div>
				</div>
			</div>
		</body>
	</html>	
