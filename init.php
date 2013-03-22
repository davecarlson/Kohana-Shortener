<?php defined('SYSPATH') or die('No direct script access.');

// Routes for the URL Shortener
Route::set('urlshortener_lengthen', 's/<code>')
	->defaults(array(
		'controller' => 'Shortener',
		'action'     => 'lengthen',
	));

Route::set('urlshortener_shorten', 's')
	->defaults(array(
		'controller' => 'Shortener',
		'action'     => 'create',
	));
