<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'table_name' => 'shortener',
	'base_url' => "http://".$_SERVER['SERVER_NAME']."/s/",
	'services' => array(
		"bitly" => array(
			'login'  => 'fwmtech',
			'apikey' => 'R_4c1ad88f6f45a8e11ffd2fc651b378a0',
		)
	)
);
