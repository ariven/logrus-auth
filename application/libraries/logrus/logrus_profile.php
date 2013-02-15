<?php

/**
 *
 * the profile is really just a stub table, intended for you to expand it with any fields that you want to add to your
 * users database..  This allows the main member table to remain uncluttered with just the basics and then you can add
 * fields to the profile table as needed.
 *
 * With this in mind, it is fairly generic, though the entire profile is supposed to be leaded into logrus_auth to make
 * it easier to access the logged in members profile.
 */
require_once('logrus_base.php');

class Logrus_profile extends Logrus_base
{
	/**
	 * init
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('logrus/profile');
	}

	/**
	 * Get a profile by email address
	 *
	 * @param $email
	 * @return mixed
	 */
	public function get($email)
	{
		$this->load->library('logrus/logrus_member');
		$member = $this->logrus_member->get($this->clean_email($email));
		if ($member)
		{
			return $this->profile->get_by(array('member_id' => $member->id));
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Create a new profile
	 *
	 * @param $email
	 * @return bool
	 */
	public function create($email)
	{
		$this->load->library('logrus/logrus_member');
		$this->load->helper('gravatar');
		$profile = $this->get($email);
		$member  = $this->logrus_member->get($email);
		if (! $profile)
		{
			if ($member)
			{
				return $this->profile->insert(array('member_id' => $member->id, 'profile_picture' => gravatar_image($email)));
			}
		}

		return TRUE;
	}

	/**
	 * Set a profile field
	 *
	 * @param $email
	 * @param $field
	 * @param $value
	 * @return bool
	 */
	public function set_field($email, $field, $value)
	{
		$profile = $this->get($email);

		if ($profile)
		{
			$update = $this->profile->update($profile->id, array($field => $value));

			return $update;
		}
		else
		{
			// no profile exists, create one
			$this->load->library('logrus/logrus_member');
			$member = $this->logrus_member->get($email);
			if ($member)
			{
				$insert = $this->profile->insert(array('member_id' => $member->id, $field => $value));

				return $insert;
			}
		}

		return FALSE;
	}

	/**
	 * Get a profile field
	 *
	 * @param $email
	 * @param $field
	 * @return bool
	 */
	public function get_field($email, $field)
	{
		$profile = $this->get($email);
		if ($profile)
		{
			return $profile->$field;
		}

		return FALSE;
	}
}