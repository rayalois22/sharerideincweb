<?php 
	//the connection class
	class connection{
		private $connection;
		public function connect(){
			try{
				return $this->connection = new PDO(DB_PARAMS['DSN'], DB_PARAMS['USER'], DB_PARAMS['PASS'], DB_PARAMS['OPT']);
			} catch(PDOException $e){
				echo 'Error: Unable to connect PDO...';
				return $this->connection = null;
			}			
		}
	}
?>