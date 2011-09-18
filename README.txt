1) Execute /public/index.php




NOTE:


If you find any page not found error then you need to set up below overwrite rules
 


.htaccess file

-----------------------------------------------------

RewriteEngine on

RewriteRule !\.(js|ico|gif|jpg|png|css|php)$ index.php

php_flag magic_quotes_gpc off

php_flag register_globals off





Apache configuration:

--------------------------

<direcory>

Modify this => AllowOverride none to AllowOverride All

</directory>



Dont forget to enable RewriteEngine module.