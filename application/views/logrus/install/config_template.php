<?php
/**
 * User: patrick
 * Date: 2/1/13
 * Time: 9:15 AM
 *
 */

echo "<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');\r\n\r\n";

$s_template = '$config[\'%s\'] = \'%s\';' . "\r\n";
$n_template = '$config[\'%s\'] = %d;' . "\r\n";
$b_template = '$config[\'%s\'] = %s;' . "\r\n";
foreach ($vars as $key => $item)
{

	if (is_numeric($item))
	{
		printf($n_template, $key, $item);
	}
	else
	{
		if ($item == 'TRUE' or $item == 'FALSE')
		{
			printf($b_template, $key, $item);
		}
		else
		{
			printf($s_template, $key, $item);
		}

	}
}

$d_template = '$config[\'auth_tables\'][\'%s\'] = \'%s\';' . "\r\n";
foreach ($tables as $key => $item)
{
	printf($d_template, $key, $item);
}