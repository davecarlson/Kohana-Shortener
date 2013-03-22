<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Shortener extends Controller_Template_Site {


	public function action_lengthen()
	{
		$code = Request::current()->param("code");
		
		$shortener = Shortener::factory();
		$shortener->lengthen($code);
		
	} // function action_lengthen()
	
	
	public function action_create()
	{
		
		$shortener = Shortener::factory();
		
		if ( $url = Arr::get($_REQUEST, 'url', false) ):
			
			if ( $api = Arr::get($_REQUEST, 'api', false) ):
				$this->auto_render = false;
				echo $shortener->shorten($url);
			else:
				$shortener->shorten($url);
			endif;
			
			$this->template->content = $shortener->render();
			
		else:
			HTTP::redirect("/");
		endif;
		
	} // action_create

} // End Shortener