<?php
	class ride{
		protected $id;
		protected $origin;
		protected $destination;
		protected $status;
		protected $dateOffered;
		protected $vehicle;
		protected $driver;
		
		public function __construct($id, $origin, $destination, $status, $dateOffered, $vehicle, $driver){
			if($id == null){
				$id = '';
			}
			if($dateOffered == null){
				$dateOffered = date("YmdHis", time());
			}
			$this->id = $id;
			$this->origin = $origin;
			$this->destination = $destination;
			$this->status = $status;
			$this->dateOffered = $dateOffered;
			$this->vehicle = $vehicle;
			$this->driver = $driver;
		}
		
		//mutators
		public function setId($id){
			$this->id = $id;
		}
		public function setOrigin($origin){
			$this->origin = $origin;
		}
		public function setDestination($destination){
			$this->destination = $destination;
		}
		public function setStatus($status){
			$this->status = $status;
		}
		public function setDateOffered($date){
			$this->dateOffered = $date;
		}
		public function setVehicle($vehicle){
			$this->vehicle = $vehicle;
		}
		public function setDriver($driver){
			$this->driver = $friver;
		}
		
		//accessors
		public function getId(){
			return $this->id;
		}
		public function getOrigin(){
			return $this->origin;
		}
		public function getDestination(){
			return $this->destination;
		}
		public function getStatus(){
			return $this->status;
		}
		public function getDateOffered(){
			return $this->dateOffered;
		}
		public function getVehicle(){
			return $this->vehicle;
		}
		public function getDriver(){
			return $this->driver;
		}
	}
?>