<?php

	/**
	 * User: ariven
	 * Date: 5/11/12
	 * Time: 11:39 PM
	 */

	if (!function_exists('gravatar_image'))
	{
		function gravatar_image($email)
		{
			return sprintf('http://www.gravatar.com/avatar/%s?d=mm', md5(trim(strtolower($email))));
		}
	}
