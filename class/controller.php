<?php
	class controller{
		//$serverUrl = 'https://sharerideincweb.herokuapp.com';
		protected $Conf;
		protected $Template;
		protected $View;
		protected $Model;
		protected $User;
		protected $Data;
		
		public function __construct($ConfigurationManager, $TemplateManager, $LayoutManager, $ModelManager){
			//initializes the core components.
			$this->Conf = $ConfigurationManager;
			$this->Template = $TemplateManager;
			$this->View = $LayoutManager;
			$this->Model = $ModelManager;
		}
		
		public function control(){
			// gets all the available data necessary to run the application efficiently.
			// this data is updated whenever the browser window refreshes: see the setSessionData() method in /class/process.php 
			$this->Model->setSessionData();
			$this->Data['users'] = unserialize($_SESSION['data']['users']);
			$this->Data['vehicles'] = unserialize($_SESSION['data']['vehicles']);
			$this->Data['rides'] = unserialize($_SESSION['data']['rides']);
			$this->Data['futurerides'] = unserialize($_SESSION['data']['futurerides']);
			$this->Data['bookings'] = unserialize($_SESSION['data']['bookings']);
			
			//get the forms
			$SF = new shareride_form();
			
			// check that we are on the index page
			if((basename($_SERVER['PHP_SELF'], '.php') == 'index')){
				if(isset($_GET['Flogin'])){
					//notify user of failed login attempt
					header('Location: ./?login');
					exit();
				}
				//checks whether the user is logged in
				if(isset($_SESSION['SESS_USER'])){
					//assigns the logged in user to the current user member 
					$this->User = unserialize($_SESSION['SESS_USER']);
					
					/**
					******************************************************
					LISTENS FOR THE FOLLOWING EVENTS
					..................................
					1. Logout request.
					2. Give-a-ride submissions.
					3. Book-a-ride requests.
					
					******************************************************
					*/
					
					//checks whether the user has logged out
					if(isset($_GET['logout'])){
						unset($_SESSION['SESS_USER']);
						header('Location: ./');
						exit();
					}
/*
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
CHECKS FOR NEW RIDE SUBMISSIONS
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
*/
//checks for new ride submissions
if(isset($_POST[$_SESSION['reader']['ride']['submit']['name']])){
	// 1. First make sure all required fields have valid values.					
	$response = $this->validateRide($_POST, ['error' => false, 'message' => '',]);
	// if all submitted values are valid, then proceed to process the data
	if(!$response['error']){
		// create the vehicle
		if($this->Model->newVehicle(new vehicle(
			$_POST[$_SESSION['reader']['vehicle']['regnumber']['name']],
			$_POST[$_SESSION['reader']['vehicle']['model']['name']],
			$_POST[$_SESSION['reader']['vehicle']['capacity']['name']],
			$this->User->getId()
		))){
			//vehicle created successfully, now create a new ride.
			if($this->Model->newRide(new ride(null,
				$_POST[$_SESSION['reader']['ride']['origin']['name']],
				$_POST[$_SESSION['reader']['ride']['destination']['name']],
				0, null, 
				$_POST[$_SESSION['reader']['vehicle']['regnumber']['name']], 
				$this->User->getId()
			))){
				// success: ride added
				// redirect to find-a-ride page
				header('Location: ./?find-a-ride&&success=true&&message=Ride created successfully and is available for booking!');
				exit();
			} else {
				// unable to add the ride...ride exists already
				// notify user
				header('Location: ./?give-a-ride&&error=true&&message=Ride exists already!');
				exit();
			}
		} else {
			//the vehicle already exists.
			
			// determines whether or not to create a ride with this vehicle.
			$FLAG_CREATE_RIDE = true;
			$FLAG_CREATE_RIDE_MSG = '';
			
			//check if it belongs to this user
			foreach($this->Data['vehicles'] as $vk => $vv){
				if($_POST[$_SESSION['reader']['vehicle']['regnumber']['name']] == $vv->getRegNumber()){
					if($vv->getDriver() == $this->User->getId()){
						//the user owns this vehicle, so they can use it to offer other rides.
						//but they must not have any future ride where they are using this vehicle with the same origin and destination being submitted
						//now check whether the vehicle is not associated with any future ride with the same origin and destination being submitted.
						foreach($this->Data['futurerides'] as $frk => $frv){
							//get all the future rides where this vehicle has been used.
							if($frv->getVehicle() == $vv->getRegNumber()){
								//this future ride uses this vehicle.
								//a new ride must have at least a different destination or origin
								if((trim($frv->getDestination()) == trim($_POST[$_SESSION['reader']['ride']['destination']['name']])) && 
									(trim($frv->getOrigin()) == trim($_POST[$_SESSION['reader']['ride']['origin']['name']]))){
									// a similar ride exists already
									$FLAG_CREATE_RIDE = false;
									$FLAG_CREATE_RIDE_MSG = 'You have another future ride with the same origin and destination already!';
									break 2;
								}
							}
						}
					} else {
						// this vehicle belongs to someone else in the system.
						// can't use it to create a ride.
						$FLAG_CREATE_RIDE = false;
						$FLAG_CREATE_RIDE_MSG = 'This vehicle belongs to someone else in the system!';
						break;
					}
				}
			}
			// create ride
			if($FLAG_CREATE_RIDE){
				//create this ride.
				if($this->Model->newRide(new ride(null,
					$_POST[$_SESSION['reader']['ride']['origin']['name']],
					$_POST[$_SESSION['reader']['ride']['destination']['name']],
					0, null, 
					$_POST[$_SESSION['reader']['vehicle']['regnumber']['name']], 
					$this->User->getId()
				))){
					// success: ride added
					// redirect to find-a-ride page.
					header('Location: ./?find-a-ride&&success=true&&message=Ride created successfully and is available for booking!');
					exit();
				} else {
					// unable to add the ride. 
					// a similar ride already exists.
					header('Location: ./?give-a-ride&&error=true&&message=A similar ride exists already!');
					exit();
				}
			} else {
				header('Location: ./?give-a-ride&&error=true&&message='. $FLAG_CREATE_RIDE_MSG .'&&ln='. __LINE__);
				exit();
			}
		}
		
	} else {
		// notify user of invalid ride details.
		// do not empty the form.
		header('Location: ./?give-a-ride&&error=true&&message='.$response['message']);
		exit();
	}
}
/*
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
CHECKS FOR BOOKING REQUESTS
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
*/
//checks for booking requests
if(isset($_GET['book'])){
	//stores the result for notification purposes.
	$result = [
		'success' => false,
		'message' => '',
	];
	if($this->Model->bookRide(new booking(null, null,
		$_GET['id'], $this->User->getId()
	))){
		// successfully booked the ride.
		$result['success'] = true;
		$result['message'] = 'Success, you have booked the ride.';
		
		//notifies the user of successful booking via mail.
		foreach($this->Data['rides'] as $ridek => $ridev){
			if($_GET['id'] == $ridev->getId()){
				// get the ride that was booked.
				$ride = $ridev;
				//gets the booking
				$booking = $this->Model->viewBooking($ride->getId())[0];
					
				// gets the driver
				foreach($this->Data['users'] as $userk => $user){
					if($ride->getDriver() == $user->getId()){
						$driver = $user;
					}
				}
				// gets the vehicle
				foreach($this->Data['vehicles'] as $vk => $vv){
					if($ride->getVehicle() == $vv->getRegNumber()){
						$vehicle = $vv;
					}
				}
			}
		}
		
		//send booking confirmation email
		if(($mail = $this->Template->mail_booking_confirmation($booking, $this->User, $ride, $vehicle, $driver)) != null){
			if(!$this->Model->sendMail($this->User->getEmailAddress(),
				$mail['subject'], $mail['body'])){
				// unable to send mail
			} else {
				// mail sent successfully
				$result['message'] = 'Success. You have booked ther ride and a confirmation email has been sent to you with the details.';
			}
		}
		
		// removes the ride from the list of future rides.
		foreach($this->Data['rides'] as $ridek => $ride){
			// finds the ride that was booked 
			if($_GET['id'] == $ride->getId()){
				// changes the status of the ride to make it a past ride since it is booked already.
				$ride->setStatus(1);
				// persists the change in the database.
				if($this->Model->updateRide($ride)){
					// successfully updated the ride, so redirect
					header('Location: ./?bookingSuccess');
					exit();
				}
			}
		}
	}
}
					
/*
............................................................................................................
@@@@@@@@@@@@@@@@@@@@
BEGINNING OF PAGE
@@@@@@@@@@@@@@@@@@@@
............................................................................................................
*/
/*
1. @@LOGIN = TRUE
*/

$this->View->shareride_head();
$this->View->shareride_navigation(true, true, $this->User);
					
					//checks if the user just logged in then displays a successful login message
					if(isset($_GET['Tlogin'])){
						// TODO: notify user of successful login before redirection.
						
						//redirect to the home page
						header('Location: ./');
						//end script execution
						exit();
					}
					
					// if the user has not requested to give a ride, shows them the available rides they can book
					if(!isset($_GET['give-a-ride'])){
// displays all future rides for user to book
$SF->form_book_ride($this->Data['futurerides'], $this->Data['users'], $this->Data['vehicles']);
$this->View->shareride_footer();
					}
					
					//Displays a Give-a-ride form, if the user wants to give a ride 
					if(isset($_GET['give-a-ride'])){
						$vehicles = [];
						foreach($this->Data['vehicles'] as $vk => $vv){
							if($this->User->getId() == $vv->getDriver()){
								array_push($vehicles, $vv);
							}
						}
						if(isset($_GET['error'])){
// displays the new ride form with server error notifications.
$SF->form_new_ride($vehicles, $_GET['message']);
						} else {
// displays the new ride form.
$SF->form_new_ride($vehicles);
						}
					}
					
					//checks for contact information request
					if(isset($_GET['contact'])){
						// displays contact inormation
					   header('Location: ./?ep=1');
					   exit();
					}
					
					//displays the contact form if the user had requested for it.
					if(isset($_GET['ep'])){
// displays contact information
$this->View->shareride_footer(true);
					} else {
$this->View->shareride_footer();
					}
				} else {
/*
2. @@LOGIN = FALSE
*/
					//clears the user member
					$this->User = null;
					
					//checks whether any resource has been requested
					// checks whether registration form has been submitted
					if(isset($_POST[$_SESSION['reader']['newuser']['submit']['name']])){
						// registers a new user
						if(	!$this->Model->newuser(new user(null,
							$_POST[$_SESSION['reader']['newuser']['firstname']['name']],
							$_POST[$_SESSION['reader']['newuser']['lastname']['name']],
							$_POST[$_SESSION['reader']['newuser']['gender']['name']],
							$_POST[$_SESSION['reader']['newuser']['password']['name']],
							$_POST[$_SESSION['reader']['newuser']['email']['name']],
							$_POST[$_SESSION['reader']['newuser']['telephone']['name']]
						))){
//creates a page to show the user
$this->View->shareride_head();
$this->View->shareride_navigation(true);
echo 'Failed to register!';

$this->View->shareride_footer();
						} else {
							//registration successful

							//notify the user via mail.
							if(($mail = $this->Template->mail_registration_confirmation($_POST[$_SESSION['reader']['newuser']['firstname']['name']] . ' ' . $_POST[$_SESSION['reader']['newuser']['lastname']['name']])) != null){
								if(!$this->Model->sendMail($_POST[$_SESSION['reader']['newuser']['email']['name']],
									$mail['subject'], $mail['body'])){
									// unable to send mail
								} else {
									// mail sent successfully
								}
							}
							
							//automatically login the user following successful registration.
							if(!$this->Model->login($_POST[$_SESSION['reader']['newuser']['email']['name']], $_POST[$_SESSION['reader']['newuser']['password']['name']])){
								// failure
								header('Location: ./?Flogin');
								exit();
							} else {
								// success
								header('Location: ./?Tlogin');
								exit();
							}
						}
					} else if(isset($_POST[$_SESSION['reader']['login']['submit']['name']])){
						//logs in the user
						if(!$this->Model->login($_POST[$_SESSION['reader']['login']['email']['name']], $_POST[$_SESSION['reader']['login']['password']['name']])){
							// failure
							header('Location: ./?Flogin');
							exit();
						} else {
							// success
							header('Location: ./?Tlogin');
							exit();
						}
					} else {
// shows a different page if the user is not logged in.
$this->View->shareride_head();
						$login = isset($_GET['login']);
						if(!isset($_GET['signup']) && !isset($_GET['login'])){
//presents the navigation bar
$this->View->shareride_navigation();
//presents the welcome information to the user
$this->View->welcome();
						}
						if(isset($_GET['signup'])){
//presents the navigation bar
$this->View->shareride_navigation($login);
//presents the registration form
$SF->form_register();
						}
						if(isset($_GET['login'])){
//presents the navigation bar
$this->View->shareride_navigation($login);
// presents the login form
$SF->form_login();
						}
						//checks for contact information request
						if(isset($_GET['contact'])){
							// displays contact inormation
						   header('Location: ./?ep=1');
						   exit();
						}
						if(isset($_GET['ep'])){
// displays contact information
$this->View->shareride_footer(true);
						} else {
$this->View->shareride_footer();
						}						
					}					
				}
			}
			
			//checks whether search form has been submitted
			if(isset($_GET['s'])){
				// query the model, i.e. iterate through all models and their elements, checking for any value that matches the search terms.
				// present search results
			}
		}
		
/*
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
UTILITY FUNCTIONS USED BY THIC CONTROLLER
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
*/
		
		/**
		* Ensures all user submitted data is safe for viewing in an email or a web page.
		*/
		private function safeInput($input){
			$data = trim($input);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
		}
		
		/**
		* Ensures valid ride details.
		*/
		private function validateRide($post, $response){
			foreach($post as $pk => $pv){
				if(empty($pv)){
					switch($pk){
						case $_POST[$_SESSION['reader']['vehicle']['regnumber']['name']]:
							$response['message'] = $_SESSION['reader']['vehicle']['regnumber']['label'] .' is required!';
							$response['error'] = true;
							return $response;
						case $_POST[$_SESSION['reader']['vehicle']['model']['name']]:
							$response['message'] = $_SESSION['reader']['vehicle']['model']['label'] . ' is required!';
							$response['error'] = true;
							return $response;
						case $_POST[$_SESSION['reader']['vehicle']['capacity']['name']]:
							$response['message'] = $_SESSION['reader']['vehicle']['capacity']['label'] . ' is required!';
							$response['error'] = true;
							return $response;
						case $_POST[$_SESSION['reader']['ride']['origin']['name']]:
							$response['message'] = $_SESSION['reader']['ride']['origin']['label'] . ' is required!';
							$response['error'] = true;
							return $response;
						case $_POST[$_SESSION['reader']['ride']['destination']['name']]:
							$response['message'] = $_SESSION['reader']['ride']['destination']['label'] . ' is required!';
							$response['error'] = true;
							return $response;
						default:
							continue;
					}
				} else {
					// check if the value provided is valid
					switch($pk){
						case $_POST[$_SESSION['reader']['vehicle']['regnumber']['name']]:
							if(!preg_match("/([\w ]+)/", $this->safeInput($pv))){
								$response['message'] = $_SESSION['reader']['vehicle']['regnumber']['label'] .' must be a valid vehicle registration number!';
								$response['error'] = true;
								return $response;
							}
						case $_POST[$_SESSION['reader']['vehicle']['model']['name']]:
							if(sizeof($this->safeInput($pv)) < 3){
								$response['message'] = $_SESSION['reader']['vehicle']['model']['label'] . ' must be a valid vehicle model name!';
								$response['error'] = true;
								return $response;
							}
						case $_POST[$_SESSION['reader']['vehicle']['capacity']['name']]:
							if(!preg_match("/^[+]?[1-9]\d*$/", $this->safeInput($pv))){
								$response['message'] = $_SESSION['reader']['vehicle']['capacity']['label'] . ' must be valid number without a leading zero!';
								$response['error'] = true;
								return $response;
							}
						case $_POST[$_SESSION['reader']['ride']['origin']['name']]:
							if(sizeof($this->safeInput($pv)) < 3){
								$response['message'] = $_SESSION['reader']['ride']['origin']['label'] . ' must be a valid name of a place!';
								$response['error'] = true;
								return $response;
							}
						case $_POST[$_SESSION['reader']['ride']['destination']['name']]:
							if(sizeof($this->safeInput($pv)) < 3){
								$response['message'] = $_SESSION['reader']['ride']['destination']['label'] . ' must be a valid name of a place!';
								$response['error'] = true;
								return $response;
							}
						default:
							continue;
					}
				}							
			}
			return $response;
		}
	}
?>
