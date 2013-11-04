<?php
class Helper_File {
	private $pathname  = "";
	private $fullname  = ""; // full name of the given file
	private $directory = ""; // 
	private $extension = "";
	private $basename  = "";
	private $nickname  = "";
	
	public function __construct ( $name ) {
		if ( !file_exists($name) ) throw new Exception("Target File Not Exist ");
		
		$this->passname  = $name;
		$this->fullname  = realpath($name);		
		$pathinfo        = pathinfo($this->fullname);
		
		$this->directory = dirname($this->fullname);
		$this->basename  = basename($this->fullname);
		$this->extension = strtoupper( trim($pathinfo['extension']) );
		$this->nickname  = substr($this->basename, 0, stripos($this->basename, ".".$this->extension));
	}
	
	public function __get ( $name ) { return in_array(strtoupper($name),array("FULLNAME","DIRECTORY","EXTENSION","BASENAME","NICKNAME"))?$this->$name:""; }

	public function delete () { unlink($this->fullname); }
	
	public static function separator () { return DIRECTORY_SEPARATOR; }
	
	public function __destruct () { }
}