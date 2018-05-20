<?php
	date_default_timezone_set(CONF['timezone']);
	
	class process{
		private $PDO;
		private $BLOWFISH;
		public function __construct(){
			$PDO = new connection();
			$this->PDO = $PDO->connect();
			$this->BLOWFISH = new blowfish();
		}
		
		public function get(){
			$this->check_download_req();
			$this->the_error();
			$this->manage_users();
			$this->manage_articles();
		}
		
		public function show_index(){
			$content = 
			'<div class="container">
				<div class="jumbotron">
					<h1>Welcome to Shareride!</h1>
					<p>You can find your ride and book it here.</p>
					<p>You can also offer others a ride!</p>
					<input class="btn btn-lg" type="submit" formaction="./?login" value="Join or Login today" />
				</div>
			</div>';
			return $content;
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
			
			//fetches all futures
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
					'firstname' => $user->getFirstName(),
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
					//user exists already
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
				echo $e->getMessage();
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
					// return false so $CTRL knows to check if it belongs to the currently logged in user.
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
			require_once $_SERVER['DOCUMENT_ROOT'] . 'lib' . DIRECTORY_SEPARATOR . $_SESSION['reader']['lib']['mailer'] . DIRECTORY_SEPARATOR . "PHPMailerAutoload.php";
			$mail = new PHPMailer();
			$mail->SMTPDebug = 1; // set to 0 when going live.
			$mail->isSMTP();
			$mail->Host = "smtp.gmail.com";
			$mail->SMTPAuth = true;
			$mail->Username = "auth.sharerideinc@gmail.com";
			$mail->Password = "sharerideinc!2018!";
			$mail->isSMTPSecure = "ssl";
			
			//TODO: comment SMTP options when going live
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
				echo 'Mailer Error: ' . $mail->ErrorInfo;
				$response  = false;
			} else {
				$response = true;
			}
			return $response;			
		}
/**
*
*login()
*
*Validates user credentials, authenticates the user,
*updates access_time and sets the 'user' session variable
*
*@return boolean  
*/
		public function login1(){
			$ObjCHK = new db_check();
			$username = $this->connection->real_escape_string($_POST[$_SESSION['readers']['users']['formfields']['r']['un']]);
			$password = $this->connection->real_escape_string($_POST[$_SESSION['readers']['users']['formfields']['r']['pw']]);
			if($ObjCHK->is_user_present($username)){
				$BF = new blowfish();
				if($BF->isPassword($password, $ObjCHK->get_password_hash($username))){
					//successful login
					if($user = $ObjCHK->get_the_user($username)){
						$user = [
							'user_id' => $user->get_user_id(),
							'user_name' => $user->get_user_name(),
							'full_name' => $user->get_full_name(),
							'email' => $user->get_email(),
							'phone_number' => $user->get_phone_number(),
							'profile_image' => $user->get_profile_image(),
							'address' => $user->get_address(),
							'user_type' => $user->get_user_type()
						];
						if($ObjCHK->lastaccess($user['user_id'])){}
						$_SESSION['user'] = $user;
						return true;
					} else {return false;}
				} else {return false;}
			} else {return false;}
		}
		public function the_error(){
			if(isset($_GET['no_user_selected'])){
				$this->main_content_open();
				print '<b>You made no selection</b>';
				$this->main_content_close();
			}
		}
		public function manage_users(){
			$this->manage_users_response();
			$this->save_new_profile_data();
			//logout
			if(isset($_GET['logout'])){
				//update access time
				$ObjCHK = new db_check();
				if($ObjCHK->lastaccess($_SESSION['user']['user_id'])){
					//forget user
					unset($_SESSION['user']);
					//release resources
					/*unset($_SESSION['lang']);
					unset($_SESSION['readers']);*/
					if(isset($_SESSION['articles'])){
						unset($_SESSION['articles']);
					}
					if(isset($_SESSION['users'])){
						unset($_SESSION['users']);
					}
				}
				//redirect
				header('Location: ./');
			}
			//update profile data
			if(isset($_GET['update_profile'])){
				$this->main_content_open();
				$FM = new amasy_forms();
				?><form action="./" method="post" enctype="multipart/form-data">
				<?php $FM->fm_user_update($_SESSION['user']); ?>
				<div>
					&nbsp;<input type="submit" name="<?php echo $_SESSION['readers']['buttons']['r']['uupdate']; ?>" value="<?php echo $_SESSION['lang']['forms']['buttons']['v']['update']; ?>" />&nbsp;&nbsp;
					<input type="submit" name="<?php echo $_SESSION['readers']['buttons']['r']['cancel']; ?>" value="<?php echo $_SESSION['lang']['forms']['buttons']['v']['cancel']; ?>" />
				</div>
				</form><?php
				$this->main_content_close();
			}
			if(isset($_GET['manage_other_users'])){
				$this->main_content_open();
				$FM = new amasy_forms();
				$ObjCHK = new db_check();
				$FM->fm_list('users', $lusers = $ObjCHK->list_users('super'));
				$_SESSION['users'] = $lusers;
				$this->main_content_close();
			}
			if(isset($_GET['manage_authors'])){
				$this->main_content_open();
				$FM = new amasy_forms();
				$ObjCHK = new db_check();
				$FM->fm_list('authors', $lusers = $ObjCHK->list_users('admin'));
				$_SESSION['users'] = $lusers;
				$this->main_content_close();
			}
		}
		public function manage_articles(){
			$this->manage_articles_response();
			if(isset($_GET['manage_articles'])){
				$this->main_content_open();
				$FM = new amasy_forms();
				$ObjCHK = new db_check();
				//list articles in management mode(marticles).
				$FM->fm_list('marticles', $larticles = $ObjCHK->list_articles());
				$this->main_content_close();
				$_SESSION['articles'] = $larticles;
			}
			if(isset($_GET['view_articles'])){
				$this->main_content_open();
				$FM = new amasy_forms();
				$ObjCHK = new db_check();
				//list articles in viewing mode(varticles).
				$FM->fm_list('varticles', $larticles = $ObjCHK->list_articles());
				$this->main_content_close();
				$_SESSION['articles'] = $larticles;
			}
		}
		private function save_new_profile_data(){
			if(isset($_POST[$_SESSION['readers']['buttons']['r']['uupdate']])){
				// read form input
				$uupd = [
					$_POST[$_SESSION['readers']['users']['formfields']['r']['fn']],
					$_POST[$_SESSION['readers']['users']['formfields']['r']['em']],
					$_POST[$_SESSION['readers']['users']['formfields']['r']['pn']],
					$_POST[$_SESSION['readers']['users']['formfields']['r']['pw']],
					$_POST[$_SESSION['readers']['users']['formfields']['r']['ad']],
					$_FILES[$_SESSION['readers']['users']['formfields']['r']['pi']]
				];
				$editedusers = [];
				if(!isset($_SESSION['editedusers'])){
					$ObjCHK = new db_check();
					$pi = [
						$uupd[5]['name'][0],
						$uupd[5]['type'][0],
						$uupd[5]['tmp_name'][0],
						$uupd[5]['error'][0],
						$uupd[5]['size'][0]
					];
					$uupd = [
						$uupd[0][0],
						$uupd[1][0],
						$uupd[2][0],
						$uupd[3][0],
						$uupd[4][0],
						$pi
					];
					//check if a new profile image has been submitted
					if(!empty($uupd[5][4])){
						if($uupd[5][3] === 0){
							//no error in the image, so upload the image
							$img = $ObjCHK->upload_profimage($uupd[5], $_SESSION['user']['user_name']);
							if($img != "default.png"){
								$_SESSION['user']['profile_image'] = $img;
							}
						} else {
							$this->main_content_open();
							print 'Image not okay';
							$this->main_content_close();
						}
					}
					//check if a new password was submitted
					if(!empty($uupd[3])){
						//encrypt the new password
						$BF = new blowfish();
						$_SESSION['user']['password'] = $BF->encrypt_password($uupd[3]);
					} else {
						$_SESSION['user']['password'] = $uupd[3];
					}
					$_SESSION['user']['full_name'] = $uupd[0];
					$_SESSION['user']['email'] = $uupd[1];
					$_SESSION['user']['phone_number'] = $uupd[2];
					$_SESSION['user']['address'] = $uupd[4];
					//go ahead and update the user's profile
					if($ObjCHK->update_user($_SESSION['user']['user_id'], $_SESSION['user']['user_name'], $_SESSION['user']['full_name'], $_SESSION['user']['email'], $_SESSION['user']['phone_number'], $_SESSION['user']['password'], $_SESSION['user']['profile_image'], $_SESSION['user']['address'])){
						//success
						header('Location: ./?success');
					} else {
						//error
						header('Location: ./?error');
					}
					exit;
				}
				for($i=0; $i<count($_SESSION['editedusers']); $i++){
					$pi  = [
						"name"		=> $uupd[5]['name'][$i],
						"type"		=> $uupd[5]['type'][$i],
						"tmp_name"	=> $uupd[5]['tmp_name'][$i],
						"error"		=> $uupd[5]['error'][$i],
						"size"		=> $uupd[5]['size'][$i]
					];
					$usa = [
						"full_name"		=> $uupd[0][$i],
						"email" 		=> $uupd[1][$i],
						"phone_number"  => $uupd[2][$i],
						"password"		=> $uupd[3][$i],
						"address"		=> $uupd[4][$i],
						"profile_image" => $pi,
						"user_name"		=> $_SESSION['editedusers'][$i],
						"user_id"		=> $_SESSION['editedids'][$i]
					];
					array_push($editedusers, $usa);
				}
				unset($_SESSION['editedusers']);
				unset($_SESSION['editedids']);
				$_SESSION['userupdates'] = $editedusers;
				unset($editedusers);
				foreach($_SESSION['userupdates'] as $key=>$value){
					$ObjCHK = new db_check();
					//put the image in a format recognised by ObjCHK, if an image exists
					if(!empty($value['profile_image']['size'])){
						$image = [
							$value['profile_image']['name'],
							$value['profile_image']['type'],
							$value['profile_image']['tmp_name'],
							$value['profile_image']['error'],
							$value['profile_image']['size']
						];
						if($image[3] === 0){
							//no error in the image, so upload the image
							$img = $ObjCHK->upload_profimage($image, $value['user_name']);
							if($img != "default.png"){
								$value['profile_image'] = $img;
							}
						} else {
							print 'Image not okay';
						}
					} else if(empty($value['profile_image']['size'])){
						//find the user's profile image
						for($i=0; $i<count($_SESSION['users']); $i++){
							if(in_array($value['user_name'], $_SESSION['users'][$i])){
								$value['profile_image'] = $_SESSION['users'][$i]['profile_image'];
							}
						}
					}
					if(!empty($value['password'])){
						//encrypt the new password
						$BF = new blowfish();
						$value['password'] = $BF->encrypt_password($value['password']);
					}
					//confirm that a profile image was uploaded or found
					if(!is_array($value['profile_image'])){
						//update profile image as well
						if($ObjCHK->update_user($value['user_id'], $value['user_name'], $value['full_name'], $value['email'], $value['phone_number'], $value['password'], $value['profile_image'], $value['address'])){
							//success
							header("Content-Type: text/html; charset=ISO-8859-1");
							header('Location: ./');
						} else {
							//error
							$this->main_content_open();
							print 'An error occurred';
							$this->main_content_close();
						}
					} else {
						$this->main_content_open();
						print 'An error occurred';
						$this->main_content_close();
					}
				}
			} 
			if(isset($_POST[$_SESSION['readers']['buttons']['r']['cancel']])){
				//redirect
				header('Location: ./');
			}
		}
		private function manage_users_response(){
			if(isset($_POST[$_SESSION['readers']['users']['actions']['r']['n']])){
				$this->main_content_open();
				$FM = new amasy_forms();
				switch($_SESSION['user']['user_type']){
					case 'super':
						$FM->fm_register('admin');
						break;
					case 'admin':
						$FM->fm_register('autha');
						break;	
					default:
						break;
				}
				$this->main_content_close();
			}
			//create a new user
			if(isset($_POST[$_SESSION['readers']['buttons']['r']['nsuper']]) || isset($_POST[$_SESSION['readers']['buttons']['r']['nadmin']]) || isset($_POST[$_SESSION['readers']['buttons']['r']['nautha']])){
				$ObjCHK = new db_check();
				$username = $this->connection->real_escape_string($_POST[$_SESSION['readers']['users']['formfields']['r']['un']]);
				if(!$ObjCHK->is_user_present($username)){
					$BF = new blowfish();
					$fullname = $this->connection->real_escape_string($_POST[$_SESSION['readers']['users']['formfields']['r']['f']]) . ' ' . $this->connection->real_escape_string($_POST[$_SESSION['readers']['users']['formfields']['r']['on']]);
					$fullname = strtolower($fullname);
					$fullname = ucwords($fullname);
					if(!empty($_FILES[$_SESSION['readers']['users']['formfields']['r']['pi']])){
						$image = [
							$_FILES[$_SESSION['readers']['users']['formfields']['r']['pi']]['name'],
							$_FILES[$_SESSION['readers']['users']['formfields']['r']['pi']]['type'],
							$_FILES[$_SESSION['readers']['users']['formfields']['r']['pi']]['tmp_name'],
							$_FILES[$_SESSION['readers']['users']['formfields']['r']['pi']]['error'],
							$_FILES[$_SESSION['readers']['users']['formfields']['r']['pi']]['size']
						];
						$profileimage = $ObjCHK->upload_profimage($image, $username);
					} else {
						$profileimage = 'default.png';
					}
					if($ObjCHK->create_new_user($fullname, $this->connection->real_escape_string($_POST[$_SESSION['readers']['users']['formfields']['r']['em']]), $this->connection->real_escape_string($_POST[$_SESSION['readers']['users']['formfields']['r']['pn']]), $username, $BF->encrypt_password($this->connection->real_escape_string($_POST[$_SESSION['readers']['users']['formfields']['r']['pw']])), $_SESSION['utype'], $profileimage, $this->connection->real_escape_string($_POST[$_SESSION['readers']['users']['formfields']['r']['ad']]))){
						$mailresponse = $this->email($_POST[$_SESSION['readers']['users']['formfields']['r']['em']], "Account created", '
							Dear ' . $fullname . ',
							
							Congratulations!
							Your account has been created with the username: '. $username . ' and default password: '. $_POST[$_SESSION['readers']['users']['formfields']['r']['pw']] . '. 
							Please login at https://amasy.herokuapp.com and change your password.
							Do not share your password with anyone.
							
							Regards,
							
							AMASY Accounts Team,
							AMASY ' . date('Y', time()) . '.
						');
						//TODO: create a template for the subject and message strings
						$this->main_content_open();
						if(!$mailresponse["error"]){
							print $mailresponse["message"];
						} else {
							print $mailresponse["message"];
						}
						$this->main_content_close();
					} else {
						$this->main_content_open();
						print 'Error';
						$this->main_content_close();
					}
					unset($_SESSION['utype']);
				} else {
					$this->main_content_open();
					print 'Error:' . $username . ' is taken. Try a different username. <br />';
					$this->main_content_close();
					exit;
				}
			}
			//edit selected users
			if(isset($_POST[$_SESSION['readers']['users']['actions']['r']['e']])){
				//print_r($_POST[$_SESSION['lang']['forms']['lists']['users']['selected']]);
				//print_r($_SESSION['users']);
				$this->main_content_open();
				$FM = new amasy_forms();
				?><form action="./" method="post" enctype="multipart/form-data"><?php
				$editedusers = [];
				$editedids   = [];
				//if(!isset($_POST[$_SESSION['lang']['forms']['lists']['users']['selected']])){
				if(!isset($_POST[$_SESSION['readers']['users']['selected']])){
					header('Location: ./?no_user_selected');
					exit;
				}
				foreach($_POST[$_SESSION['readers']['users']['selected']] as $key=>$value){
					for($i=0; $i<count($_SESSION['users']); $i++){
						if(in_array($value, $_SESSION['users'][$i])){
							$FM->fm_user_update($_SESSION['users'][$i]); 
							array_push($editedusers, $_SESSION['users'][$i]['user_name']);
							array_push($editedids, $_SESSION['users'][$i]['user_id']);
						}
					}
				}
				$_SESSION['editedusers'] = $editedusers;
				$_SESSION['editedids']	 = $editedids;
				unset($editedusers);
				unset($editedids);
				?>
				<div>
					&nbsp;<input type="submit" name="<?php echo $_SESSION['readers']['buttons']['r']['uupdate']; ?>" value="<?php echo $_SESSION['lang']['forms']['buttons']['v']['update']; ?>" />&nbsp;&nbsp;
					<input type="submit" name="<?php echo $_SESSION['readers']['buttons']['r']['cancel']; ?>" value="<?php echo $_SESSION['lang']['forms']['buttons']['v']['cancel']; ?>" />
				</div>
				</form>
				<?php
				$this->main_content_close();
			}
			//delete selected user(s)
			if(isset($_POST[$_SESSION['readers']['users']['actions']['r']['d']])){
				if(!isset($_POST[$_SESSION['readers']['users']['selected']])){
					header('Location: ./?no_user_selected');
					exit;
				}
				$isdeleted = false;
				$ObjCHK = new db_check();
				for($i=0; $i<count($_SESSION['users']); $i++){
					foreach($_POST[$_SESSION['readers']['users']['selected']] as $key=>$value){
						if(in_array($value, $_SESSION['users'][$i])){
							if($ObjCHK->delete_user($_SESSION['users'][$i]['user_id'] ,$value)){
								$isdeleted = true;
							} else {
								$isdeleted = false;
							}
						}
					}
				}
				if($isdeleted){
					header('Location: ./?manage_users');
				} else {
					$this->main_content_open();
					print 'Failed to delete due to an error<br /><br />';
					$this->main_content_close();
				}
			}
			if(isset($_POST[$_SESSION['readers']['users']['actions']['r']['pdf']])){
				//generate user pdf document
				header('Location: export.php?user_pdf');
			}
			if(isset($_POST[$_SESSION['readers']['users']['actions']['r']['xls']])){
				//generate user xls document 
				header('Location: export.php?user_xls');				
			}
			if(isset($_POST[$_SESSION['readers']['users']['actions']['r']['txt']])){
				//generate user txt
				header('Location: export.php?user_txt');
			}
		}
		public function export_user_pdf(){
			if(isset($_SESSION['users'])){
				switch($_SESSION['user']['user_type']){
					case 'super':
						$type = 'users';
						$tabletitle = $_SESSION['lang']['forms']['user']['t']['ausrs'];
						break;
					case 'admin':
						$type = 'authors';
						$tabletitle = $_SESSION['lang']['forms']['user']['t']['autha'];
						break;
					default:
						$type = '';
						break;
				}
				//require tcpdf
				require_once 'lib/tcpdf/tcpdf.php';
				// create new PDF document
				$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, $_SESSION['lang']['enc'], false);

				// set document information
				$pdf->SetCreator(PDF_CREATOR);
				$pdf->SetAuthor($_SESSION['readers']['site']['title']);
				$pdf->SetTitle("$tabletitle");//document title
				$pdf->SetSubject("$tabletitle");
				$pdf->SetKeywords('TCPDF, PDF, '."$tabletitle".', list, pdf');

				// set default header data
				$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $_SESSION['readers']['site']['title'], '');

				// set header and footer fonts
				$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, $_SESSION['readers']['site']['title'], PDF_FONT_SIZE_MAIN));
				$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, $_SESSION['readers']['site']['title'], PDF_FONT_SIZE_DATA));

				// set default monospaced font
				$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

				// set margins
				$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
				$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
				$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

				// set auto page breaks
				$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

				// set image scale factor
				$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

				// set some language-dependent strings (optional)
				/*
				if (@file_exists(dirname(__FILE__).'home/lang/eng.php')) {
					require_once(dirname(__FILE__).'home/lang/eng.php');
					$pdf->setLanguageArray($l);
				}
				*/
				// ---------------------------------------------------------

				// set font
				$pdf->SetFont('helvetica', 'B', 16);

				// add a page
				$pdf->AddPage();

				$pdf->Write(0, "$tabletitle", '', 0, 'L', true, 0, false, false, 0);//title of the table is Users

				$pdf->SetFont('helvetica', '', 10);
				$data = $_SESSION['users'];
				
				$lheaders = array_slice($_SESSION['lang']['forms']['lists']['users']['headers'], 0, 9);
				$tbl = '';
				$tbl .= '
				<table border = "1" align = "center" style = "width: 80%; border-collapse: collapse; border: 1px solid #373737;">
					<tr>';  
						foreach($lheaders as $key=>$value){
							$tbl .= '<td width="14%">' . $value . '</td>';
						}
				$tbl .= 
					'</tr>';
					for($i=0; $i<count($data); $i++){
						$tbl .= '<tr class="the_list_row" id="the_list_row">';
						foreach($data[$i] as $key=>$value){
							switch($type){
								case "users":
									$tbl .= '<td>' . $data[$i]['user_id'] . '</td>';
									$tbl .= '<td>' . $data[$i]['full_name'] . '</td>';
									$tbl .= '<td>' . $data[$i]['email'] . '</td>';
									$tbl .= '<td>' . $data[$i]['phone_number'] . '</td>';
									$tbl .= '<td>' . $data[$i]['user_name'] . '</td>';
									$tbl .= '<td>' . $data[$i]['profile_image'] . '</td>';
									$tbl .= '<td>' . $data[$i]['address'] . '</td>';
									$tbl .= '<td>' . $data[$i]['user_type'] . '</td>';
									$tbl .= '<td>' . $data[$i]['access_time'] . '</td>';
									break;
								case "authors":
									$tbl .= '<td>' . $data[$i]['user_id'] . '</td>';
									$tbl .= '<td>' . $data[$i]['full_name'] . '</td>';
									$tbl .= '<td>' . $data[$i]['phone_number'] . '</td>';
									$tbl .= '<td>' . $data[$i]['email'] . '</td>';
									$tbl .= '<td>' . $data[$i]['user_name'] . '</td>';
									$tbl .= '<td>' . $data[$i]['profile_image'] . '</td>';
									$tbl .= '<td>' . $data[$i]['address'] . '</td>';
									$tbl .= '<td>' . $data[$i]['user_type'] . '</td>';
									$tbl .= '<td>' . $data[$i]['access_time'] . '</td>';
									break;
								default:
									break;
							}
							break;
						}
						$tbl .= '</tr>';
					}
				$tbl .= '<tr>'; 
						foreach($lheaders as $key=>$value){
							$tbl .= '<td>' . $value . '</td>';
						}
				$tbl .= '</tr></table>';
				$pdf->writeHTML($tbl, true, false, false, false, '');
				// -----------------------------------------------------------------------------
				//Close and output PDF document
				$pdf->Output('The title' . '_' . date("Y") . '.pdf', 'I');
			}
		}
		public function export_user_xls(){
			//get the data to export
			$data = $_SESSION['users'];
			//switch filename prefix 
			switch($_SESSION['user']['user_type']){
				case 'super':
					$pref = $_SESSION['lang']['forms']['user']['t']['ausrs'];
					break;
				case 'admin':
					$pref = $_SESSION['lang']['forms']['user']['t']['autha'];
					break;
				case 'autha':
					$pref = $_SESSION['lang']['forms']['article']['t']['all'];
					break;
				default:
					break;
			}
			//obtain cell headings
			$chead = array_slice($_SESSION['lang']['forms']['lists']['users']['headers'], 0, 9);
			$cdata = [];
			//match data fields to their respective cell headings
			for($i=0; $i<count($data); $i++){
				$ndata = array_values($data[$i]);
				$last = array_splice($ndata, 5, 3);
				$pwd = array_splice($last, 0, 1);
				$ndata = array_merge($ndata, $last);
				array_push($cdata, $ndata);
			}
			$ch = '';
			$cd = '';
			//delimit cell headers with a tab
			foreach($chead as $key=>$field){
				$h = $field . "\t";
				$ch .= $h;
			}
			//delimit each row with a line feed
			for($i=0; $i<count($cdata); $i++){
				$d = '';
				//check for empty fields
				//enclose data in double quotes 
				//delimit each cell with a tab
				foreach($cdata[$i] as $key=>$value){
					if ( ( !isset( $value ) ) || ( $value == "" ) ){
						$value = "\t";
					}else{
						//Check for phone number and timestamps and 
						//delimit with single quotes to prevent excel from managing them.
						if((preg_match("/(\d{10})/", $value))||(preg_match("/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/", $value))){
							$value = "'$value";
						}
						$value = str_replace( '"' , '""' , $value );
						$value = '"' . $value . '"' . "\t";
					}
					$d .= $value;
				}
				$cd .= trim( $d ) . "\n";
			}
			$cd = str_replace( "\r" , "" , $cd );
			//check if no data
			if ( $cd == "" ){
				$cd = "\nNo Record(s) Found!\n";
			}
			$ymdhms = date("_M_Y_dHis", time());
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=".$pref."$ymdhms.xls");
			header("Pragma: no-cache");
			header("Expires: 0");
			print "$ch\n$cd";
		}
		public function export_user_txt(){
			//get the data to export
			$data = $_SESSION['users'];
			//switch filename prefix 
			switch($_SESSION['user']['user_type']){
				case 'super':
					$pref = $_SESSION['lang']['forms']['user']['t']['ausrs'];
					break;
				case 'admin':
					$pref = $_SESSION['lang']['forms']['user']['t']['autha'];
					break;
				case 'autha':
					$pref = $_SESSION['lang']['forms']['article']['t']['all'];
					break;
				default:
					break;
			}
			$cdata = [];
			//match data fields to their respective cell headings
			for($i=0; $i<count($data); $i++){
				$ndata = array_values($data[$i]);
				$last = array_splice($ndata, 5, 3);
				$pwd = array_splice($last, 0, 1);
				$ndata = array_merge($ndata, $last);
				array_push($cdata, $ndata);
			}
			foreach($cdata as $key=>$user){
				$userid = str_pad($user[0], 8, " ", STR_PAD_RIGHT);
				$fullname = str_pad($user[1], 30, " ", STR_PAD_RIGHT);
				$email = str_pad($user[2], 40, " ", STR_PAD_RIGHT);
				$phonenumber = str_pad($user[3], 15, " ", STR_PAD_RIGHT);
				$username = str_pad($user[4], 15, " ", STR_PAD_RIGHT);
				$profileimage = str_pad($user[5], 30, " ", STR_PAD_RIGHT);
				$address = str_pad($user[6], 20, " ", STR_PAD_RIGHT);
				$usertype = str_pad($user[7], 8, " ", STR_PAD_RIGHT);
				$accesstime = str_pad(date("jS F Y, H:i:s", strtotime($user[8])), 20, " ", STR_PAD_RIGHT);
				$auser = $userid.$fullname.$email.$phonenumber.$username.$profileimage.$address.$usertype.$accesstime."\n\r";
				$ymdhms = date("_M_Y_dHis", time());
				$handle = fopen("textfiles/".$pref."$ymdhms.txt", "a") or die("Unable to open file!");
				fwrite($handle, $auser);
			}
			fclose($handle);
			header('Location: textfiles/'.$pref."$ymdhms.txt");
		}
		public function export_article_pdf($data){
			//generate pdf
			//require tcpdf
			require_once 'lib/tcpdf/tcpdf.php';
			// create new PDF document
			$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			// set document information
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('AMASY');
			$pdf->SetTitle($data['article_title']);//document title
			$pdf->SetSubject($data['article_title']);
			$pdf->SetKeywords('TCPDF', 'PDF', $data['article_title'], 'article', 'pdf');

			// set default header data
			$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $_SESSION['readers']['site']['title'], 'AMASY');

			// set header and footer fonts
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, $_SESSION['readers']['site']['title'], PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, $_SESSION['readers']['site']['title'], PDF_FONT_SIZE_DATA));

			// set default monospaced font
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

			// set margins
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

			// set auto page breaks
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

			// set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			// set some language-dependent strings (optional)
			/*
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
				require_once(dirname(__FILE__).'/lang/eng.php');
				$pdf->setLanguageArray($l);
			}
			*/
			// ---------------------------------------------------------

			// set font
			$pdf->SetFont('helvetica', 'B', 16);

			// add a page
			$pdf->AddPage();

			$pdf->Write(0, $data['article_title'], '', 0, 'L', true, 0, false, false, 0);//title of the table is set here

			$pdf->SetFont('helvetica', '', 12);
			
			$ObjCHK = new db_check();
			if($author=$ObjCHK->get_author_name($data['author_id'])){$data['author_full_name']= $author['full_name'];$data['author_user_name']=$author['user_name'];}else{$data['author_full_name']="";$data['author_user_name']="";}
			$tbl = '';
			$tbl .= '
			<table border = "0" style = "width: 100%; border-collapse: collapse;"><tr><td width="100%">'.$_SESSION['lang']['forms']['lists']['articles']['headers']['author_user_name'].':<span><em>'.$data['author_user_name'].'('.$data['author_id'].')</em></span></td></tr><tr><td>'.$_SESSION['lang']['forms']['lists']['articles']['headers']['article_created_date'].': '.$data['article_created_date'].'</td></tr><tr><td>'.$_SESSION['lang']['forms']['lists']['articles']['headers']['article_last_update'].': '.$data['article_last_update'].'</td></tr><tr><td>'.$_SESSION['lang']['forms']['lists']['articles']['headers']['article_full_text'].':</td></tr><tr><td>'.$data['article_full_text'].'</td></tr></table>';
			
			$pdf->writeHTML($tbl, true, false, false, false, '');
			// -----------------------------------------------------------------------------
			//Close and output PDF document
			$pdf->Output($data['article_title'] . '_' . date("Y") . '.pdf', 'I');
		}
		public function export_article_txt($data){
			//generate txt
			$ObjCHK = new db_check();
			if($author=$ObjCHK->get_author_name($data['author_id'])){$data['author_full_name']= $author['full_name'];$data['author_user_name']=$author['user_name'];}else{$data['author_full_name']="";$data['author_user_name']="";}
			$pref=strtolower($data['author_user_name'].'_'.$data['article_title']);
			$author=str_pad($data['author_user_name'].'('.$data['author_id'].')',30, " ", STR_PAD_RIGHT);
			$articletitle=str_pad($data['article_title'], 40, " ", STR_PAD_RIGHT);
			$articlecreateddate=str_pad($_SESSION['lang']['forms']['lists']['articles']['headers']['article_created_date'].':'.$data['article_created_date'], 20, " ", STR_PAD_RIGHT);
			$articlelastupdate=str_pad($_SESSION['lang']['forms']['lists']['articles']['headers']['article_last_update'].':'.$data['article_last_update'], 20, " ", STR_PAD_RIGHT);
			$articlefulltext=$data['article_full_text'];
			$txt = $author."\n".$articletitle."\n".$articlecreateddate."\n".$articlelastupdate."\n".$articlefulltext."\n\r";
			$ymdhms = date("Ymd_His", time());
			$handle = fopen('textfiles/'.$pref."$ymdhms.txt", "a")or die("Unable to open file!");
			fwrite($handle, $txt);
			fclose();
			header('Location: textfiles/'.$pref."$ymdhms.txt");
		}
		//new article, edit articles, delete articles, export as pdf/excel/text file.
		private function manage_articles_response(){
			//if the new button is pushed, display a form to register a new article
			if(isset($_POST[$_SESSION['readers']['articles']['actions']['r']['n'] ])){
				$FM = new amasy_forms();
				$this->main_content_open();
				$FM->fm_register('article');
				$this->main_content_close();
			}
			//new article
			if(isset($_POST[$_SESSION['readers']['buttons']['r']['narticle']])){
				$ObjCHK = new db_check();
				//check if an article with the same title exists, if not create it
				if(!$ObjCHK->is_article_present($_SESSION['user']['user_id'], $this->connection->real_escape_string($_POST[$_SESSION['readers']['article']['r']['ti']]))){
					//print $_POST[$_SESSION['readers']['article']['r']['di']];
					$art = [
						$_SESSION['user']['user_id'],
						addslashes($_POST[$_SESSION['readers']['article']['r']['ti']]),
						addslashes($_POST[$_SESSION['readers']['article']['r']['ft']]),
						$_POST[$_SESSION['readers']['article']['r']['di']],
						$_POST[$_SESSION['readers']['article']['r']['or']]
					];
					$this->main_content_open();
					if($ObjCHK->create_new_article($art[0], $art[1], $art[2], $art[3], $art[4])){
						print '<em>' . $art[1] . '</em> has been created.';
						//get email addresses for all admins
						$alladmins = $ObjCHK->get_admin_emails();
						$this->email($alladmins, "New Article", '
							Dear admin,
							
							A new article by the title: ' . $art[1] . ' has just been created.
							Please login and read it on https://amasy.000webhostapp.com
							
							Regards,
							
							AMASY 2017.
						');
					} else {
						print 'Error: Unable to create article: <em>' . $art[1] . '</em>';
					}
					$this->main_content_close();
				}
			}
			if(isset($_GET['no_article_selected'])){
				$this->main_content_open();
				print 'You have not selected any <em>article</em>';
				$this->main_content_close();
			}
			//edit selected articles
			if(isset($_POST[$_SESSION['readers']['articles']['actions']['r']['e']])){
				if(!isset($_POST[$_SESSION['readers']['articles']['selected']])){
					header('Location: ./?no_article_selected');
					exit;
				}
				$this->main_content_open();
				$FM = new amasy_forms();
				$editedarticles = [];
				?><form action="./" method="post" name="articleedit" enctype="multipart/form-data"><?php
					foreach($_POST[$_SESSION['readers']['articles']['selected']] as $key=>$value){
						for($i=0; $i<count($_SESSION['articles']); $i++){
							if($value === $_SESSION['articles'][$i]['article_title']){
								$FM->fm_article_update($_SESSION['articles'][$i]);
								array_push($editedarticles, $_SESSION['articles'][$i]);
							}
						}
					}
					$_SESSION['editedarticles'] = $editedarticles;//the articles selected for update
					unset($editedarticles);
				?>
				<div>
					&nbsp;<input type="submit" name="<?php echo $_SESSION['readers']['buttons']['r']['aupdate']; ?>" value="<?php echo $_SESSION['lang']['forms']['buttons']['v']['update']; ?>" />&nbsp;&nbsp;
					<input type="submit" name="<?php echo $_SESSION['readers']['buttons']['r']['cancel']; ?>" value="<?php echo $_SESSION['lang']['forms']['buttons']['v']['cancel']; ?>" />
				</div>
				</form>
				<?php
				$this->main_content_close();
			}
			//update the edited articles
			if(isset($_POST[$_SESSION['readers']['buttons']['r']['aupdate']])){
				$aupd = [
					$_POST[$_SESSION['readers']['article']['r']['ft']],
					$_POST[$_SESSION['readers']['article']['r']['or']],
					$_POST[$_SESSION['readers']['article']['r']['di']]
				];
				for($i=0; $i<count($_SESSION['editedarticles']); $i++){
					//create an updated article, art
					$art = [
						$_SESSION['editedarticles'][$i]['author_id'],
						$_SESSION['editedarticles'][$i]['article_title'],
						$aupd[0][$i],
						$aupd[1][$i],
						$aupd[2][$i]
					];
					// now update the database with art
					$ObjCHK = new db_check();
					if($ObjCHK->update_article($art[0], $art[1], $art[2], $art[4], $art[3])){
						//success
						//header('Location: ./?manage_articles');
					} else {
						//error
						//header('Location: ./');
					}
				}
			}
			//delete selected articles
			if(isset($_POST[$_SESSION['readers']['articles']['actions']['r']['d']])){
				if(!isset($_POST[$_SESSION['readers']['articles']['selected']])){
					header('Location: ./?no_article_selected');
				}
				$isdeleted = false;
				$ObjCHK = new db_check();
				for($i=0; $i<count($_SESSION['articles']); $i++){
					foreach($_POST[$_SESSION['readers']['articles']['selected']] as $key=>$value){
						if(in_array($value, $_SESSION['articles'][$i])){
							if($ObjCHK->delete_article($_SESSION['articles'][$i]['author_id'] ,$value)){
								$isdeleted = true;
							} else {
								$isdeleted = false;
							}
						}
					}
				}
				if($isdeleted){
					header('Location: ./?manage_articles');
				} else {
					$this->main_content_open();
					print 'Failed to delete due to an error<br /><br />';
					$this->main_content_close();
				}
			}
		}
		public function check_download_req(){
			//check if the user clicked on a pdf or txt link for an article
			if(isset($_SESSION['articles'])){
				for($i=0; $i<count($_SESSION['articles']); $i++){
					if(isset($_POST[str_replace(" ", "", $_SESSION['articles'][$i]['article_title']) . '_pdf_'])){
						$_SESSION["articlepdf"] = $_SESSION['articles'][$i];
						header('Location: export.php?article_pdf');
					}
					if(isset($_POST[str_replace(" ", "", $_SESSION['articles'][$i]['article_title']) . '_txt_'])){
						$_SESSION["articletxt"] = $_SESSION['articles'][$i];
						header('Location: export.php?article_txt');
					}
					if(isset($_GET[str_replace(" ", "", $_SESSION['articles'][$i]['article_title']) . '_pdf_'])){
						###TODO: Display a modal instead...
						$_SESSION["articlepdf"] = $_SESSION['articles'][$i];
						header('Location: export.php?article_pdf');
					}
				}
			}
		}
		private function email($address, $subject, $message){
			require_once $_SESSION['readers']['site']['mailer'] . "PHPMailerAutoload.php";
			$mail = new PHPMailer();
			$mail->SMTPDebug = 0;
			$mail->isSMTP();
			$mail->Host = "smtp.gmail.com";
			$mail->SMTPAuth = true;
			$mail->Username = "auth.amasy@gmail.com";
			$mail->Password = "amasy!2017!";
			$mail->isSMTPSecure = "tls";
			$mail->Port = 587;
			$mail->From = "auth.amasy@gmail.com";
			$mail->FromName = "AMASY " . date('Y', time());
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
				$response  = false;
			} else {
				$response = true;
			}
			return $response;
		}
	}
?>