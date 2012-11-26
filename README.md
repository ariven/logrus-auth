logrus-auth
===========

Authentication package for CodeIgniter 2.1x

required:
---------
This uses the fantastic base model extension by Jamie Rumbelow, found here:
https://github.com/jamierumbelow/codeigniter-base-model
You should be able to modify the models to not use it if you so desire.

To add oauth2 login support I used the oauth2 library by Phil Sturgeon, found here:
https://github.com/philsturgeon/codeigniter-oauth2

I modified the windowslive provider to also return the users email address, since accounts are driven by that, so I included a version of that.

The example Auth controller uses the base controller by Jamie Rumbelow found here:
https://github.com/jamierumbelow/codeigniter-base-controller
instead of my own templating system.  I have adjusted the sample views as well.

Example auth controller included, the main library that does the grunt work is logrus_auth.

It also uses my notify library for emailing password resets.  You will need to replace the code in the send
routines in libraries/notify.php to whatever method you want to use to send email.

I included a small gravatar grabber helper. (whole 1 line function :) ) that is used to grab a profile
picture/avatar for the member

Included is sql for mysql.  You will need to edit table names if you intend to use a prefix.

I will update this with better documentation when I get a chance, but for now check the controllers/auth.php
file to see how I use it.

Passwords are generated with the PBKDF2 method, as described here: https://defuse.ca/php-pbkdf2.htm and are set
to default to 1000 iterations and sha256 for the hash generation.

These defaults can be changed in the pbkdf2 config file, and changing these constants will not affect existing pbkdf2
style passwords in your database.

The current way that the sessions work, is single log in.  If you log in somewhere else, it logs you out
of the first session.  This is on my @todo list to change at a later date.

Currently it should support the config option to not allow creation of account when an unknown oauth2 person tries to log in.

You will need to register with google, facebook and windows live to get the client id and secret keys to use

these should get you started down the garden path:
https://developers.facebook.com/apps
https://code.google.com/apis/
http://msdn.microsoft.com/en-us/live/

I will try to hunt down the exact urls again if people need them.


Changelog
---------
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
