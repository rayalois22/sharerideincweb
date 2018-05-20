<?php 
	/*
		*** By Rayalois ***
		********************
		RESPONSIBLE FOR THE ENCRYPTION/DECRYPTION OF PASSWORDS
	*/
	class blowfish{
		/*
			``````
			``````NOTE:
			``````THE DATABASE FIELD FOR THE PASSWORD HASH MUST BE LONG ENOUGH TO
			``````ENSURE THAT THE HASH IS NOT TRUNCATED AND LEFT USELESS!(length: 60).
			``````
		*/
		public function encrypt_password($password){ 
			//checks if the php installation supports blowfish encryption
			if(defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH){
				//blowfish is enabled
				if(version_compare(PHP_VERSION, "5.5.0", ">=")){
					//PHP 5.5.0+ has built-in function for generating password hashes using bcrypt by default
					$enc = $this->the_bcrypt($password);
					return $enc;
				} else {
					function the_bcrypt($password, $cost = 7){
						$salt = "";
						$arr_salt_chars = array_merge( range('A','Z'), range('a','z'), range(0,9) );
						for($i=0; $i < 22; $i++){
							$salt .= $arr_salt_chars[array_rand($arr_salt_chars)];
						}
						if(version_compare(PHP_VERSION, "5.3.7", "<")){
							//crypt function automatically detects blowfish as the encryption type if the salt starts with $2a$, followed by a 2 digit cost parameter and 22 digits from the alphabet.
							return crypt($password, sprintf('$2a$%02d$', $cost) . $salt);
						} else {
							//For security reasons, php.net advises the use of $2y$ for versions >= 5.3.7
							return crypt($password, sprintf('$2y$%02d$', $cost) . $salt);
						}
					}
					$enc = the_bcrypt($password);
					return $enc;
				}
			} else{
				//blowfish is not enabled
				//default back to the basic crypt function
				$enc = crypt($password);
				return $enc;
			}
		}
		
		public function isPassword($password, $password_hash){
			if(version_compare(PHP_VERSION, "5.5.0", ">=")){
				//there's a built-in function
				if(password_verify($password, $password_hash)){
					//password is correct
					return true;
				} else {
					//password is incorrect
					return false;
				}
			} else {
				if(crypt($password, $password_hash) == $password_hash){
					//password is correct
					return true;
				} else {
					//password is incorrect
					return false;
				}
			}
		}
		
		public function the_bcrypt($password, $rounds = 10){
			$crypt_params = array(
				'cost' => $rounds
			);
			//the built-in function is ```password_hash($password, $encryption_type, $crypt_parameters)```````````
			return password_hash($password, PASSWORD_BCRYPT, $crypt_params);
		}
	}
?>