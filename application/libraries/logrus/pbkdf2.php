<?php
/*
 * Derived from the public domain code at https://defuse.ca/php-pbkdf2.htm
 *
 * Password hashing with PBKDF2.
 * Author: havoc AT defuse.ca
 * www: https://defuse.ca/php-pbkdf2.htm
 */


class Pbkdf2
{
	private $hash_algorithm;
	private $iterations;
	private $salt_bytes;
	private $hash_bytes;
	private $hash_sections;
	private $hash_algorithm_index;
	private $hash_iteration_index;
	private $hash_salt_index;
	private $hash_pbkdf2_index;

	private $ci;

	function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->config('logrus_pbkdf2');

		// These constants may be changed without breaking existing hashes.
		$this->hash_algorithm       = $this->config->item('hash_algorithm');
		$this->iterations           = $this->config->item('iterations');
		$this->salt_bytes           = $this->config->item('salt_bytes');
		$this->hash_bytes           = $this->config->item('hash_bytes');
		$this->hash_sections        = $this->config->item('hash_sections');
		$this->hash_algorithm_index = $this->config->item('hash_algorithm_index');
		$this->hash_iteration_index = $this->config->item('hash_iteration_index');
		$this->hash_salt_index      = $this->config->item('hash_salt_index');
		$this->hash_pbkdf2_index    = $this->config->item('hash_pbkdf2_index');

	}

	/**
	 * Creates a hash for a password. This hash includes the hash protocol, the iterations and the salt
	 *
	 * format: algorithm:iterations:salt:hash
	 *
	 * @param $password
	 * @return string
	 */
	function create_hash($password)
	{
		// format: algorithm:iterations:salt:hash
		$salt = base64_encode(mcrypt_create_iv($this->salt_bytes, MCRYPT_DEV_URANDOM));

		$hash = $this->hash_algorithm . ":" . $this->iterations . ":" . $salt . ":" .
			base64_encode($this->derive(
							  $this->hash_algorithm,
							  $password,
							  $salt,
							  $this->iterations,
							  $this->hash_bytes,
							  TRUE
						  ));

		return $hash;
	}


	function validate_password($password, $good_hash)
	{
		$params = explode(":", $good_hash);
		if (count($params) < $this->hash_sections)
		{
			return FALSE;
		}
		$pbkdf2 = base64_decode($params[$this->hash_pbkdf2_index]);

		return $this->slow_equals(
			$pbkdf2,
			$this->derive(
				$params[$this->hash_algorithm_index],
				$password,
				$params[$this->hash_salt_index],
				(int)$params[$this->hash_iteration_index],
				strlen($pbkdf2),
				TRUE
			)
		);
	}

	/**
	 * Compares two strings $a and $b in a length constant time.
	 *
	 * @param $a
	 * @param $b
	 * @return bool
	 */
	function slow_equals($a, $b)
	{
		$diff = strlen($a) ^ strlen($b);
		for ($i = 0; $i < strlen($a) && $i < strlen($b); $i ++)
		{
			$diff |= ord($a[$i]) ^ ord($b[$i]);
		}

		return $diff === 0;
	}

	/*
	 * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
	 * $algorithm - The hash algorithm to use. Recommended: SHA256
	 * $password - The password.
	 * $salt - A salt that is unique to the password.
	 * $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
	 * $key_length - The length of the derived key in bytes.
	 * $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
	 * Returns: A $key_length-byte key derived from the password and salt.
	 *
	 * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
	 *
	 * This implementation of PBKDF2 was originally created by https://defuse.ca
	 * With improvements by http://www.variations-of-shadow.com
	 */
	function derive($algorithm, $password, $salt, $count, $key_length, $raw_output = FALSE)
	{
		$algorithm = strtolower($algorithm);
		if (! in_array($algorithm, hash_algos(), TRUE))
		{
			die('PBKDF2 ERROR: Invalid hash algorithm.');
		}
		if ($count <= 0 || $key_length <= 0)
		{
			die('PBKDF2 ERROR: Invalid parameters.');
		}

		$hash_length = strlen(hash($algorithm, "", TRUE));
		$block_count = ceil($key_length / $hash_length);

		$output = "";
		for ($i = 1; $i <= $block_count; $i ++)
		{
			// $i encoded as 4 bytes, big endian.
			$last = $salt . pack("N", $i);
			// first iteration
			$last = $xorsum = hash_hmac($algorithm, $last, $password, TRUE);
			// perform the other $count - 1 iterations
			for ($j = 1; $j < $count; $j ++)
			{
				$xorsum ^= ($last = hash_hmac($algorithm, $last, $password, TRUE));
			}
			$output .= $xorsum;
		}

		if ($raw_output)
		{
			return substr($output, 0, $key_length);
		}
		else
		{
			return bin2hex(substr($output, 0, $key_length));
		}
	}

	/**
	 * __get
	 *
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
}