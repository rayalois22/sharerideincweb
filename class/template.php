<?php
	class template{
		public function mail_booking_confirmation($booking, $user, $ride, $vehicle, $driver){
			if($booking == null | $user == null | $ride == null | $vehicle == null | $driver == null){
				return null;
			}
			$mail = [];
			$subject = 'Booking confirmation';
			$body = 
			'<p>Dear '. ucwords($user->getFirstName()) . ', '.'</p>
			<br /><br />
			<p>You have successfully booked a ride. Below are the details.</p>
			<br /><br />								
			<table>
				<tbody>
					<tr>
						<td>Ride ID:</td>
						<td>'. $ride->getId().'</td>
					</tr>
					<tr>
						<td>Origin:</td>
						<td>'. $ride->getOrigin() .'</td>
					</tr>
					<tr>
						<td>Destination:</td>
						<td>'. $ride->getDestination() .'</td>
					</tr>
					<tr>
						<td>Capacity:</td>
						<td>'. $vehicle->getCapacity() .'</td>
					</tr>
					<tr>
						<td>Driver:</td>
						<td>'. $driver->getFirstName() . ' '. $driver->getLastName() .'</td>
					</tr>
					<tr>
						<td>Vehicle registration:</td>
						<td>'. $vehicle->getRegNumber() .'</td>
					</tr>
					<tr>
						<td>Date of booking:</td>
						<td>'. $booking->getDateBooked().'</td>
					</tr>
				</tbody>
			</table>
			<br /><br />								
			<p>You can <a href="tel:'. $driver->getTelephone() .'">call</a> or <a href="mailto:'. $driver->getEmailAddress() .'">email</a> the driver to have the ride.</p>
			<br /><br />
			<p>Thank you for choosing '. CONF['site']['title'] . '</p>.<br /><br />
			
			<p>Regards,</p><br /><br />
			
			<p>'. CONF['site']['title'] . ' Support Team,</p><br />
			<p>'. CONF['site']['copyright'].'</p>';
			$mail['subject'] = $subject;
			$mail['body'] = $body;
			return $mail;
		}
		
		public function mail_registration_confirmation($user){
			if($user == null){
				return null;
			}
			$mail = [];
			$subject = 'Account created.';
			$message = 
			'<p>Dear '. $user .',</p>
			<br /><br />
			<p>Congratulations!<br />
			<p>Your account has been created successfully.</p><br />
			<p>You can now <a href="'. CONF['site']['url'] .'">login</a> using your email address and password.</p><br />
			<p>We hope to see you give and/or find rides.</p><br /><br />
			
			<p>PS: Please do not share your password with anyone.</p><br /><br />
			
			Regards,<br /><br />
			
			'. CONF['site']['title'] . ' Support Team,<br />
			'. CONF['site']['copyright'];
			
			$mail['subject'] = $subject;
			$mail['body']	= $message;
			return $mail;
		}
	}
?>