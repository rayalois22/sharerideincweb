<?php 
	date_default_timezone_set("Africa/Nairobi");
	/* 
		CHECKS FOR OR STORES CONTENT IN THE DATABASE
	*/
	class db_check{
		private $connection;
		public function __construct(){
			$db = new db_connection();
			$this->connection = $db->connect();
			//print_r($this->connection);
		}
		public function lastaccess($userid){
			if($statement = $this->connection->prepare("UPDATE `users` SET `access_time` = ? WHERE `user_id` = ?")){
				$accesstime = date("YmdHis", time());
				if($statement->bind_param("ss", $accesstime, $userid)){
					if($statement->execute()){
						if(!$this->connection->errno){
							//success
							return true;
						} else {
							//error
							return false;
						}
					} else {
						//error
						return false;
					}
				} else {
					//error
					return false;
				}
			} else {
				//error
				return false;
			}
		}
		public function is_user_present($username){
			if($statement = $this->connection->prepare("SELECT * FROM `users` WHERE `user_name` = ?")){
				if($statement->bind_param("s", $username)){if($statement->execute()){
					$statement->store_result();
					if($statement->num_rows > 0){
						//user exists already
						$statement->close();
						return true;
					} else {
						//user does not exist
						$statement->close();
						return false;
					}
				} else return false;} else {return false;}
			}else {return false;}
		}
		public function is_article_present($author_id, $article_title){
			$statement = $this->connection->prepare("SELECT * FROM `articles` WHERE `author_id` = ? AND `article_title` = ?");
			$statement->bind_param("ss", $author_id, $article_title);
			$statement->execute();
			$statement->store_result();
			if($statement->num_rows > 0){
				//article exists already
				$statement->close();
				return true;
			} else {
				//article does not exist
				$statement->close();
				return false;
			}
		}
		public function get_password_hash($username){
			$statement = $this->connection->prepare("SELECT `password` FROM `users` WHERE `user_name` = ?");
			$statement->bind_param("s", $username);
			$statement->execute();
			$statement->bind_result($token1);
			while($statement->fetch()){
				$password_hash = $token1;
			}
			$statement->close();
			return $password_hash;
		}
		public function get_admin_emails(){
			$emails = array();
			if($statement = $this->connection->prepare("SELECT `email` FROM `users` WHERE `user_type` IN ('admin')")){
				if($statement->execute()){
					if($statement->bind_result($token)){
						if($statement->store_result()){
							//visit each row of the resultset
							for($i=0; $i<$statement->num_rows; $i++){
								//move the result pointer to the current row
								$statement->data_seek($i);
								//fetch the field values in the current row into the bound variables
								$statement->fetch();
								array_push($emails, $token);
							}
						}
					}
				}
			}
			return $emails;
		}
		public function get_user_type($username){
			$statement = $this->connection->prepare("SELECT `user_type` FROM `users` WHERE `user_name` = ?");
			$statement->bind_param("s", $username);
			$statement->execute();
			$statement->bind_result($token1);
			while($statement->fetch()){
				$user_type = $token1;
			}
			$statement->close();
			return $user_type;
		}
		public function create_new_user($fullname, $email, $phonenumber, $username, $password, $usertype, $profileimage, $address){
			$accesstime = date('YmdHis', time());
			$userid = "";
			$statement = $this->connection->prepare("INSERT INTO `users`(`user_id`, `full_name`, `email`, `phone_number`, `user_name`, `password`, `user_type`, `access_time`, `profile_image`, `address`) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$statement->bind_param("ssssssssss", $userid, $fullname, $email, $phonenumber, $username, $password, $usertype, $accesstime, $profileimage, $address);
			$result = $statement->execute();
			$statement->close();
			if( $this->is_user_present($username) ){
				//user was registered successfully
				return true;
			} else {
				//unable to register user
				return false;
			}
		}
		public function create_new_article($id, $title, $text, $display, $order){
			$createddate = date('YmdHis', time());
			$lastupdate = date('YmdHis', time());
			if($statement = $this->connection->prepare("INSERT INTO `articles`(`author_id`, `article_title`, `article_full_text`, `article_created_date`, `article_last_update`, `article_display`, `article_order`) VALUES(?, ?, ?, ?, ?, ?, ?)")){
				$statement->bind_param("sssssss", $id, $title, $text, $createddate, $lastupdate, $display, $order);
				$statement->execute();
				$statement->close();
			}
			//now confirm that the article exists
			if($this->is_article_present($id, $title)){
				// article created successfully
				return true;
			} else {
				//error
				return false;
			}
		}
		public function list_users($this_user_type){
			$arr_results = [];
			switch ($this_user_type){
				case "super":
					$statement = $this->connection->prepare("SELECT * FROM `users` WHERE `user_type` IN ('admin', 'autha')");
					$statement->execute();
					$meta = $statement->result_metadata();
					while ( $rows = $meta->fetch_field() ){
						$params[] = &$row[$rows->name];
					}
					call_user_func_array(array($statement, 'bind_result'), $params);
					while ( $statement->fetch() ){
						$user = array();
						foreach( $row as $key=>$value ){
							$user[$key] = $value;
						}
						$arr_results[] = $user;
					}
					$statement->close();
					break;
				case "admin":
					$statement = $this->connection->prepare("SELECT * FROM `users` WHERE `user_type` IN ('autha')");
					$statement->execute();
					$meta = $statement->result_metadata();
					while ( $rows = $meta->fetch_field() ){
						$params[] = &$row[$rows->name];
					}
					call_user_func_array(array($statement, 'bind_result'), $params);
					while ( $statement->fetch() ){
						$user = array();
						foreach( $row as $key=>$value ){
							$user[$key] = $value;
						}
						$arr_results[] = $user;
					}
					$statement->close();
					break;
				default:
					break;
			}
			return $arr_results;
		}
		//list of all articles
		public function list_articles(){
			$arr_results = [];
			if($statement = $this->connection->prepare("SELECT * FROM `articles`")){
				if($statement->execute()){
					$meta = $statement->result_metadata();
					while ( $rows = $meta->fetch_field() ){
						$params[] = &$row[$rows->name];
					}
					if(call_user_func_array(array($statement, 'bind_result'), $params)){
						while ( $statement->fetch() ){
							$article = array();
							foreach( $row as $key=>$value ){
								$article[$key] = $value;
							}
							$arr_results[] = $article;
						}
						$statement->close();
					}
				}
			}
			return $arr_results;
		}
		public function delete_users($user_name){
			$statement = $this->connection->prepare("DELETE FROM `users` WHERE `user_name` = ?");
			$statement->bind_param("s", $user_name);
			if($statement->execute()){
				$statement->close();
				return true;
			} else {
				$statement->close();
				return false;
			}
		}
		public function update_user($userid, $username, $fullname, $email, $phonenumber, $password, $profileimage, $address){
			// check whether a new password has been set, then update the database accordingly
			if(!empty($password)){
				// change the password
				$statement = $this->connection->prepare("UPDATE `users` SET `full_name` = ?, `email` = ?, `phone_number` = ?, `password` = ?, `profile_image` = ?, `address`=? WHERE `user_id`=? AND `user_name`=?");
				$statement->bind_param("ssssssss", $fullname, $email, $phonenumber, $password, $profileimage, $address, $userid, $username);
			} else {
				// do not change the user's password
				$statement = $this->connection->prepare("UPDATE `users` SET `full_name` = ?, `email` = ?, `phone_number` = ?, `profile_image` = ?, `address` = ? WHERE `user_id` = ? AND `user_name` = ?");
				$statement->bind_param("sssssss", $fullname, $email, $phonenumber, $profileimage, $address, $userid, $username);
			}
			if($statement->execute()){
				$statement->close();
				return true;
			} else {
				$statement->close();
				return false;
			}
		}
		public function update_article($author, $title, $text, $display, $order){
			$statement = $this->connection->prepare("UPDATE `articles` SET `article_full_text` = ?, `article_last_update` = ?, `article_display` = ?, `article_order` = ? WHERE `author_id` = ? AND `article_title` = ?");
			$lastupdate = date('YmdHis', time());
			$statement->bind_param("ssssss", $text, $lastupdate, $display, $order, $author, $title);
			if($statement->execute()){
				if(!$this->connection->errno){
					//success
					return true;
				} else {
					//error
					return false;
				}
			} else {
				//error
				return false;
			}
		}
		public function delete_article($author, $title){
			$statement = $this->connection->prepare("DELETE FROM `articles` WHERE `article_title` = ? AND `author_id` = ?");
			$statement->bind_param("ss", $title, $author);
			if($statement->execute()){
				if(!$this->connection->errno){
					//success
					return true;
				} else {
					//error
					//print $this->connection->error;
					return false;
				}
			} else {
				//error
				//print $this->connection->error;
				return false;
			}
		}
		//delete user
		public function delete_user($userid, $username){
			if($statement = $this->connection->prepare("DELETE FROM `users` WHERE `user_name` = ? AND `user_id` = ?")){
				if($statement->bind_param("ss", $username, $userid)){
					if($statement->execute()){
						if(!$this->connection->errno){
							//success
							return true;
						} else {
							//error
							return false;
						}
					} else {
						//error
						return false;
					}
				} else {
					//error
					return false;
				}
			} else {
				//error
				return false;
			}
		}
		public function get_author_name($userid){
			$error = false;
			if($statement = $this->connection->prepare("SELECT `full_name`, `user_name` FROM `users` WHERE `user_id` = ?")){
				if($statement->bind_param("s", $userid)){
					if($statement->execute()){
						if(!$this->connection->errno){
							$statement->bind_result($token1, $token2);
							while($statement->fetch()){
								$author['full_name'] = $token1;
								$author['user_name'] = $token2;
							}
							$statement->close();
						} else{
							$error = true;
						}
					} else {$error = true;}
				} else {$error = true;}
			} else {$error = true;}
			if($error){
				return $error;
			} else {
				return $author;
			}
		}
		public function get_the_user($username){
			$statement = $this->connection->prepare("SELECT * FROM `users` WHERE `user_name` = ?");
			$statement->bind_param("s", $username);
			$statement->execute();
			$statement->bind_result($token1, $token2, $token3, $token4, $token5, $token6, $token7, $token8, $token9, $token10);
			while($statement->fetch()){
				switch($token7){
					case "super":
						$user = new super_user($token2, $token3, $token4, $token5, $token6, $token9, $token10);
						$user->set_user_id($token1);
						break;
					case "admin":
						$user = new admin_user($token2, $token3, $token4, $token5, $token6, $token9, $token10);
						$user->set_user_id($token1);
						break;
					case "autha":
						$user = new auth_user($token2, $token3, $token4, $token5, $token6, $token9, $token10);
						$user->set_user_id($token1);
						break;
					default:
						break;
				}
			}
			$statement->close();
			return $user;
		}
		public function upload_profimage($image, $username){
			$str_file_name = $image[0];
			$arr_file_name = explode(".", $str_file_name);
			$profimage_ext = end($arr_file_name);
			$profimage_types = array("image/jpg", "image/JPG", "image/png", "image/PNG", "image/jpeg", "image/JPEG");
			$profimage_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../res/images/upload/profiles/';
			$profimage_name = mt_rand(10000, 99000) . '_' . $username . '_' . '.';
			$profimage_dest = $profimage_dir . $profimage_name . $profimage_ext;
			if( in_array($image[1], $profimage_types) ){
				if(isset($image[1])){
					if(move_uploaded_file($image[2], $profimage_dest)){
						$profimage = $profimage_name . $profimage_ext;
					} else {
						$profimage = "default.png";
					}
				} else {
					$profimage = "default.png";
				}
			} else {
				$profimage = "default.png";
			}
			return $profimage;
		}
		public function get_relation($value){
			$relation = "";
			$statement_u = $this->connection->prepare("SELECT * FROM `users` WHERE `user_name`=?");
			$statement_a = $this->connection->prepare("SELECT * FROM `articles` WHERE `article_title`=?");
			$statement_u->bind_param("s", $value);
			$statement_a->bind_param("s", $value);
			$statement_u->execute();
			$statement_u->store_result();
			$statement_a->execute();
			$statement_a->store_result();
			if($statement_u->num_rows > 0){
				$relation = "users";
			} else if($statement_a->num_rows > 0){
				$relation = "articles";
			}
			return $relation;
		}
	}
?>
