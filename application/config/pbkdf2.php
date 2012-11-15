<?php
	/**
	 * User: patrick
	 * Date: 11/14/12
	 * Time: 10:07 AM
	 */

	$config['hash_algorithm']       = 'sha256'; // change this to your preferred hash algorithm, i.e. can switch to sha512 if you prefer
	$config['iterations']           = 1000; // Iteration count. Higher is better, but slower. Recommended: At least 1000.
	$config['salt_bytes']           = 24;
	$config['hash_bytes']           = 24;
	$config['hash_sections']        = 4;
	$config['hash_algorithm_index'] = 0;
	$config['hash_iteration_index'] = 1;
	$config['hash_salt_index']      = 2;
	$config['hash_pbkdf2_index']    = 3;
