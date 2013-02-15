<?php

/**
 * User: ariven
 * Date: 1/19/13
 * Time: 2:46 PM
 */

require_once('logrus_base.php');

class Logrus_password extends Logrus_base
{

	public $message = '';

	/**
	 * Sets password for member
	 *
	 * @parame $email
	 * @param $password
	 * @return bool
	 */
	public function set_password($email, $password)
	{
		$this->load->library('logrus/logrus_member');
		$this->load->library('logrus/pbkdf2');

		$member = $this->logrus_member->get($email);
		if ($member)
		{
			$hash   = $this->pbkdf2->create_hash($password);
			$update = $this->logrus_member->set_field($member->email, 'hash', $hash);
			return $update;
		}
		return FALSE;
	}

	/**
	 * Generates a reset code for the specified member and emails it to them
	 *
	 * @param $email
	 */
	public function generate_reset_code($email, $reset_type = 'reset')
	{
		$this->load->library('logrus/logrus_member');
		$member = $this->logrus_member->get($email);
		if ($member)
		{
			$this->load->model('logrus/password_resets');
			$this->load->library('logrus/logrus_notify');

			$reset_code = random_string('sha1', 32);
			$this->password_resets->delete_by('member_id', $member->id); // clear previous resets
			$insert = $this->password_resets->insert(
				array(
					'member_id'  => $member->id,
					'reset_code' => $reset_code
				)
			);
			if ($insert)
			{
				switch ($reset_type)
				{
					case 'reset':
						$email_view    = 'logrus/mail/reset_password';
						$email_subject = 'Password reset request at ' . site_url();
						break;
					case 'confirm_email':
						$email_view    = 'logrus/mail/confirm_email';
						$email_subject = 'Email confirmation from ' . site_url();
						break;
					default:
						$email_view    = 'logrus/mail/new_user_reset';
						$email_subject = 'New Account at ' . site_url();
						break;
				}
				$result = $this->logrus_notify->send(
					$member->email,
					$member->display_name,
					$email_subject,
					$this->load->view($email_view, array('member' => $member, 'base_url' => site_url(), 'reset_code' => $reset_code), TRUE)
				);
				if ($result)
				{
					return $reset_code;
				}
			}
		}
		return FALSE;
	}


	/**
	 * @param $code
	 * @return mixed
	 */
	public function get_reset_code($code)
	{
		$this->load->model('logrus/password_resets');
		$reset = $this->password_resets->get_by('reset_code', $code);
		return $reset;
	}

	/**
	 * @param $member_id
	 * @param $code
	 * @return bool
	 */
	public function validate_reset_code($member_id, $code)
	{
		$code = $this->get_reset_code($code);
		if ($code and $code->member_id = $member_id)
		{
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * sets the password for a member using reset code, returns TRUE if valid reset, and FALSE if it fails
	 *
	 * @param $email
	 * @param $code
	 * @param $password
	 */
	public function set_password_with_reset($email, $code, $password)
	{
		$this->load->library('logrus/logrus_member');
		$member = $this->logrus_member->get($email);
		if ($member)
		{
			$reset = $this->validate_reset_code($member->id, $code);
			if ($reset)
			{
				return $this->set_password($member->email, $password);
			}
		}
		return FALSE;
	}

	/**
	 * verifies that the password given validates against the specified users account.
	 * @param      $email
	 * @param      $password
	 * @return bool
	 */
	public function validate($email, $password)
	{
		$this->load->library('logrus/logrus_member');
		$member = $this->logrus_member->get($email);
		if ($member)
		{
			$this->load->library('logrus/pbkdf2');
			return $this->pbkdf2->validate_password($password, $member->hash);
		}
		return FALSE;

	}

}