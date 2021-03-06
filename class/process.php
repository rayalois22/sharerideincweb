<?php
	date_default_timezone_set(CONF['timezone']);
	/**
	* This class is responsible for all the heavy-lifting throughout the application.
	* It is the only class that interacts with the database and avails all the data used in the application.
	* 
	*/
	class process{
		private $PDO;
		private $BLOWFISH;
		public function __construct(){
			// creates a single PHP PDO Object used throughout the application to interact with the database.
			$PDO = new connection();
			$this->PDO = $PDO->connect();
			// initializes the component responsible for password encryption.
			$this->BLOWFISH = new blowfish();
		}
		
		/**
		* Ensures all user submitted data is safe for viewing in an email or a web page.
		*/
		public function safeInput($input){
			$data = trim($input);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
		}
		
		public function setSessionData(){
			// fetches all users
			$stmt = $this->PDO->prepare('SELECT * FROM tbl_user');
			$stmt->execute([]);
			// specifying PDO::FETCH_PROPS_LATE allows PDO to set the members using the fetched data
			// after constructor has been called
			$users = $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'user', array_fill(0, 7, ''));
			
			// fetches all vehicles
			$stmt = $this->PDO->prepare('SELECT * FROM tbl_vehicle');
			$stmt->execute([]);
			$vehicles = $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'vehicle', array_fill(0, 4, ''));
			
			// fetches all rides
			$stmt = $this->PDO->prepare('SELECT * FROM tbl_ride');
			$stmt->execute([]);
			$rides = $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'ride', array_fill(0, 7, ''));
			
			//fetches all future rides
			$stmt = $this->PDO->prepare('SELECT * FROM `tbl_ride` WHERE `status` = :status');
			$stmt->execute(['status' => 0]);
			$futurerides = $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'ride', array_fill(0, 7, ''));
			
			//fetches all bookings 
			$stmt = $this->PDO->prepare('SELECT * from `tbl_booking`');
			$stmt->execute([]);
			$bookings = $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'booking', array_fill(0, 4, ''));
			
			// store the data in the current session
			$_SESSION['data'] = [
				'users' => serialize($users),
				'vehicles' => serialize($vehicles),
				'rides' => serialize($rides),
				'futurerides' => serialize($futurerides),
				'bookings' => serialize($bookings),
			];
		}
		
		/**
		*	Creates a new user
		*/
		public function newUser($user){
			try{
				$stmt = $this->PDO->prepare(
					'INSERT INTO `tbl_user`(`id`, `firstName`, `lastName`, `gender`, 
					`password`, `emailAddress`, `telephone`, `role`, `status`, `profileImage`, 
					`lastAccess`, `lastIP`) VALUES (:id, :firstname, :lastname, :gender, :password,
					:emailadress, :telephone, :role, :status, :profileimage, :lastacess, :lastip)'
				);
				$stmt->execute([
					'id'	=> $user->getId(),
					'firstname' => $user->getfirstName(),
					'lastname'	=> $user->getLastName(),
					'gender' 	=> $user->getGender(),
					'password'	=> $this->BLOWFISH->encrypt_password($user->getPassword()),
					'emailadress'=> $user->getEmailAddress(),
					'telephone'	=> $user->getTelephone(),
					'role'		=> $user->getRole(),
					'status'	=> $user->getStatus(),
					'profileimage'=> $user->getProfileImage(),
					'lastacess'	=> $user->getLastAccess(),
					'lastip'	=> $user->getLastIP(),
				]);
				return true;
			}catch(PDOException $e){
				$duplicate = 'Integrity constraint violation: 1062 Duplicate entry';
				if(strpos($e->getMessage(), $duplicate) !== false){
					//users exists already
					return false;
				} else {
					//unable to register the user due to unknown reason/exception
					throw $e;
				}
			}
		}
		
		public function updateUser(){
			
		}
		
		public function removeUser(){
			
		}
		
		public function viewUsers(){
			
		}
		
		public function login($emailAddress, $password){
			try {
				//check if user exists
				$stmt = $this->PDO->prepare('SELECT * FROM `tbl_user` WHERE `emailAddress`=:emailAddress');
				$stmt->execute(['emailAddress' => $emailAddress]);
				$user = $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'user', array_fill(0, 7, ''));
				if($user != null){
					if($this->BLOWFISH->isPassword($password, $user[0]->getPassword())){
						//correct password
						$_SESSION['SESS_USER'] = serialize($user[0]);
						return true;
					} else {
						//incorrect password
						if(isset($_SESSION['SESS_USER'])){
							unset($_SESSION['SESS_USER']);
						}
						return false;
					}
				}
				return false;
			} catch(PDOException $e){
				return false;
			}			
		}
		
		public function logout(){
			
		}
		
		public function newVehicle($vehicle){
			try {
				$stmt = $this->PDO->prepare('INSERT INTO `tbl_vehicle`(
					`regNumber`, `model`, `capacity`, `driver`) VALUES (
					:regNumber, :model, :capacity, :driver)');
				$stmt->execute([
					'regNumber' => $vehicle->getRegNumber(),
					'model'	=> $vehicle->getModel(),
					'capacity'	=> $vehicle->getCapacity(),
					'driver'	=> $vehicle->getDriver(),
				]);
				return true;
			} catch(PDOException $e){
				$duplicate = 'Integrity constraint violation: 1062 Duplicate entry';
				if(strpos($e->getMessage(), $duplicate) !== false){
					//vehicle already exists. 
					// check whether it belongs to this user and whether the origin and destination are different before creating new ride.
					return false;
				} else {
					//unable to register the vehicle due to unknown reason/exception
					throw $e;
				}
			}			
		}
		
		public function updateVehicle(){
			
		}
		
		public function removeVehicle(){
			
		}
		
		public function newRide($ride){
			try {
				$stmt = $this->PDO->prepare('INSERT INTO `tbl_ride`(
					`id`, `origin`, `destination`, `status`, `dateOffered`, `vehicle`, `driver`)
					VALUES (:id, :origin, :destination, :status, :dateOffered, :vehicle, :driver)');
				$stmt->execute([
					'id' => $ride->getId(),
					'origin'	=> $ride->getOrigin(),
					'destination'	=> $ride->getDestination(),
					'status'	=> $ride->getStatus(),
					'dateOffered' => $ride->getDateOffered(),
					'vehicle'	=> $ride->getVehicle(),
					'driver'	=> $ride->getDriver(),
				]);
				return true;
			} catch(PDOException $e){
				$duplicate = 'Integrity constraint violation: 1062 Duplicate entry';
				if(strpos($e->getMessage(), $duplicate) !== false){
					//ride exists already
					return false;
				} else {
					//unable to register the ride due to unknown reason/exception
					throw $e;
				}
			}
		}
		
		public function bookRide($booking){
			try {
				$stmt = $this->PDO->prepare('INSERT INTO `tbl_booking`(`id`, `dateBooked`, `ride`,
					`user`) VALUES (:id, :datebooked, :ride, :user)');
				$stmt-> execute([
					'id' 			=> $booking->getId(),
					'datebooked' 	=> $booking->getDateBooked(),
					'ride'			=> $booking->getRide(),
					'user'			=> $booking->getUser(),
				]);
				return true;
			} catch(PDOException $e){
				$duplicate = 'Integrity constraint violation: 1062 Duplicate entry';
				if(strpos($e->getMessage(), $duplicate) !== false){
					//booking exists already.
					return false;
				} else {
					//unable to book the ride due to unknown reason/exception
					throw $e;
				}
			}
		}
		
		public function viewBooking($rideId){
			try{
				$stmt = $this->PDO->prepare('SELECT * FROM `tbl_booking` WHERE `ride`=:ride');
				$stmt->execute(['ride' => $rideId]);
				$booking = $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'booking', array_fill(0, 4, ''));
				return $booking;
			} catch(PDOException $e){
				return null;
			}
		}
		
		public function updateRide($ride){
			try {
				$stmt = $this->PDO->prepare('UPDATE `tbl_ride` SET `origin`=:origin,
					`destination`=:destination,`status`=:status WHERE `id`=:id');
				$stmt->execute([
					'origin' => $ride->getOrigin(),
					'destination' => $ride->getDestination(),
					'status'	=> $ride->getStatus(),
					'id'		=> $ride->getId(),
				]);
				return true;
			} catch(PDOException $e){
				return false;
				//throw $e;
			}
		}
		
		public function viewFutureRides(){
			
		}
		
		public function viewRides(){
			
		}
		
		public function sendMail($address, $subject, $message){
			require_once 'lib' . DIRECTORY_SEPARATOR . $_SESSION['reader']['lib']['mailer'] . DIRECTORY_SEPARATOR . "PHPMailerAutoload.php";
			$mail = new PHPMailer();
			$mail->SMTPDebug = 0; // set to 0 when going live.
			$mail->isSMTP();
			$mail->Host = "smtp.gmail.com";
			$mail->SMTPAuth = true;
			$mail->Username = "auth.sharerideinc@gmail.com";
			$mail->Password = "sharerideinc!2018!";
			$mail->isSMTPSecure = "ssl"; // can as well use tls
			
			//TODO: comment SMTP options when going live or set it to more secure values
			$mail->SMTPOptions = [
				'ssl' => [
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true,
				],
			];
			
			$mail->Port = 587;
			$mail->isHTML(true);
			$mail->From = "auth.sharerideinc@gmail.com";
			$mail->FromName = CONF['site']['title'];
			$mail->Subject = $subject;
			$mail->Body = $message;
			if(!is_array($address)){
				$mail->addAddress($address);
			} else {
				foreach($address as $key=>$value){
					$mail->addAddress($value);
				}
			}
			if(!$mail->send()){
				//echo 'Mailer Error: ' . $mail->ErrorInfo;
				$response  = false;
			} else {
				$response = true;
			}
			return $response;			
		}
	}
?>