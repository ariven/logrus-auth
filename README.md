logrus-auth
===========

Authentication package for CodeIgniter 2.1x

required:
This uses the fantastic base model extension by Jamie Rumbelow, found here:
https://github.com/jamierumbelow/codeigniter-base-model

You should be able to modify the models to not use it if you so desire.

Example auth controller included, the main library that does the grunt work is logrus_auth.

The example uses my template library, so the template is included.

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

