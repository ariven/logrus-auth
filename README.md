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

passwords are a sha512 hash of the plaintext password, a salt, and optionally a sitewide salt.

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

