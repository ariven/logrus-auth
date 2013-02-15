<?php

/**
 * User: ariven
 * Date: 1/19/13
 * Time: 2:44 PM
 *
 * Handles basic authentication routines.
 * Holds master currently logged in member in $member, and master copy of their profile in $profile
 *
 */
require_once('logrus_base.php');

class Logrus_auth extends Logrus_base
{

	public $member = FALSE; // current member data
	public $profile = FALSE; // current member profile record
	public $message = ''; // status messages
	public $profile_picture;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}

	/**
	 * Attempts to log in the member using the given password, returns success as TRUE or FALSE
	 *
	 * @param $email
	 * @param $password
	 * @return bool
	 */
	public function login($email, $password)
	{
		$this->message = '';
		$this->load->library('logrus/logrus_member');
		$email  = $this->clean_email($email);
		$member = $this->logrus_member->get($email);
		if ($member)
		{
			if (! $member->active)
			{
				$this->message = 'Email address or password incorrect';
			}
			else
			{
				if ($this->check_fail_count($email))
				{
					$this->message = sprintf('Failed login count exceeded, you can try again in %d minutes.',
											 floor($this->config->item('auth_failed_time') / 60));
				}
				else
				{
					$this->load->library('logrus/logrus_password');
					if ($this->logrus_password->validate($email, $password))
					{
						$session_id = $this->session->userdata($this->config->item('auth_session_id'));
						if (! $session_id)
						{
							$this->load->helper('string');
							$session_id = random_string('alnum', 128);
						}
						$this->update_session($member, $session_id);

						$this->message = 'Successfully logged in';

						return TRUE;
					}
				}
			}
		}
		else
		{
			$this->message = 'Email address or password incorrect';
		}

		$this->increase_fail_count($email);

		return FALSE;
	}

	/**
	 * @param $email
	 * @param $password
	 */
	public function login_with_redirect($email, $password)
	{
		if ($this->login($email, $password))
		{
			$this->load->helper('url');
			if ($this->config->item('auth_logged_in_url') != strtolower(uri_string()))
			{
				redirect($this->config->item('auth_logged_in_url'));
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Logs the member out
	 *
	 * @param $redirect do we redirect after logging out?
	 */
	public function logout($redirect = TRUE)
	{
		$this->delete_session();
		if ($redirect)
		{
			$this->load->helper('url');
			redirect($this->config->item('auth_login_url'));
		} // back to the login page
	}

	/**
	 * Checks to see if member is logged in, and redirects non logged in users to the login page
	 */
	public function check_login()
	{
		if (! $this->logged_in())
		{
			$this->load->helper('url');
			if (strtolower(uri_string()) <> $this->config->item('auth_login_url'))
			{
				redirect($this->config->item('auth_login_url'), 'refresh');
			}
		}
		return TRUE;
	}

	/**
	 * checks to see if member is logged in
	 *
	 * @return bool
	 */
	public function logged_in()
	{
		$this->load->library('logrus/logrus_member');
		$email         = $this->clean_email($this->session->userdata($this->config->item('auth_session_username')));
		$session_id    = $this->clean_string($this->session->userdata($this->config->item('auth_session_id')));
		$member        = $this->logrus_member->get($email);
		$this->message = 'not logged in';

		if ($member and ($this->validate_session($member->id, $session_id)))
		{
			$this->member = $member;

			$this->load->helper('gravatar');
			$this->load->library('logrus/logrus_profile');

			$this->profile = $this->logrus_profile->get($member->id);
			if ($this->profile and property_exists($this->profile,
												   'profile_picture') and $this->profile->profile_picture
			)
			{
				$this->profile_picture = $this->profile->profile_picture;
			}
			else
			{
				$this->profile_picture = gravatar_image($member->email);
			}
			$this->update_session($member, $session_id);
			$this->message = 'logged in';

			return TRUE;
		}
		$this->logout(FALSE);

		return FALSE;
	}

	/**
	 * Refresh session data for current member
	 */
	public function update_session($member, $session_id, $login_authority = FALSE)
	{
		if ($member)
		{
			$this->load->model('logrus/session_auth');

			$session      = $this->session_auth->get_by('session_id', $session_id);
			$session_info = array(
				'ip_address' => $this->input->ip_address(),
				'last_login' => date('Y-m-d H:i:s'),
				'logged_in'  => 1,
				'member_id'  => $member->id
			);
			if ($login_authority)
			{
				$session_info['login_authority'] = $login_authority;
			}
			if ($session)
			{
				$check = $this->session_auth->update($session->id, $session_info);
			}
			else
			{
				$session_info['session_id'] = $session_id;
				$check                      = $this->session_auth->insert($session_info);
			}
			if ($check)
			{
				$this->session->set_userdata($this->config->item('auth_session_username'), $member->email);
				$this->session->set_userdata($this->config->item('auth_session_id'), $session_id);

				return TRUE;
			}
			else
			{
				$this->message = 'There was an error updating session information';
			}
		}

		return FALSE;
	}


	/**
	 * Delete session data for current member
	 *
	 * @return bool
	 */
	public function delete_session()
	{
		$this->load->model('logrus/session_auth');
		$session_id = $this->session->userdata($this->config->item('auth_session_id'));
		$this->session->unset_userdata($this->config->item('auth_session_username'));
		$this->session->unset_userdata($this->config->item('auth_session_id'));
		if ($session_id)
		{
			return $this->session_auth->delete_by('session_id', $session_id);
		}

		return FALSE;
	}


	/**
	 * Validate session is legitimate
	 */
	public function validate_session($member_id, $session_id)
	{
		$this->load->model('logrus/session_auth');
		$member  = $this->logrus_member->get_by_field('id', $member_id);
		$session = $this->session_auth->get_by('session_id', $session_id);
		if ($member and $session)
		{
			if ((strtotime($session->last_login) - (time() > $this->config->item('auth_keep_login_duration'))) and ($session->logged_in) and ($session->member_id == $member->id))
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/////////////////////////////////////////////////////////////////////////////////////////////
	///  OAUTH2  ////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////


	/**
	 * Logs a member in using oauth2 as the authenticator.  Will create a new account if member doesn't exist, AND
	 * auth_open_enrollment is TRUE in config.
	 *
	 * Requires that the oauth2 provider also gives us an email.  We are also trusting the provider to either be
	 * the provider of the email address, or verify the person has control over the email.
	 *
	 * Because of this lack of proof of credentials of email address, this gives us a potential risk of impersonation.
	 * Your acceptance level of risk should dictate who you set up as a provider.
	 *
	 * @param $provider_name name of provider
	 * @param $token         provided by oauth2 library
	 * @param $user_fields   provided by oauth2 library
	 */
	function oauth2_member_login($provider_name, $token, $user_fields, $skip_redirect = FALSE)
	{
		$email  = $this->clean_email($user_fields['email']);
		$name   = $this->clean_string((isset($user_fields['name']) ? $user_fields['name'] : $email));
		$member = $this->logrus_member->get($email);

		$success = TRUE;

		if (! $member and ($this->config->item('auth_open_enrollment')))
		{
			if ($this->config->item('auth_open_enrollment'))
			{
				if ($this->logrus_member->create($email, $name))
				{
					$member = $this->logrus_member->get($email);
				}
				else
				{
					$success = FALSE;
				}
			}
			else
			{
				$this->message = 'Site is closed for new members';
				$success       = FALSE;
			}
		}

		if ($member and $success)
		{
			$this->oauth2_update_member($provider_name, $token, $user_fields);
			$session_id = $this->session->userdata($this->config->item('auth_session_id'));
			if (! $session_id)
			{
				$this->load->helper('string');
				$session_id = random_string('alnum', 128);
			}
			$this->update_session($member, $session_id);
			$this->message = 'Logged in';
			if (! $skip_redirect)
			{
				redirect($this->config->item('auth_logged_in_url'));

			}

			return TRUE;
		}
		else
		{
			// Cant log in if not a member.
			$this->session->unset_userdata($this->session_username);
			$this->session->unset_userdata($this->session_id);
			$this->message = 'Site closed to new members';
		}

		return FALSE;

	}

	/**
	 * Updates member fields with new stuff, i.e. image, provider, etc
	 *
	 * @param $provider_name name of provider
	 * @param $token         provided by oauth2 library
	 * @param $user_fields   provided by oauth2 library
	 */
	function oauth2_update_member($provider_name, $token, $user_fields)
	{
		$this->load->library('logrus/logrus_member');

		if (isset($user_fields['email']))
		{
			$email = $this->clean_email($user_fields['email']);
			return $this->logrus_member->oauth2_update_member($email, $provider_name, $token, $user_fields);
		}
		else
		{
			// cant update without email address
			return FALSE;
		}
	}


	/////////////////////////////////////////////////////////////////////////////////////////////
	///  FAIL COUNT  ////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Increments failed login count
	 *
	 * @param $id member id
	 */
	public function increase_fail_count($email)
	{
		$member = $this->logrus_member->get($email);
		if ($member)
		{
			$this->load->model('logrus/failed_logins');
			$this->failed_logins->insert(array('member_id' => $member->id));
		}
	}

	/**
	 * Clears members failed login count (intended to be used only by administrator)
	 *
	 * @param $email
	 */
	public function clear_fail_count($email)
	{
		$member = $this->logrus_member->get($email);
		if ($member)
		{
			$this->load->model('logrus/failed_logins');
			$this->failed_logins->delete_by(array('member_id' => $member->id));
		}
	}

	/**
	 * Get the current fail count for member
	 *
	 * @param $email
	 */
	public function get_fail_count($email)
	{
		$email = $this->clean_email($email);

		$this->load->library('logrus/logrus_member');
		$member = $this->logrus_member->get($email);
		if ($member)
		{
			$this->load->model('logrus/failed_logins');
			$count = $this->failed_logins->fail_count($member->id);

			return $count;
		}

		return 0;
	}

	/**
	 * Returns TRUE if FAILED count exceeded
	 *
	 * @param $email
	 * @return int
	 */
	public function check_fail_count($email)
	{
		$email = $this->clean_email($email);
		$this->load->library('logrus/logrus_member');
		$member = $this->logrus_member->get($email);
		if ($member)
		{
			$this->load->model('logrus/failed_logins');
			return $this->failed_logins->failed_count($member->id);
		}

		return FALSE; // no member
	}

}