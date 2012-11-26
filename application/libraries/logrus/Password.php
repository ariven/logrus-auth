<?php
	/**
	 * User: patrick
	 * Date: 11/20/12
	 * Time: 8:06 AM
	 *
	 * Password is a plugin method to authenticate the password for a user.  This is modularized to allow for different
	 * methods of getting the password mechanism, i.e. local or password server
	 */


	class Password
	{

		protected $ci;

		function __construct()
		{
			$this->ci = & get_instance();
			$this->load->library('pbkdf2');
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
		 * Retrieves member record
		 *
		 * @param $username
		 */
		function get_member($username)
		{
			$this->load->model('logrus/member');
			$username = strtolower(trim(filter_var($username, FILTER_SANITIZE_EMAIL)));
			$member   = new stdClass;

			if ($username)
			{
				$the_member = $this->member->get_by('email', $username);
				if ($the_member)
				{
					foreach ($the_member as $key => $value)
					{
						$member->$key = $value;
					}

					return $member;
				}

			}

			return FALSE;
		}

		function get_member_by_id($id)
		{
			$this->load->model('logrus/member');
			$member     = new stdClass;
			$the_member = $this->member->get($id);
			if ($the_member)
			{
				foreach ($the_member as $key => $value)
				{
					$member->$key = $value;
				}

				return $member;
			}

			return FALSE;
		}

		function get_member_by_field($field, $data)
		{
			$this->load->model('logrus/member');
			$member     = new stdClass;
			$the_member = $this->member->get_by($field, $data);
			if ($the_member)
			{
				foreach ($the_member as $key => $value)
				{
					$member->$key = $value;
				}

				return $member;
			}

			return FALSE;
		}

		function create_member($email, $name)
		{
			$this->load->model('logrus/member');

			$email = trim(strtolower($email));
			$name  = trim($name);

			$member = array(
				'email'        => $email,
				'display_name' => $name,
				'hash'         => random_string('alnum', 128), // bogus password they cannot log in with
			);

			$insert = ($this->member->insert($member));
			if ($insert)
			{

				if ($this->config->item('auth_create_default_group'))
				{
					$this->load->model('logrus/groups');
					$this->load->model('logrus/member_groups');
					$group        = $this->groups->get_by('tag', $this->config->item('auth_default_group'));
					$group_result = $this->member_groups->insert(array(
																	  'group_id'  => $group->id,
																	  'member_id' => $insert
																 ));
					if (!$group_result)
					{
						$this->lg->error('Failed to create default group for member #%s ', $insert);
					}
				}

				return $insert;
			}
			else
			{
				return FALSE;
			}
		}

		function get_profile($username)
		{
			$member = $this->get_member($username);
			if ($member)
			{
				$this->load->model('logrus/profile');
				$profile = $this->profile->get_by('member_id', $member->id);
				if ($profile)
				{
					return $profile;
				}
				else
				{
					return FALSE;
				}
			}
		}

		function set_member_field($username, $field, $data)
		{

			$member = $this->get_member($username);
			print_r($member);
			if ($member)
			{
				$this->load->model('logrus/member');
				$update = $this->member->update($member->id, array($field => $data));
				if ($update)
				{
					return TRUE;
				}
			}

			return FALSE;
		}

		function set_profile_field($username, $field, $data)
		{
			$profile = $this->get_profile($username);
			if ($profile)
			{
				$this->load->model('logrus/profile');
				$update = $this->profile->update($profile->id, array($field => $data));
				if ($update)
				{
					return TRUE;
				}
			}

			return FALSE;
		}

		function get_failed_login_count($username)
		{
			$this->load->model('logrus/member');
			$this->load->model('logrus/failed_logins');
			$member = $this->get_member($username);
			if ($member)
			{
				return $this->failed_logins->failed_count($member->id);
			}
			else
			{
				return 0;
			}
		}

		function increase_failed_count($username)
		{
			$this->load->model('logrus/member');
			$this->load->model('logrus/failed_logins');
			$member = $this->get_member($username);
			if ($member)
			{
				$this->failed_logins->insert(array(
												  'member_id' => $member->id
											 ));
			}
		}

		function validate_password($password, $hash)
		{
			return $this->pbkdf2->validate_password($password, $hash);
		}

		function set_password($username, $password)
		{
			$this->load->model('logrus/member');
			$member = $this->get_member($username);
			if ($member)
			{
				$hash   = $this->pbkdf2->create_hash($password);
				$update = $this->member->update($member->id, array('hash' => $hash));
				if ($update)
				{
					return TRUE;
				}
			}

			return FALSE;
		}
	}
