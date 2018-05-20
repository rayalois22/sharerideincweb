<?php	
	// the global configuration array
	$CONF = [
		'db' => [
			'driver' => 'mysql',
			'port' => 3306,
			// utf8mb4 supports storage of characters outside the Basic Multilingual Plane(BMP), 
			// eg. newly introduced emojis and symbols.
			'charset' => 'utf8mb4',
			'opt' => [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
			],
		],
		'timezone' => 'Africa/Nairobi',
		'site' => [
			'title' => 'Shareride Inc.',
		],
	];
	// obtains the CLEARDB database credentials on Heroku.
	$CONF['db']['url'] = parse_url(getenv("CLEARDB_DATABASE_URL"));
	
	// database credentials on development environment on localhost
	//$CONF['db']['url'] = ['host' => 'localhost','user' => 'root','pass' => 'root','path' => '0shareride',];
	
	$CONF['site']['copyright'] = 'Copyright &copy;' . date('Y') . ' - ' . $CONF['site']['title'];
	$CONF['site']['url'] = 'https://sharerideincweb.herokuapp.com';
	
	//host: us-cdbr-iron-east-04.cleardb.net
	//database: heroku_b48f610ebfce970
	//Username:	bbe6013ca3a5d2
	//Password:	032ad7c9 (Reset)
	// mysql://bbe6013ca3a5d2:032ad7c9@us-cdbr-iron-east-04.cleardb.net/heroku_b48f610ebfce970?reconnect=true
	
?>