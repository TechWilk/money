# Money

A very quick and messy web app for logging finances, currently considered a pre-alpha release and still under significant development.

> WARNING: There is no authentication built into the site, so **DO NOT** run on a public facing web server.

## Install notes

* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writeable.

To run the application in development, you can also run this command. 

	php composer.phar start

Run this command to run the test suite

	php composer.phar test

That's it!