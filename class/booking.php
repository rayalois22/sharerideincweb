<?php
	class booking{
		protected $id;
		protected $dateBooked;
		protected $ride;
		protected $user;
		
		public function __construct($id, $dateBooked, $ride, $user){
			if($id == null){
				$id = '';
			}
			if($dateBooked == null){
				$dateBooked = date("YmdHis", time());
			}
			$this->id = $id;
			$this->dateBooked = $dateBooked;
			$this->ride = $ride;
			$this->user = $user;
		}
		
		//accessors
		public function getId(){
			return $this->id;
		}
		public function getDateBooked(){
			return $this->dateBooked;
		}
		public function getRide(){
			return $this->ride;
		}
		public function getUser(){
			return $this->user;
		}
	}
?>