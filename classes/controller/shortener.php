<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Shortener extends Controller_Template {

	public function action_lengthen()
	{
		$code = $this->request->param("code");
		$shortener = Shortener::factory();
		$shortener->lengthen($code);
		
	} // function action_lengthen()
	
	
	public function action_create()
	{
		
		$shortener = Shortener::factory();
		
		if (isset($_REQUEST['api']) && $_REQUEST['api'] == true):

			$this->auto_render = false;
			echo $shortener->shorten($_REQUEST['url']);

		else:
			
			if ( isset($_POST['url']) ):
				$shortener->shorten($_POST['url']);
			endif;
			$this->template->content = $shortener->render();
			
		endif;
	} // action_create

} // End Shortener