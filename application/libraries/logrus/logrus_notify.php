<?php
/**
 * User: patrick
 * Date: 1/30/13
 * Time: 9:23 AM
 *
 */

require_once('logrus_base.php');

class Logrus_notify extends Logrus_base
{
	public function __construct()
	{
		parent::__construct();
		$this->load->config('logrus_notify');
	}

	/**
	 * Send a quick email to the admin, with a generic subject line
	 *
	 * @param $msg
	 * @return bool
	 */
	public function admin($msg)
	{
		return $this->send(
			$this->config->item('logrus_admin_email'),
			$this->config->item('logrus_admin_name'),
			'message from ' . $this->config->item('logrus_site_name') . ' on ' . date('m-d-Y \a\t H:i:s'),
			$msg
		);
	}

	/**
	 * Generic email function.  Override with your own preferred implementation.
	 *
	 * @param        $email
	 * @param        $name
	 * @param        $subject
	 * @param        $msg
	 * @param bool   $urgent
	 * @param string $bcc
	 * @return bool
	 */
	public function send($email, $name, $subject, $msg, $urgent = FALSE, $bcc = '')
	{
		$to        = sprintf('%s <%s>', $name, $email);
		$crlf      = "\r\n";
		$headers[] = 'Message-ID: <' . $_SERVER['REQUEST_TIME'] . md5($_SERVER['REQUEST_TIME']) . '@' . $_SERVER['SERVER_NAME'] . '>';
		$headers[] = 'From: ' . $this->config->item('logrus_mail_from');
		$headers[] = 'Reply-To: ' . $this->config->item('logrus_reply_to');
		$headers[] = 'X-Mailer: PHP/' . phpversion() . '/logrus_notify';
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=iso-8859-1';

		$bcc_template = "Bcc: %s\r\n";
		$tbcc         = '';
		if (is_array($bcc))
		{
			foreach ($bcc as $b)
			{
				if (strlen($tbcc) > 0)
				{
					$tbcc .= ','; // add comma
				}
				$tbcc .= $b; // add each item
			}
		}
		else
		{
			$tbcc = $bcc;
		}
		$headers[] = sprintf($bcc_template, $tbcc);

		return mail($to, $subject, $msg, implode("\r\n", $headers));
	}
}