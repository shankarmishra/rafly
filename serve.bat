@echo off
REM Starts the local dev server WITH the router that emulates .htaccess's
REM clean-URL rewrites. Running `php -S 127.0.0.1:8899` without router.php
REM as the second argument serves the homepage for every clean URL instead
REM of the real page — see router.php's header comment for why.
php -S 127.0.0.1:8899 router.php
