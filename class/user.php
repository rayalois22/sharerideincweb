
<?php 
	class user {
		public $id;
		public $firstName;
		public $lastName;
		protected $gender;
		protected $password;
		protected $emailAddress;
		protected $telephone;
		protected $role;
		protected $status;
		protected $profileImage;
		protected $lastAccess;
		protected $lastIP;
		
		public function __construct($id, $firstName, $lastName, $gender, $password, $emailAddress, $telephone, $role = 1, $status = 0, $profileImage = 'default.png', $lastAccess = null, $lastIP = null){
			if($lastAccess == null){
				$lastAccess = date("YmdHis", time());
			}
			if($lastIP == null){
				$lastIP = $_SERVER['REMOTE_ADDR'];
			}
			if($id == null){
				$id = '';
			}
			$this->id =$id;
			$this->firstName = $firstName;
			$this->lastName = $lastName;
			$this->gender = $gender;
			$this->password = $password;
			$this->emailAddress = $emailAddress;
			$this->telephone = $telephone;
			$this->role = $role;
			$this->status = $status;
			$this->profileImage = $profileImage;
			$this->lastAccess = $lastAccess;
			$this->lastIP = $lastIP;
		}
		
		// the mutators
		public function setId($id){
			$this->id = $id;
		}
		public function setFirstName($name){
			$this->firstName = $name;
		}
		public function setLastName($name){
			$this->LastName = $name;
		}
		public function setGender($gender){
			$this->gender = $gender;
		}
		public function setPassword($password){
			$this->password = $password;
		}
		public function setEmailAddress($email){
			$this->emailAddress = $email;
		}
		public function setTelephone($telephone){
			$this->telephone = $telephone;
		}
		public function setRole($role){
			$this->role = $role;
		}
		public function setStatus($status){
			$this->status = $status;
		}
		public function setLastAccess($lastAccess){
			$this->lastAccess = $lastAccess;
		}
		public function setLastIP($IPAdress){
			$this->lastIP = $IPAdress;
		}
		public function setProfileImage($profileImage){
			$this->profileImage = $profileImage;
		}
		
		// the accessors
		public function getId(){
			return $this->id;
		}
		public function getFirstName(){
			return $this->firstName;
		}
		public function getLastName(){
			return $this->lastName;
		}
		public function getGender(){
			return $this->gender;
		}
		public function getPassword(){
			return $this->password;
		}
		public function getEmailAddress(){
			return $this->emailAddress;
		}
		public function getTelephone(){
			return $this->telephone;
		}
		public function getRole(){
			return $this->role;
		}
		public function getStatus(){
			return $this->status;
		}
		public function getLastAccess(){
			return $this->lastAccess;
		}
		public function getProfileImage(){
			return $this->profileImage;
		}
		public function getLastIP(){
			return $this->lastIP;
		}	
	}
	
?>