<?php

	if (!defined('BASEPATH'))
	{
		exit('No direct script access allowed');
	}

	/**
	 * 2012-04-12 - removed saving url to redirect to if found logged out, was breaking things
	 * 2012-04-12 - added subscription check
	 * 2012-02-27
	 * handles authentication
	 *
	 * @author patrick
	 *
	 */
	class Logrus_auth
	{

		protected $ci; // codeigniter

		protected $auth_tables;

		protected $login_page;
		protected $use_ssl;
		protected $session_username;
		protected $session_id;
		protected $keep_login_duration;

		protected $password_library;

		public $member_menu = ''; // login menu
		public $open_enrollment = FALSE; // do we accept apps, or make admin generate

		public $message;
		public $member = FALSE;
		public $user_groups = array();
		public $redirect_to = FALSE;

		function __construct()
		{
			$this->ci = & get_instance();
			$this->load->config('logrus_auth');
			$this->load->library('session');
			$this->load->helper('url');
			$this->load->library('notify');
			$this->load->library('pbkdf2');
			$this->load->model('lg');

			$this->login_page       = strtolower($this->config->item('auth_login_url'));
			$this->use_ssl          = $this->config->item('auth_use_ssl');
			$this->auth_tables      = $this->config->item('auth_tables');
			$this->password_library = $this->config->item('auth_password_library');

			$this->load->library($this->password_library, '', 'password'); // load the password server

			$this->session_username    = $this->config->item('auth_session_username');
			$this->session_id          = $this->config->item('auth_session_id');
			$this->keep_login_duration = $this->config->item('auth_keep_login_duration');

			$this->login_snippet = '';

			// get redirect url
			$this->redirect_to = strtolower($this->session->userdata($this->ci->config->item('auth_redirect')));
			if ($this->redirect_to)
			{
				$this->session->unset_userdata($this->config->item('auth_redirect'));
			}

			$this->ssl_check();
			$this->get_member();
			$this->login_menu();
			$this->open_enrollment = $this->config->item('open_enrollment');

		}


		/**
		 * Allows library to access CI's loaded classes using the same
		 * syntax as controllers.
		 *
		 * @param    string
		 * @access private
		 */
		function __get($key)
		{
			return $this->ci->$key;
		}

		/**
		 * basic cleanup on username
		 *
		 * @param $username
		 * @return string
		 */
		function prep_username($username)
		{
			return strtolower(trim(filter_var($username, FILTER_SANITIZE_STRING)));
		}

		/**
		 * checks to see if current user is in group passed in $which.
		 *
		 * @param type $which
		 */
		function group_member($which)
		{
			$valid = FALSE;
			if ($this->logged_in())
			{
				if (is_numeric($which))
				{
					$this->load->model('logrus/user_groups');
					$group = $this->groups->get($which);
					$which = $group['name'];
				}
				foreach ($this->member_groups as $group)
				{
					if ($group == $which)
					{
						$valid = TRUE;
					}
				}
			}

			return $valid;
		}

		/**
		 * Checks to see if someone is in a specific group, redirects to not_authorized_url if they are not.
		 *
		 * @param $group
		 */
		function check_group($group)
		{
			if (!$this->group_member($group))
			{
				redirect($this->config->item('not_authorized_url'));
			}
		}

		/**
		 * checks login status, redirects to defined url if not logged in
		 */
		function check_login()
		{
			if (!$this->logged_in())
			{
				$current_page = strtolower($_SERVER['REQUEST_URI']);
				if ($current_page <> $this->login_page)
				{
					redirect($this->login_page, 'refresh');
				}
			}
		}


		/**
		 * logs in a member.  Status messages are stored in $this->message
		 *
		 * @param string $email    the email address entered on login form
		 * @param string $password the password entered on login form
		 * @return bool success
		 */
		function login($email, $password, $skip_redirect = FALSE)
		{

			$username      = $this->prep_username($email);
			$this->message = '';
			$success       = FALSE;
			$member        = $this->password->get_member($username);

			if ($member)
			{
				if ($member->active == 0)
				{
					$this->message = 'Inactive user';
				}
				if ($this->password->get_failed_login_count($member->email))
				{
					$fail_time     = $this->config->item('auth_failed_time');
					$mins          = floor($fail_time / 60);
					$this->message = sprintf('Failed login count exceeded, You can try again in %d minues.', $mins);
				}
				else
				{
					if ($this->password->validate_password($password, $member->hash))
					{
						$this->message = 'Logged in';
						$success       = TRUE;
					}
					else
					{
						$this->message = 'email address or password did not match our records';
					}
				}
			}
			else
			{
				$this->message = 'email address or password did not match our records';
				$success       = FALSE;
			}

			if ($success)
			{
				$session_id = $this->session->userdata($this->ci->config->item('auth_session_id'));
				if (!$session_id)
				{
					$this->load->helper('string');
					$session_id = hash('sha512', random_string('alnum', 128));
				}
				$this->update_session($member->id, $session_id);
				$this->session->set_userdata($this->session_username, $email);
				$this->session->set_userdata($this->session_id, $session_id);
				$this->message = 'Logged in';
				if (!$skip_redirect)
				{
					redirect($this->config->item('auth_logged_in_url'));
				}

				return TRUE;
			}
			else
			{
				$this->session->unset_userdata($this->session_username);
				$this->session->unset_userdata($this->session_id);
				if ($member)
				{
					$this->password->increase_fail_count($member->email);
				}

				return FALSE;
			}
		}


		/**
		 * Looks up the member by their email address
		 *
		 * @param $email
		 * @return bool
		 */
		function member_by_email($email)
		{
			return $this->password->get_member($this->prep_username($email));
		}

		/**
		 * Retrieves member data.  Saves it in the public variable $member as well as returning it.
		 *
		 * @return array|bool
		 */
		function get_member($username = FALSE)
		{
			$this->member        = array();
			$this->member_groups = array();
			if (!$username)
			{
				$username = $this->prep_username($this->ci->session->userdata($this->session_username));
			}
			else
			{
				$username = $this->prep_username($username);
			}
			if ($username)
			{
				$this->member = $this->password->get_member($username);
			}

			return $this->member;
		}

		function member_exists($username)
		{
			return $this->password->get_member($username);
		}

		/**
		 * returns TRUE if user is logged in, will log out user who doesnt meet criteria
		 */
		function logged_in()
		{

			$this->ci->load->helper('gravatar');
			$this->ci->load->model('logrus/profile');
			$this->ci->load->model('logrus/session_auth');

			$username      = $this->prep_username($this->session->userdata($this->session_username));
			$session_id    = $this->session->userdata($this->session_id);
			$member        = $this->password->get_member($username);
			$this->message = 'NOT logged in'; // assume the worst
			$valid         = FALSE;

			if ($member and ($this->valid_session($member->id, $session_id)))
			{
				$profile = $this->password->get_profile($username);
				if ($profile)
				{
					$profile_picture = $profile->profile_picture;
				}
				else
				{
					$profile_picture = gravatar_image($member->email);
				}

				$this->update_session($member->id, $session_id);
				$this->message = 'logged in';
				$valid         = TRUE;
				// $member_data['profile_picture'] = $profile_picture;
				// $member_data['display_name'] = ($member->display_name) ? $member->display_name : $member->email;
				// $member_data['email'] = $member->email;
			}

			if (!$valid)
			{
				$this->log_out(FALSE);

				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}

		/**
		 * Logs out user by removing cookie data, etc
		 *
		 * @param bool $redirect
		 */
		function log_out($redirect = TRUE)
		{
			$this->ci->load->helper('url');
			$this->ci->load->helper('string');

			// $username = $this->prep_username($this->session->userdata($this->session_username));
			// $member = $this->password->get_member($username);
			$session_id = $this->session->userdata($this->session_id);
			if ($session_id)
			{
				$this->clear_session($session_id);
				$this->ci->session->unset_userdata($this->session_username);
				$this->ci->session->unset_userdata($this->session_id);
			}

			if ($redirect)
			{
				if ($_SERVER['REQUEST_URI'] <> $this->login_page)
				{
					redirect($this->login_page, 'refresh');
				}
			}
		}


		/**
		 * checks to see if we need to use SSL, if so, forces it
		 */
		function ssl_check()
		{
			if ($this->use_ssl)
			{
				if (isset($_SERVER['HTTPS']))
				{
					if ($_SERVER['HTTPS'] != "on")
					{
						$url = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
						redirect($url);
					}
				}
				else
				{
					$url = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
					redirect($url);
				}
			}
		}

		/**
		 * validates a reset code
		 *
		 * @param $reset_code
		 * @return mixed FALSE if not valid or not found, the record if found
		 */
		function valid_reset_code($reset_code)
		{
			$this->load->model('logrus/password_resets');

			$reset = $this->password_resets->get_by('reset_code', $reset_code);
			if ($reset)
			{
				if (strtotime($reset->created_at) > (time() - $this->config->item('auth_password_reset_expires')))
				{
					return $reset;
				}
				else
				{
					$this->message = 'Reset code expired.';
				}
			}
			else
			{
				$this->message = 'Invalid Reset code';
			}

			return FALSE;
		}

		function password_matches($username, $password)
		{
			$member = $this->password->get_member($username);
			if ($member)
			{
				return $this->password->validate_password($password, $member->hash);
			}
			else
			{
				$this->message = 'Member not found';

				return FALSE;
			}
		}


		/**
		 * Sets the users password
		 *
		 * @param $member_id
		 * @param $password
		 * @return bool success
		 */
		function set_password($username, $password)
		{
			return $this->password->set_password($username, $password);
		}

		/**
		 * validates reset code and resets password.  $this->message has error mesasge if it fails
		 *
		 * @param $reset_code
		 * @param $password
		 * @return bool
		 */
		function validate_and_set_password($reset_code, $password)
		{
			$code = $this->valid_reset_code($reset_code);
			if ($code)
			{
				return $this->set_password($code->username, $password);
			}

			return FALSE;
		}


		/**
		 * generates a password reset code for the given member
		 *
		 * @param $id member ID
		 */
		function reset_password($id, $reset_type)
		{

			$this->load->model('logrus/password_resets');
			$this->load->library('notify');
			$this->load->helper('string');

			// clean up expired resets
			$this->password_resets->delete_by(
				'created_at < ',
				date('Y-m-d H:i:s',
					 time() - $this->config->item('auth_password_reset_expires')));


			$member = $this->password->get_member_by_id($id);

			if ($member)
			{
				$reset_code = random_string('sha1', 32);
				$this->password_resets->delete_by('member_id', $member->id); // clear old resets
				$this->password_resets->insert(
					array(
						 'member_id'  => $member->id,
						 'username'   => $member->email,
						 'reset_code' => $reset_code,
					)
				);

				if ($reset_type == 'reset')
				{
					$email_view    = 'auth/mail_reset_password';
					$email_subject = 'Password reset request at ' . site_url();
				}
				else
				{
					$email_view    = 'auth/mail_new_user_reset';
					$email_subject = 'New Account at ' . site_url();
				}

				$result = $this->notify->send_now(
					$member->email,
					$member->display_name,
					$email_subject,
					$this->load->view($email_view, array(
														'name'       => $member->display_name,
														'base_url'   => site_url(),
														'reset_code' => $reset_code
												   ), TRUE)
				);

				if ($result)
				{
					return $reset_code;
				}
				else
				{
					return FALSE;
				}
			}

			return FALSE;
		}


		/**
		 * Clears up failed logins for a user before natural expiration time.
		 *
		 * @param $id member id
		 */
		public function clear_failed_logins($id)
		{
			$this->ci->load->model('logrus/failed_logins');
			$this->ci->failed_logins->clear_failed($id);
		}

		/**
		 * Increases failed login count by one
		 *
		 * @param $id member id
		 */
		public function increase_fail_count($id)
		{
			$this->ci->load->model('logrus/failed_logins');
			$this->ci->failed_logins->insert(array(
												  'member_id' => $id,
												  'fail_date' => date('Y-m-d H:i:s')
											 ));
		}

		// /////////////////////////////////////////////////////////////////
		// //  Session id related  /////////////////////////////////////////
		// /////////////////////////////////////////////////////////////////

		/**
		 * handles updating or creating session info in table.
		 *
		 * @param $session_id
		 *
		 * @todo handle session authority
		 */
		public function update_session($member_id, $session_id, $login_authority = FALSE)
		{
			$this->load->model('logrus/session_auth');
			$member  = $this->password->get_member_by_id($member_id);
			$session = $this->session_auth->get_by('session_id', $session_id);

			if ($member)
			{
				$session_info = array(
					'ip_address' => $_SERVER['REMOTE_ADDR'],
					'last_login' => date('Y-m-d H:i:s'),
					'logged_in'  => 1,
					'member_id'  => $member->id,
				);
				if ($login_authority)
				{
					$session_info['login_authority'] = $login_authority;
				}
				if ($session)
				{
					$this->session_auth->update($session->id, $session_info);
				}
				else
				{
					$session_info['session_id'] = $session_id;

					$this->session_auth->insert($session_info);
				}
			}
		}

		/**
		 * removes session from database
		 *
		 * @param $session_id
		 */
		public function clear_session($session_id)
		{
			$this->load->model('logrus/session_auth');
			$session = $this->session_auth->get($session_id);
			if ($session)
			{
				$this->session_auth->delete($session_id);
			}
		}

		/**
		 * checks to see if session is flagged as logged in, and is within time frame of valid session
		 *
		 * @param $session_id
		 */
		public function session_logged_in($session_id)
		{
			$this->load->model('logrus/session_auth');
			$session = $this->session_auth->get_by('session_id', $session_id);
			if ($session)
			{
				if (strtotime($session->last_login) < (time() - $this->config->item('auth_keep_login_duration')))
				{
					if ($session->logged_in)
					{
						return TRUE;
					}
				}
			}

			return FALSE;
		}

		/**
		 * confirms if the given session id is valid for the user.
		 *
		 * @param $member_id  member id
		 * @param $session_id session id
		 * @return bool
		 */
		public function valid_session($member_id, $session_id)
		{
			$this->load->model('logrus/session_auth');
			$member = $this->password->get_member_by_id($member_id);

			$session = $this->session_auth->get_by('session_id', $session_id);
			if (($member) and ($session))
			{
				if ((strtotime($session->last_login) - (time() > $this->config->item('auth_keep_login_duration'))))
				{
					if ($session->logged_in)
					{
						if ($session->member_id == $member->id)
						{
							return TRUE;
						}
					}
				}
			}

			return FALSE;
		}


		/**
		 * Generates the member menu with logged out or logged in variants
		 */
		function login_menu()
		{
			if ($this->logged_in())
			{
				$this->member_menu = $this->load->view('auth/menu_member_logged_in', $this->member, TRUE);
			}
			else
			{
				$this->member_menu = $this->load->view('auth/menu_member_not_logged_in', '', TRUE);
			}
		}


		function create_member($username, $name)
		{
			$username = $this->prep_username($username);
			$result   = $this->password->create_member($username, $name);

			return $result;
		}

		function update_member_field($username, $field, $data)
		{
			$username = $this->prep_username($username);

			return $this->password->set_member_field($username, $field, $data);
		}

		// //////////////////////////////////////////////////////////////////
		// //  OAuth 2 Stuff ////////////////////////////////////////////////
		// //////////////////////////////////////////////////////////////////

		/**
		 * Looks up member by OAuth2 UID
		 *
		 * @param $uid OAUTH2 uid
		 * @return bool
		 */
		function member_by_uid($uid)
		{
			return $this->password->get_member_by_field('oauth2_uid', $uid);
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
			$username = $this->prep_user_name($user_fields['email']);
			$member   = $this->password->get_member($username);
			$profile  = $this->password->get_profile($username);
			$this->password->set_member_field($username, 'login_authority', $provider_name);

			if ($profile)
			{
				if (!$profile->profile_picture)
				{
					$this->password->set_profile_field($username, 'profile_picture',
													   (isset($user_fields['image'])) ? $user_fields['image'] : gravatar_image($user_fields['email']));
				}
			}
			if (!$member->display_name)
			{
				$this->password->set_member_field($username, 'display_name',
												  (isset($user_fields['name'])) ? $user_fields['name'] : $user_fields['email']
				);
			}
		}

		/**
		 * Logs a member in using oauth2 as the authenticator.  Will create a new account if member doesn't exist, AND
		 * auth_open_enrollment is TRUE in config.
		 *
		 * Requires that the oauth2 provider also gives us an email.  We are also trusting the provider to either be
		 * the provider of the email address, or verify the person has access.
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
			$member = $this->member_by_email($user_fields['email']);
			if (!$member)
			{
				if ($this->config->item('auth_open_enrollment'))
				{
					// make an account, log them in
					$this->create_member($user_fields['email'], $user_fields['name']);
					$member = $this->member_by_email($user_fields['email']);
				}
				else
				{
					redirect('/auth/site_closed');
				}
			}

			if ($member)
			{
				$this->oauth2_update_member($provider_name, $token, $user_fields);

				$session_id = $this->session->userdata($this->config->item('auth_session_id'));
				if (!$session_id)
				{
					$session_id = hash('sha512', random_string('alnum', 128));
				}
				$this->update_session($member->id, $session_id);
				$this->session->set_userdata($this->session_username, $user_fields['email']);
				$this->session->set_userdata($this->session_id, $session_id);
				$this->message = 'Logged in';
				if (!$skip_redirect)
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
				redirect('/auth/site_closed');

				return FALSE;
			}

		}
	}

