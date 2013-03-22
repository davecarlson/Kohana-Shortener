<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Shortener {

	// Base URL to prepend to the short code
	protected $base_url;
	
	// Config File
	protected $_config;
	
	// Database Table Name to store codes
	protected $table_name;

	// URL Submitted by the Form
	protected $submitted_url;

	// Short code created by the system
	protected $code;
	
	// Full URL to give back
	protected $short_url;

	// Notifies if any errors have occured
	protected $status;

	/**
	 * Creates a new Shortener object.
	 *
	 * @return  Shortener
	 */
	public static function factory()
	{

		return new Shortener();

	} // public static function factory()
	

	/**
	 * Creates a new Shortener object.
	 *
	 * @return  void
	 */
	public function __construct()
	{
	
		$config_file = Kohana::$config->load('shortener');
		$this->_config = $config_file;
		
		$this->table_name = $config_file['table_name'];
		$this->base_url = $config_file['base_url'];		
		
	}  // public function __construct()

	
	/**
	 * Creates the Short URL form
	 *
	 * @return View
	 */
	public function render()
	{

		$view = View::factory("shortener/create");
		if ($this->status == "error"):
			$view->bind("error", $this->status);
		elseif ($this->short_url):
			$view->bind("url", $this->short_url);
		endif;
		return $view->render();

	} // public function render()


	/**
	 * Shortens a URL
	 *
	 * @param string URL
	 * @return string "Short URL"
	 */
	public function shorten($url)
	{
		$this->status = '';
		
		if ( $this->is_valid_url($url) ):
		
			// Check DB to see if URL already exists
			$result = DB::select("code")
					->from($this->table_name)
					->where("url", "=", $url)
					->execute();

			// If url already exists, just return the existing code
			if ($result->count() > 0):
				$row = $result->current();
				$this->short_url = $this->base_url.$row['code'];
				return $this->short_url;
			endif;

			$this->code = $this->create_random_code();
			$this->submitted_url = $url;
			$this->created = date("Y-m-d H:i:s");
			
			// Insert details into the database for later retrieval
			$query = DB::insert( $this->table_name, array("code", "url", "created") )
				->values( array($this->code, $this->submitted_url, $this->created) )
				->execute();
			
			if ( $query ):	
				$this->short_url = $this->base_url.$this->code;
				return $this->short_url;
			else:
				return false;
			endif;

		else:
			$this->status = "error";
			return false;
		endif;
		
	} // public function shorten()


	/**
	 * Generates a random code
	 *
	 * @return string code
	 */
	private function create_random_code()
	{
		$code = "";
		$character_set = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    	
		for ($i = 1; $i <= 6; $i++):
			$code .= $character_set[rand(0, strlen($character_set) - 1)];
		endfor;
		
		// Check to see if this code exists already in the database
		$result = DB::select("code")
					->from($this->table_name)
					->where("code", "=", $code)
					->execute();

		// If so, create a new code			
		if ( $result->count() > 0):
			return $this->create_random_code();
		endif;

		return $code;

	} // function create_random_code()
	

	/**
	 * Check if a URL is well formed
	 *
	 * @param string url
	 * @return boolean
	 */
	private function is_valid_url($url)
	{	
		return preg_match('/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i', $url);
		//return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	
	}  // function is_valid_url()


	/**
	 * Looks up short code and links user to original URL
	 *
	 * @param string code
	 * @return 302 redirect on success
	 * @return boolean on fail
	 */
	public function lengthen($code)
	{
		// Check to see if valid code
		if ( preg_match("/^[0-9a-zA-Z]{6}$/", $code) ):
		
			$result = DB::select("url")
					->from($this->table_name)
					->where("code", "=", $code)
					->execute();

			if ( $result->count() > 0  ):
				$row = $result->current();
				Request::current()->redirect( $row['url']) ;
				exit;
			else:
				return false;
			endif;
		else:
			return false;
		endif;			
	} // function lengthen_url


	/**
	 * Use BitLy API to create a shortened URL
	 *
	 * @author Iain Jewitt <iain.jewitt@fastwebmedia.com>
	 * @param string url
	 * @param string timeout
	 * @return string bitly url
	 */
	public function bitly($url, $timeout = 5)
	{
		if ( !$this->is_valid_url($url) ):
			throw new Kohana_Exception("Invalid URL");
		endif;
		
		if ( ! $services = $this->_config->get("services") ):
			throw new Kohana_Exception("Please ensure you have a list of services in the config.");
		endif;
		
		if ( !array_key_exists( 'bitly', $services) ):
			throw new Kohana_Exception("Please ensure you have specified the bitly settings in the config.");
		endif;
		
		if ( !array_key_exists( 'login', $services['bitly']) ):
			throw new Kohana_Exception("Please ensure you have specified the bitly login in the config.");
		endif;
		
		if ( !array_key_exists( 'apikey', $services['bitly']) ):
			throw new Kohana_Exception("Please ensure you have specified the bitly API key in the config.");
		endif;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.bit.ly/v3/shorten?login=' . urlencode($services['bitly']['login']) . '&apiKey=' . urlencode($services['bitly']['apikey']) . '&uri=' . urlencode($url) . '&format=txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);

		return preg_replace('/[\s\n\r]+/', '', $data);

	} // public function bitly

} // class Kohana_Shortener
