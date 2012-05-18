<?php
	/**
	 * User: patrick
	 * Date: 5/16/12
	 * Time: 11:05 AM
	 */


	$config['mail_master_html_template'] = 'mail/email_template'; // master template in view directory to wrap mail in
	$config['mail_site_name']            = 'RANDOM SITE NAME';
	$config['mail_admin_email']          = 'admin@example.com';
	$config['mail_admin_name']           = 'administrator';
	$config['mail_cs_email']             = 'admin@example.com';
	$config['mail_cs_name']              = 'administrator';
	$config['mail_from_email']           = 'admin@example.com';
	$config['mail_from_name']            = 'RANDOM SITE NAME';

	$config['mail_domain_name']          = 'example.com'; // the domain to send mail from as per server
	$config['mail_domain_key']           = 'DOMAINKEY'; // key to this domain name
	$config['mail_server']               = 'http://mserv.example.com/api/mail'; // rest server for email