logrus-auth
===========

Authentication package for CodeIgniter 2.1x

There is now an installer script.  You go to [domain]/logrus_install/install to initiate the process.

The installer will ask you basic configuration questions (with defaults) and will create your initial logrus_auth.php config file.  Once this file is present, the installer will exit and not process commands or create database tables.  If you wish to rerun the installer to get a new config, you will have to delete or rename the existing one.

The installer will NOT delete tables, if a table it needs is not there, it will create it.. otherwise it will ignore it.  It also does NOT change tables from older versions that might have different column names/configurations.

The table creation honors the table prefix you put in during the install process, and you can have a blank prefix if you so desire.

Required add-ons:
---------
This uses the fantastic base model extension by Jamie Rumbelow, found here:
https://github.com/jamierumbelow/codeigniter-base-model
You should be able to modify the models to not use it if you so desire.

To add oauth2 login support I used the oauth2 library by Phil Sturgeon, found here:
https://github.com/philsturgeon/codeigniter-oauth2
If you do not use oauth2, you do not need to have this installed.

I modified the windowslive provider to also return the users email address, since accounts are driven by that, so I included a version of that.

Example auth controller included, the main library that does the grunt work is logrus_auth.

It uses a primitive notification library for emailling password resets and similar notices.  This defaults to using PHPs mail command, but since I use a different emailling solution in my own sites, this was a good compromise so that you wont have to go edit out all the email sending in the auth sample controller.

I included a small gravatar grabber helper. (whole 1 line function :) ) that is used to grab a profile
picture/avatar for the member

Included is sql for mysql, though the installer script will create the tables for you

I will update this with better documentation when I get a chance, but for now check the controllers/auth.php
file to see how I use it.

Installation
------------
1. Install 2.x version of Codeigniter
2. Install the [Jamie Rumbelow base model](https://github.com/jamierumbelow/codeigniter-base-model) in your Core directory
3. Copy all files from logrus_auth to the appropriate directories in your install, take care with the .htaccess file, this is the one that I use to remove the index.php from being part of the url... in theory stuff will work without it, but thats how I have mine set up by default
4. configure a default mysql database
5. turn on sessions and database in autoload autoload
6. go to your domain/logrus_install/install to initialize the configuration for logrus_auth
7. if you want to use the oauth2 functions, you will need to install [Phil Sturgeon's oauth2 library](https://github.com/philsturgeon/codeigniter-oauth2)

Security
--------
Passwords are generated with the PBKDF2 method, as described here: https://defuse.ca/php-pbkdf2.htm and are set
to default to 1000 iterations and sha256 for the hash generation.

These defaults can be changed in the pbkdf2 config file, and changing these constants will not affect existing pbkdf2
style passwords in your database, but you can increase the iterations and change the hash method and all subsequent passwords will use the newer configuration seemlessly.

The current way that the sessions work, is single log in.  If you log in somewhere else, it may log you out
of the first session.  This is on my @todo list to change at a later date.



Oauth2
------
You will need to register with google, facebook and windows live to get the client id and secret keys to use

these should get you started down the garden path:
https://developers.facebook.com/apps
https://code.google.com/apis/
http://msdn.microsoft.com/en-us/live/

I will try to hunt down the exact urls again if people need them.


Changelog
---------
2013-02-15
----------
Redesigned the library
* Modularized the system
* Removed the need for the the Jamie Rumbelow MY_Controller mod
* Changed sample views to be controller agnostic.  You WILL need to do work to integrate within your system, since it doesn't even wrap them in an html page
* You still need the Jamie Rumbelow default model, unless you want to modify the models to handle the same functionality
* The oauth2 functions are NOT tested in this iteration.  In theory they will work just the same, except NO TESTING has been done yet
* Created install function, this will create the logrus_auth.php config file for you, and create tables in the current default MYSQL database

==================================================================================
Older changes

- Added Basic oauth2 support for Gmail, Windows Live and Facebook authentication.
Be warned, only allow Facebook logins if you feel that you can trust them as an authority on the email address, because
they are not an email address provider like windows live and gmail are.  This means that in theory someone could
hijack an account if you are not careful.

- Changed password hash to use the PBKDF2 method.  This is change is not compatible with the old method of handling
password and hashes, it requires a change to the table structure and a different method of handling the salt.  This
change though allows you to fine tune your password hash generation to increase security over time without forcing
 users (of this algorithm) to redo their passwords.

- Created install controller that will create the tables and foreign keys needed.  This is experimental.  This also
changes the structure of several of the tables, so it is an all or nothing thing.

- Added a new library file, logrus/password.php.  This file is used as a replaceable connector to manage member records.
This allows the general logic in logrus_auth.php to be static without needing to change it if you decide to change how
you manage the member accounts.  All you have to do is created your own version of the functions in logrus/password.php
and change the new parameter in logrus_auth config (auth_password_library) to point to your new library.  The reason
for this added layer of abstraction is that I found the need to build a RESTful style password server that handles
the creation/authentication of user accounts and passwords on a remote server instead of storing the password hashes
locally.  If there is any desire to see the password server let me know and I will clean up my new connector for it as
well as the code itself and put it up on github to use.
