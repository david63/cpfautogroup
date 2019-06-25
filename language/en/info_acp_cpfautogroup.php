<?php
/**
*
* @package CPFs for Autogroups Extension
* @copyright (c) 2019 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'CPF_AUTOGROUP'					=> 'CPF Autogroup',
	'CPF_AUTOGROUP_MANAGE'			=> 'CPF autogroup settings',
	'CPF_AUTOGROUPS_TYPE_BOOLEAN'	=> 'Custom Profile - boolean',
	'CPF_AUTOGROUPS_TYPE_DATE'		=> 'Custom Profile - date',
	'CPF_AUTOGROUPS_TYPE_NUMBER'	=> 'Custom Profile - number',
	'CPF_AUTOGROUPS_TYPE_TEXT'		=> 'Custom Profile - text',

	'CPF_NOT_CONFIGURED'			=> 'CPF autogroup has not been configured for this field type.',

	'LOG_CPF_AUTOGROUP'				=> '<strong>CPF Autogroup settings updated</strong>',
));
