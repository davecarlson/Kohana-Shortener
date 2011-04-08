URL Shortener
---

A simple module to convert a full url to a short code version, and back again

How to:

1) Create the shortener table in your database. db_schema.sql includes the necessary SQL.
2) Edit copy config/shortener.php into application/config/ and adjust values. Please ensure the base_url ends in a trailing slash.
3) Add 'shortener'  => MODPATH.'shortener' to your modules array in bootstrap.php

The system currently runs on the URI /s/. If you wish to change this, simple adjust the routes in init.php and update the config base_url accordingly.


This module has been tested with Kohana 3.1, but should also work with Kohana 3.0.x