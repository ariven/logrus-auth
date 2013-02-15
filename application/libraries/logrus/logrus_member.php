<?php

/**
 * User: ariven
 * Date: 1/19/13
 * Time: 2:46 PM
 *
 * Handled member specific functions such as creation, deletion, setting and getting fields, verification that the member
 * exists, validating email, looking up member and updating fields from an oauth2 info pack
 */

require_once('logrus_base.php');

class Logrus_member extends Logrus_base
{


	public function __construct()
	{
		parent::__construct();
		$this->load->model('logrus/member');
	}


	/**
	 * Creates a member
	 *
	 * @param $email
	 * @param $name
	 * @return bool
	 */
	public function create($email, $name)
	{
		$this->load->helper('string');
		$email = $this->clean_email($email);
		$name  = strip_tags(trim($name));

		$insert = $this->member->insert(
			array(
				 'email'        => $email,
				 'display_name' => $name,
				 'hash'         => random_string('alnum', 128), // bogus password that cannot be logged in with
			)
		);
		if ($insert)
		{
			if ($this->config->item('auth_create_default_group'))
			{
				$this->load->library('logrus/logrus_group');
				$this->logrus_group->add_group($email, $this->config->item('auth_default_group'));
			}

			return $insert;
		}

		return FALSE;
	}

	/**
	 * deletes a member
	 *
	 * @param $email
	 */
	public function delete($email)
	{
		return $this->member->delete_by('email', $this->clean_email($email));
	}

	/**
	 * Sets a members field to specified value
	 *
	 * @param $email
	 * @param $field
	 * @param $value
	 */
	public function set_field($email, $field, $value)
	{
		$member = $this->get($this->clean_email($email));
		if ($member)
		{
			return $this->member->update($member->id, array($field => $value));
		}
	}

	/**
	 * gets the value of a field (will not get profile data)
	 *
	 * @param $email
	 * @param $field
	 */
	public function get_field($email, $field)
	{
		$member = $this->get($this->clean_email($email));
		if ($member)
		{
			return $member->$field;
		}

		return FALSE;
	}


	/**
	 * checks to see if the user exists
	 *
	 * @param $email
	 */
	public function exists($email)
	{
		return $this->get($this->clean_email($email));
	}

	/**
	 * checks to see if the validation code is legit, then toggles the email validated flag
	 *
	 * @param $email
	 * @param $code
	 */
	public function validate_email($email, $code)
	{
		$member = $this->get($this->clean_email($email));
		if ($member)
		{
			$this->load->library('logrus/logrus_password');

			return $this->logrus_password->validate_reset_code($member->id, $code);
		}

		return FALSE;
	}

	/**
	 * gets member data
	 *
	 * @param $email
	 */
	public function get($email)
	{
		return $this->member->get_by('email', $this->clean_email($email));
	}

	/**
	 * gets value of field for member
	 *
	 * @param $field
	 * @param $value
	 */
	public function get_by_field($field, $value)
	{
		return $this->member->get_by(array($field => $value));
	}

	/**
	 * Updates member with data from oauth2
	 *
	 * @param $email
	 * @param $provider_name
	 * @param $token
	 * @param $data expects fields name, image and email, if image doesnt exist, it will use a gravatar for it based on the members email
	 */
	public function oauth2_update($email, $provider_name, $token, $data)
	{
		$email  = $this->clean_email($email);
		$member = $this->get($email);
		$this->load->library('logrus/logrus_profile');
		$profile         = $this->logrus_profile->get($email);
		$profile_success = TRUE;
		if (! isset($data['name']))
		{
			$data['name'] = $email;
		}
		if (! isset($data['image']))
		{
			$data['image'] = gravatar_image($email);
		}

		if (! $profile)
		{
			$this->logrus_profile->create($email);
			$profile = $this->logrus_profile->get($email);
		}
		if ($profile)
		{
			if (! $profile->profile_picture)
			{
				$this->load->helper('gravatar');
				$this->logrus_profile->set_field($email, 'profile_picture', $data['image']);
			}
		}
		if ($member)
		{
			$update_data = array(
				'login_authority' => $provider_name,
				'oauth_token'     => $token,
			);
			if (! $member->display_name)
			{
				$update_data['display_name'] = $data['email'];
			}
			$this->load->model('logrus/member');
			$update = $this->member->update_by('email', $email, $update_data);

			return $update;
		}

		return FALSE;
	}
}