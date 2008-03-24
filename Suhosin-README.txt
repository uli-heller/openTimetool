Some remarks concerning Suhosin Patch (Hardend PPHP)


Suhosin doesn't interpret any php_value entries in htdocs/.htaccess !!!!!

One of the most important lines within the whole openTimetool package however is
this one in that file :

php_value auto_prepend_file <docroot>/openTimetool/config.php

Without this line openTimetool simply will not work at all !

Even the PECL-package htscanner doesn't help in this case ... however it should ...

So the only advice currently is to run openTimetool without Suhosin and disable
it in the matching vhost of your apache configuration.

Its usually that simple :

<IfModule mod_suphp.c>
suPHP_Engine off
</IfModule>

Just put that into your vhost configuration ...