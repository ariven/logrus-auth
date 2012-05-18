<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 *
	 * configuration options for template library
	 *
	 * view files for template are stored in:
	 * views/template/{name}
	 *
	 *
	 */

	$config['default_template']         = 'default'; // no trailing slash
	$config['template_main_tpl']        = 'main'; // main template
	$config['template_generic_wrapper'] = 'simple_wrapper'; // simple wrapper used in several locations

	/**
	 * menu list, by template
	 */
	$config['template_menus']['default'] = array('main', 'alt', 'member'); // menu sections in this template

	/**
	 * these are variable names used in template_main_tpl to point to where they are.  for example
	 * a section named header would be $section['header'], footer would be $section['footer']
	 *
	 * section 'content' is mandatory
	 */
	$config['template_sections']['default'] = array('content', 'header', 'footer', 'precontent');

	/**
	 * wrappers around successive items put in a section, $content is the only variable.
	 * If a configuration doesnt exist, items are just appended normally.
	 */
	$config['template_section_wrap']['default']['content'] = 'content_wrap'; // wrapper around successive items put in a section, $content is the only variable


	/**
	 * model or library definitions for autofill data
	 * model or library must be set with the name of the model or library to use to pull data for the content section
	 * the content is appended after any user added content, just prior to render
	 */
//$config['template_section_autodata']['default']['sidebar']['model'] = 'sidebar';
//$config['template_section_autodata']['default']['sidebar']['model_function_name'] = 'get_for_display';

	/*
	 * custom menu definitions
	 */
	$config['template_menu']['default']['main']   = 'mainmenu'; // custom menu location
	$config['template_menu']['default']['alt']    = 'altmenu'; // custom menu location
	$config['template_menu']['default']['member'] = 'membermenu'; // custom menu location

	/**
	 * head section items, optional
	 */
	$config['template_meta']['title']       = ''; // default title
	$config['template_meta']['description'] = ''; // default description
	$config['template_meta']['keywords']    = ''; // default keywords
	$config['template_meta']['author']      = 'Your Name Here'; // default author