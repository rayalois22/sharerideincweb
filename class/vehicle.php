<?php
	class vehicle{
		//members
		protected $regNumber;
		protected $model;
		protected $capacity;
		protected $driver;
		
		public function __construct($regNumber, $model, $capacity, $driver){
			$this->regNumber = $regNumber;
			$this->model = $model;
			$this->capacity = $capacity;
			$this->driver = $driver;
		}
		
		//mutators
		public function setRegNumber($regNumber){
			$this->regNumber = $regNumber;
		}
		public function setModel($model){
			$this->model = $model;
		}
		public function setCapacity($capacity){
			$this->capacity = $capacity;
		}
		
		//accessors
		public function getRegNumber(){
			return $this->regNumber;
		}
		public function getDriver(){
			return $this->driver;
		}
		public function getModel(){
			return $this->model;
		}
		public function getCapacity(){
			return $this->capacity;
		}
	}
?>