<?php

	if (!defined('BASEPATH'))
		exit('No direct script access allowed');

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

		public $member_menu = ''; // login menu
		public $open_enrollment = FALSE; // do we accept apps, or make admin generate

		public $message;
		public $member = FALSE;
		public $user_groups = array();
		public $redirect_to = FALSE;

		function __construct()
		{
			$this->ci = & get_instance();
			$this->ci->load->config('logrus_auth');
			$this->ci->load->library('session');
			$this->ci->load->helper('url');
			$this->ci->load->library('notify');

			$this->login_page  = strtolower($this->ci->config->item('auth_login_url'));
			$this->use_ssl     = $this->ci->config->item('auth_use_ssl');
			$this->auth_tables = $this->ci->config->item('auth_tables');

			$this->session_username    = $this->ci->config->item('auth_session_username');
			$this->session_id          = $this->ci->config->item('auth_session_id');
			$this->keep_login_duration = $this->ci->config->item('auth_keep_login_duration');

			$this->login_snippet = '';

			// get redirect url
			$this->redirect_to = strtolower($this->ci->session->userdata($this->ci->config->item('auth_redirect')));
			if ($this->redirect_to)
			{
				$this->session->unset_userdata($this->ci->config->item('auth_redirect'));
			}

			$this->ssl_check();
			$this->get_member();
			$this->login_menu();
			$this->open_enrollment = $this->ci->config->item('open_enrollment');
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
					$this->ci->load->model('logrus/user_groups');
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
			$this->ci->load->model('logrus/failed_logins');
			$this->ci->load->model('logrus/member');

			$email         = strtolower(trim($email));
			$this->message = '';
			$success       = TRUE;

			$member = $this->ci->member->get_by('email', $email);

			if (!$member)
			{
				// no user found, fail
				$this->message = 'email address or password did not match our records. ' . __LINE__;
				$success       = FALSE;
			}
			else
			{
				if ($member->active == 0)
				{
					$this->message = 'Inactive user. ' . __LINE__;
					$success       = FALSE;
				}
				else
				{
					if ($this->failed_login_count($member->id))
					{
						$success       = FALSE;
						$fail_time     = $this->config->item('auth_failed_time');
						$mins          = floor($fail_time / 60);
						$this->message = sprintf('Failed login count exceeded, You can try again in %d minues.', $mins);
					}
					else
					{
						$hash = $this->hash_password($password, $member->salt);

						if ($member->password == $hash)
						{
							$this->message = 'logged in.';
						}
						else
						{
							$this->message = 'email address or password did not match our records. ' . __LINE__;
							$success       = FALSE;
						}
					}
				}
			}
			if ($success)
			{
				$session_id = $this->ci->session->userdata($this->ci->config->item('auth_session_id'));
				if (!$session_id)
				{
					$session_id = $this->hash_password(time(), $member->salt);
				}
				$this->update_session($member->id, $session_id);
				$this->ci->session->set_userdata($this->session_username, $email);
				$this->ci->session->set_userdata($this->session_id, $session_id);
				$this->message = 'Logged in';
				if (!$skip_redirect)
				{
					redirect($this->ci->config->item('auth_logged_in_url'));
				}
				return TRUE;
			}
			else
			{
				$this->ci->session->unset_userdata($this->session_username);
				$this->ci->session->unset_userdata($this->session_id);
				if ($member)
				{
					$this->increase_fail_count($member->id);
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
			$email = filter_var(strtolower(trim($email)));
			if ($email)
			{
				$member = $this->ci->member->get_by('email', $email);
				return $member;
			}
			else
			{
				return FALSE;
			}
		}

		/**
		 * Retrieves member data.  Saves it in the public variable $member as well as returning it.
		 *
		 * @return array|bool
		 */
		function get_member()
		{

			$this->member        = array();
			$this->member_groups = array();
			$email               = filter_var($this->ci->session->userdata($this->session_username), FILTER_SANITIZE_EMAIL);
			$this->ci->load->model('logrus/member');

			if ($email)
			{
				$member = $this->member_by_email($email);
				if ($member)
				{
					$this->member = $member;
				}
			}
			else
			{
				$this->member = array();
			}
			return $this->member;
		}


		/**
		 * returns TRUE if user is logged in, will log out user who doesnt meet criteria
		 */
		function logged_in()
		{
			$this->ci->load->model('logrus/member');
			$this->ci->load->model('logrus/session_auth');
			$this->ci->load->model('logrus/profile');
			$this->ci->load->helper('gravatar');

			$this->message = 'NOT logged in'; // assume the worst
			$valid         = FALSE;

			$email = strtolower(trim($this->ci->session->userdata($this->session_username)));

			$session_id = $this->ci->session->userdata($this->session_id);

			$member = $this->ci->member->get_by('email', $email);
			if ($member)
			{
				$profile = $this->ci->profile->get_by('member_id', $member->id);
				if ($profile)
				{
					$profile_picture = $profile->profile_picture;
				}
				else
				{
					$profile_picture = gravatar_image($member->email);
				}
			}

			if (($member) and ($this->valid_session($member->id, $session_id)))
			{
				$this->update_session($member->id, $session_id);

				$this->message                  = 'logged in';
				$valid                          = TRUE;
				$member_data['profile_picture'] = $profile_picture;
				if ($member->display_name == '')
				{
					$member_data['display_name'] = $member->email;
				}
				else
				{
					$member_data['display_name'] = $member->display_name;
				}
				$member_data['email'] = $member->email;
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
			$this->ci->load->model('logrus/member');

			$email  = strtolower(trim($this->ci->session->userdata($this->session_username)));
			$member = $this->ci->member->get_by('email', $email);

			$session_id = $this->ci->session->userdata($this->session_id);
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
			$this->ci->load->model('logrus/password_resets');

			$reset = $this->ci->password_resets->get_by('reset_code', $reset_code);
			if ($reset)
			{
				if (strtotime($reset->reset_date) > (time() - $this->ci->config->item('auth_password_reset_expires')))
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

		/**
		 * @param $member_id member id
		 * @param $password password to check
		 * @return bool
		 */
		function password_matches($member_id, $password)
		{
			$this->ci->load->model('logrus/member');
			$member = $this->ci->member->get($member_id);
			if ($member)
			{
				$hash = $this->hash_password($password, $member->salt);
				if ($hash == $member->password)
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
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
		function set_password($member_id, $password)
		{
			$this->ci->load->model('logrus/member');
			$member = $this->ci->member->get($member_id);
			if ($member)
			{
				$salt         = ($member->salt <> '') ? $member->salt : $this->generate_salt();
				$new_password = $this->hash_password($password, $salt);
				$this->ci->member->update($member_id, array('password' => $new_password,
															'salt'     => $salt));
				return TRUE;
			}
			else
			{
				return FALSE;
			}
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
				return $this->set_password($code->member_id, $password);
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
			$this->ci->load->model('logrus/member');
			$this->ci->load->model('logrus/password_resets');
			$this->ci->load->library('notify');

			// clean up old resets
			$this->ci->password_resets->delete_by('reset_date <', date('Y-m-d H:i:s', time() - $this->config->item('auth_password_reset_expires')));

			$member = $this->member->get($id);
			if ($member)
			{
				$reset_code = hash('sha1', print_r($member, TRUE) . time());
				$this->ci->password_resets->delete_by('member_id', $member->id); // clear old resets
				$this->ci->password_resets->insert(
					array(
						'member_id'      => $member->id,
						'reset_code'     => $reset_code,
						'reset_date'     => date('Y-m-d H:i:s')
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

				$result = $this->ci->notify->send_now(
					$member->email,
					$member->display_name,
					$email_subject,
					$this->ci->load->view($email_view, array('name'       => $member->display_name,
															 'base_url'   => site_url(),
															 'reset_code' => $reset_code), TRUE)
				);
				return $reset_code;
			}
			return FALSE;
		}

		/**
		 * @return string Generates a salt
		 */
		public function generate_salt()
		{
			$this->ci->load->helper('string');
			return hash('sha512', time() . random_string('sha1', 40));
		}

		/**
		 * Produces a hashed password, using password, salt and site key
		 *
		 * @param $password members password
		 * @param $salt members salt
		 * @return string
		 */
		public function hash_password($password, $salt)
		{
			$this->ci->config->load('logrus_auth');
			return hash('sha512', $password . $salt . $this->ci->config->item('auth_site_key'));
		}


		/**
		 * Returns TRUE if user failed login count
		 *
		 * @param $id member id
		 * @return bool
		 */
		public function failed_login_count($id)
		{
			$this->ci->load->model('logrus/failed_logins');
			return $this->ci->failed_logins->failed_count($id);
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
			$this->ci->load->model('logrus/session_auth');
			$this->ci->load->model('logrus/member');
			$member  = $this->ci->member->get($member_id);
			$session = $this->ci->session_auth->get_by('session_id', $session_id);

			if ($member)
			{
				$session_info = array(
					'ip_address'   => $_SERVER['REMOTE_ADDR'],
					'last_login'   => date('Y-m-d H:i:s'),
					'logged_in'    => 1,
					'member_id'    => $member->id,
				);
				if ($login_authority)
				{
					$session_info['login_authority'] = $login_authority;
				}
				if ($session)
				{
					$this->ci->session_auth->update($session->id, $session_info);
				}
				else
				{
					$session_info['session_id'] = $session_id;

					$this->ci->session_auth->insert($session_info);
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
			$this->ci->load->model('logrus/session_auth');
			$session = $this->ci->session_auth->get($session_id);
			if ($session)
			{
				$this->ci->session_auth->delete($session_id);
			}

		}

		/**
		 * checks to see if session is flagged as logged in, and is within time frame of valid session
		 *
		 * @param $session_id
		 */
		public function session_logged_in($session_id)
		{
			$this->ci->load->model('logrus/session_auth');
			$session = $this->ci->session_auth->get_by('session_id', $session_id);
			if ($session)
			{
				if (strtotime($session->last_login) < (time() - $this->ci->config->item('auth_keep_login_duration')))
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
		 * @param $member_id member id
		 * @param $session_id session id
		 * @return bool
		 */
		public function valid_session($member_id, $session_id)
		{
			$this->ci->load->model('logrus/session_auth');
			$this->ci->load->model('logrus/member');
			$member  = $this->ci->member->get($member_id);
			$session = $this->ci->session_auth->get_by('session_id', $session_id);
			if (($member) and ($session))
			{
				if ((strtotime($session->last_login) - (time() > $this->ci->config->item('auth_keep_login_duration'))))
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
				$this->member_menu = $this->ci->load->view('auth/menu_member_logged_in', $this->member, TRUE);
			}
			else
			{
				$this->member_menu = $this->ci->load->view('auth/menu_member_not_logged_in', '', TRUE);
			}

		}


		/**
		 * tries to retrieve members profile
		 *
		 * @param $id
		 * @return mixed
		 */
		function member_profile($id)
		{
			$this->ci->load->model('logrus/profile');
			return $this->ci->profile->get_by('member_id', $id);
		}

		/**
		 * Sets specified profile field for member, creates profile if none exists
		 *
		 * @param $id
		 * @param $which
		 * @param $what
		 */
		function set_profile_field($id, $which, $what)
		{
			$this->ci->load->model('logrus/profile');
			$profile = $this->member_profile($id);
			if (!$profile)
			{
				$this->ci->profile->insert(array('member_id' => $id,
												 $which      => $what));
			}
			else
			{
				$this->ci->profile->update($id, array($which => $what));
			}
		}


		function get_profile_field($id, $which)
		{
			$this->ci->load->model('logrus/profile');
			$profile = $this->member_profile($id);
			if ($profile)
			{
				if ($profile->$which)
				{
					return $profile->$which;
				}
			}
			return '';
		}


		function create_member($email, $name)
		{
			$this->ci->load->model('logrus/member');
			$success = TRUE;

			$result = $this->ci->member->insert(array(
				'email'        => strtolower(trim($email)),
				'display_name' => trim($name)
			));
			if ($result)
			{
				if ($this->ci->config->item('auth_create_default_group'))
				{
					$this->ci->load->model('logrus/groups');
					$this->ci->load->model('logrus/member_groups');
					$group        = $this->ci->groups->get_by('tag', $this->ci->config->item('auth_default_group'));
					$group_result = $this->ci->member_groups->insert(array(
						'group_id'  => $group->id,
						'member_id' => $result
					));
					if (!$group_result)
					{
						$this->ci->lg->error('Failed to create default group for member ' . $result);
					}
				}
			}
			else
			{
				$success = FALSE;
			}
			return $success;
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
			if ($uid)
			{
				$member = $this->ci->member->get_by('oauth2_uid', $uid);
				return $member;
			}
			else
			{
				return FALSE;
			}
		}

		/**
		 * Updates member fields with new stuff, i.e. image, provider, etc
		 *
		 * @param $provider_name name of provider
		 * @param $token provided by oauth2 library
		 * @param $user_fields provided by oauth2 library
		 */
		function oauth2_update_member($provider_name, $token, $user_fields)
		{
			$member = $this->member_by_email($user_fields['email']);
			$this->ci->member->update($member->id, array('oauth2_token'    => $token->access_token,
														 'oauth2_uid'      => $user_fields['uid'],
														 'login_authority' => $provider_name));

			if (!$this->get_profile_field($member->id, 'profile_picture'))
			{
				if (isset($user_fields['image']))
				{
					$this->set_profile_field($member->id, 'profile_picture', $user_fields['image']);
				}
				else
				{
					$this->ci->load->helper('gravatar');
					$this->set_profile_field($member->id, 'profile_picture', gravatar_image($user_fields['email']));
				}
			}
			if (!$member->display_name)
			{
				if (isset($user_fields['name']))
				{
					$this->ci->member->update($member->id, array('display_name' => $user_fields['name']));
				}
				else
				{
					$this->ci->member->update($member->id, array('display_name' => $user_fields['email']));
				}
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
		 * @param $token provided by oauth2 library
		 * @param $user_fields provided by oauth2 library
		 */
		function oauth2_member_login($provider_name, $token, $user_fields, $skip_redirect = FALSE)
		{
			$member = $this->member_by_email($user_fields['email']);
			if (!$member)
			{
				if ($this->ci->config->item('auth_open_enrollment'))
				{
					// make an account, log them in
					$this->create_member($user_fields['email'], $user_fields['name']);
					$member = $this->member_by_email($user_fields['email']);
					$this->ci->lg->info('Created new member via OAuth2 and provider ' . $provider_name);
				}
				else
				{
					redirect('/auth/site_closed');
				}
			}

			if ($member)
			{
				$this->oauth2_update_member($provider_name, $token, $user_fields);

				$session_id = $this->ci->session->userdata($this->ci->config->item('auth_session_id'));
				if (!$session_id)
				{
					$session_id = $this->hash_password(time(), $member->salt . $this->ci->config->item('auth_site_key') . mt_rand());
				}
				$this->update_session($member->id, $session_id);
				$this->ci->session->set_userdata($this->session_username, $user_fields['email']);
				$this->ci->session->set_userdata($this->session_id, $session_id);
				$this->message = 'Logged in';
				if (!$skip_redirect)
				{
					redirect($this->ci->config->item('auth_logged_in_url'));
				}
				return TRUE;
			}
			else
			{
				// Cant log in if not a member.
				$this->ci->session->unset_userdata($this->session_username);
				$this->ci->session->unset_userdata($this->session_id);
				redirect('/auth/site_closed');
				return FALSE;
			}

		}

		// ///////////////////////////////////////////////////////////////////////
		// //  Profile image support  ////////////////////////////////////////////
		// ///////////////////////////////////////////////////////////////////////

		/**
		 * grabs the profile image off the interwebs and saves it locally for thumbnail processing.
		 * Checks to see if it exists locally unless $override is TRUE
		 *
		 * @param $url
		 */
		function download_profile_image($url, $override = FALSE)
		{

			$save_path = directory_map($this->ci->config->item('auth_profile_image_directory'));
			$image_path = '';
			if ($override)
			{
				if (file_exists)
				{

				}
			}

		}

	}

