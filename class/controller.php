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
					
					//checks whether the user has logged out
					if(isset($_GET['logout'])){
						unset($_SESSION['SESS_USER']);
						header('Location: ./');
						exit();
					}
					
					//checks for new ride submissions.
					if(isset($_POST[$_SESSION['reader']['ride']['submit']['name']])){
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
								// ride added successfully.
								header('Location: ./');
								exit();
							} else {
								// unable to add the ride.
							}
						}
					}
					
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
					
					//creates a page to show the user
					$this->View->shareride_head();
					$this->View->shareride_navigation(false, true, $this->User);
					if(isset($_GET['give-a-ride'])){
						$vehicles = [];
						foreach($this->Data['vehicles'] as $vk => $vv){
							if($this->User->getId() == $vv->getDriver()){
								array_push($vehicles, $vv);
							}
						}
						$SF->form_new_ride($vehicles);
					} else {
						if(isset($_GET['Tlogin'])){
							// TODO: notify user of successful login before redirection.
							
							//redirect to the home page
							header('Location: ./');
							//end script execution
							exit();
						}
						// displays all future rides for user to book
						$SF->form_book_ride($this->Data['futurerides'], $this->Data['users'], $this->Data['vehicles']);
						$this->View->shareride_footer();
					}
					$this->View->shareride_footer();
					
					
					
					//show available rides
					//allow the user to give a ride: create or choose vehicle, 
					//allow the user to get a ride
				} else {
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
						$this->View->shareride_navigation(true);
						if(!isset($_GET['signup']) && !isset($_GET['login'])){
							$this->View->welcome();
						}
						if(isset($_GET['signup'])){
							//presents the registration form
							$SF->form_register();
						}
						if(isset($_GET['login'])){
							// presents the login form
							$SF->form_login();
						}
						$this->View->shareride_footer();
					}					
				}
			}
			
			//checks whether search form has been submitted
			if(isset($_GET['s'])){
				// query the model, i.e. iterate through all models and their elements, checking for any value that matches the search terms.
				// present search results
			}
			
			
		}
	}
?>