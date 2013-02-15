<?php

/**
 * User: ariven
 * Date: 1/19/13
 * Time: 3:30 PM
 *
 * Base class used to extend logrus libraries with common routines.
 */


class Logrus_base
{
	protected $_ci;

	/**
	 * Initialize CodeIgniter instance variable
	 */
	public function __construct()
	{
		$this->_ci = & get_instance();
	}



	/////////////////////////////////////////////////////////////////////////////////////////////
	///  UTILITIES  /////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * cleans up an email address to be safe
	 *
	 * @param $email
	 * @return mixed
	 */
	public function clean_email($email)
	{
		return filter_var(trim(strtolower($email)), FILTER_SANITIZE_EMAIL);
	}

	/**
	 * Used for simple strings like name, etc.  removes HTML!
	 *
	 * @param $str
	 * @return mixed
	 */
	public function clean_string($str)
	{
		return strip_tags(filter_var(trim($str), FILTER_SANITIZE_STRING));
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
		return $this->_ci->$key;
	}
}