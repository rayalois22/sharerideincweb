<?php
	class template{
		public function mail_booking_confirmation($booking, $user, $ride, $vehicle, $driver){
			if($booking == null | $user == null | $ride == null | $vehicle == null | $driver == null){
				return null;
			}
			$mail = [];
			$subject = 'Booking confirmation';
			$body = 
			'Dear '. ucwords($user->getFirstName()) . ', '.'
			<br /><br />
			You have successfully booked a ride. Below are the details.
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
			You can <a href="tel:'. $driver->getTelephone() .'">call</a> or <a href="mailto:'. $driver->getEmailAddress() .'">email</a> the driver to have the ride.
			<br /><br />
			Thank you for choosing '. CONF['site']['title'] . '.<br /><br />
			
			Regards,<br /><br />
			
			'. CONF['site']['title'] . ' Support Team,<br />
			'. CONF['site']['copyright'];
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
			'Dear '. $user .',
			<br /><br />
			Congratulations!<br />
			Your account has been created successfully.<br />
			You can now <a href="'. CONF['site']['url'] .'">login</a> using your email address and password.<br />
			We hope to see you give and/or find rides.<br /><br />
			
			PS: Please do not share your password with anyone.<br /><br />
			
			Regards,<br /><br />
			
			'. CONF['site']['title'] . ' Support Team,<br />
			'. CONF['site']['copyright'];
			
			$mail['subject'] = $subject;
			$mail['body']	= $message;
			return $mail;
		}
	}
?>