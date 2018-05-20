<?php
	$reader = [
		'script' => [
			'jquery'	=> 'jquery.min.js', // must be included before bootstrap's JS
			'bootstrap' => 'bootstrap.min.js',
			'clock'		=> 'Clock.js',
			'main'		=> 'main.js',
		],
		'style'	 => [
			'bootstrap'	=> 'bootstrap.min.css',
			'main'		=> 'main.css',
		],
		'lib'	=> [
			'mailer'	=> 'phpmailer',
		],
		'login' => [
			'label' => 'Login form',
			'email' => [
				'label' => 'Email address',
				'name'	=> 'ulea',
			],
			'password' => [
				'label' => 'Password',
				'name'	=> 'ulp',
			],
			'submit'	=> [
				'label'	=> 'Login',
				'name'	=>	'uls',
			],
		],
		'newuser' => [
			'label' => 'Registration form',
			'id' => [
				'label' => 'User ID',
				'name'	=> 'uid',
			],
			'firstname' 	=> [
				'label' => 'First name',
				'name'	=> 'ufn',
			],
			'lastname' 		=> [
				'label' => 'Last name',
				'name'	=> 'uln',
			],
			'gender' 		=> [
				'label' => 'Gender',
				'name'	=> 'ugen',
			], 
			'password' 		=> [
				'label' => 'Enter password',
				'name'	=> 'up',
			], 
			'passwordc'		=> [
				'label' => 'Confirm password',
				'name'	=> 'upc',
			],
			'email' 		=> [
				'label' => 'Email address',
				'name'	=> 'uea',
			], 
			'telephone' 	=> [
				'label' => 'Telephone',
				'name'	=> 'utel',
			], 
			'role' 			=> [
				'label' => 'Role',
				'name'	=> 'urol',
			], 
			'status' 		=> [
				'label' => 'Status',
				'name'	=> 'usta',
			], 
			'profileimage' 	=> [
				'label' => 'Profile photo',
				'name'	=> 'upi',
			], 
			'lastaccess' 	=> [
				'label' => 'Last seen',
				'name'	=> 'ulsn',
			], 
			'lastip' 		=> [
				'label' => 'Last IP Address',
				'name'	=> 'ulip',
			],
			'submit'		=> [
				'label' => 'Register',
				'name'	=> 'snewuser',
			],
		],
		'vehicle' => [
			'label' => 'Vehicle details',
			'regnumber' => [
				'label'	=> 'Number plate',
				'name'	=> 'vnp',
			],
			'model'	=> [
				'label' => 'Model',
				'name'	=> 'vm',
			], 
			'capacity' => [
				'label'	=> 'Capacity',
				'name'	=> 'vc',
			],
			'driver'	=> [
				'label'	=> 'Driver',
				'name'	=> 'vdr',
			]
		],
		'ride' => [
			'label' => 'Ride Details',
			'labelfuture' => 'Future Rides List',
			'give' => 'Give a Ride',
			'find' => 'Find a Ride',
			'id' => [
				'label'	=> 'ID',
			],
			'origin'	=> [
				'label' => 'Origin',
				'name'	=> 'rorig',
			], 
			'destination' => [
				'label'	=> 'Destination',
				'name'	=> 'rdest',
			],
			'status'	=> [
				'label' => 'Status',
			],
			'dateoffered'	=> [
				'label'	=> 'Date',
			],
			'vehicle'	=> [
				'label'	=> 'Vehicle',
			],
			'driver'	=> [
				'label'	=> 'Driver',
			],
			'submit'	=> [
				'label'	=> 'Give a ride',
				'name'	=> 'rsubm',
			],
		],
		'booking'	=> [
			'id'	=> [
				'label'	=> 'ID',
			],
			'datebooked'	=> [
				'label'	=> 'Date',
			],
			'ride'	=> [
				'label'	=> 'Ride',
			],
			'user'	=> [
				'label' => 'User',
			],
		],
		
	];
?>