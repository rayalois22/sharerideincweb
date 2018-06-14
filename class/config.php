<?php 
	class config{
		public function __construct(){
			require_once 'conf.php';
			$this->db_params($CONF);
			$this->site_conf($CONF);
			$this->get_reader();
		}
		private function db_params($CONF){
			$dsn = $CONF['db']['driver'] . ':host=' . $CONF['db']['url']['host'] . ';dbname=' . substr($CONF['db']['url']['path'], 1) . ';port=' . $CONF['db']['port'] . ';charset='. $CONF['db']['charset'] . ';';
			$db_args = [
				$dsn, 
				$CONF['db']['url']['user'], 
				$CONF['db']['url']['pass'], 
				$CONF['db']['opt'],
			];
			//$db_args = array($url["host"], $url["user"], $url["pass"], substr($url["path"], 1));
			$db_fields = ["DSN", "USER", "PASS", "OPT"];
			$db_args = array_combine($db_fields, $db_args);
			define("DB_PARAMS", $db_args);
			//TODO: Remove
			echo DB_PARAMS;
		}
		private function site_conf($CONF){
			define("CONF", [
				'timezone' => $CONF['timezone'],
				'site' => $CONF['site'],
			]);			
		}
		
		private function get_reader(){
			if(!isset($_SESSION['reader'])){
				require_once 'reader.php';
				$_SESSION['reader'] = $reader;
			}
		}
	}
?>
