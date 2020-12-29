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
	$lang = [];
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
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ALL'								=> 'All',
	'AUTO'								=> 'Auto',

	'C'									=> '<strong>(C)&nbsp;</strong>',
	'CONDITIONS'						=> 'Conditions',
	'CPFAG_SETTINGS'					=> 'Settings',
	'CPF_AUTOGROUP_MANAGE_EXPLAIN'		=> 'Here you can configure the options for the Custom Profile Field Auto Groups extension.',
	'CPF_CRON_TIME'						=> 'Cron time interval',
	'CPF_CRON_TIME_EXPLAIN'				=> 'The time interval that this action is to be run as a Cron job.',
	'CPF_CRON_USE'						=> 'Use cron',
	'CPF_CRON_USE_EXPLAIN'				=> 'Do you want this action to be run as a phpBB Cron job or run automatically?',
	'CPF_DATE_TYPE_SELECT'				=> 'Select date/time interval',
	'CPF_DATE_TYPE_SELECT_EXPLAIN'		=> 'For a date field select the interval that is referenced in the “Minimum & Maximum” fields in the Autogroups settings.',
	'CPF_EVENT_TRIGGER'					=> 'Event trigger or Cron',
	'CPF_EVENT_TRIGGER_SELECT'			=> 'Event trigger',
	'CPF_EVENT_TRIGGER_SELECT_EXPLAIN'	=> 'The event that will trigger the Auto Group to update.<br>This will only be effective if Auto is selected above.',
	'CPF_FIELD'							=> 'Field name',
	'CPF_FIELD_SELECT'					=> 'Select CPF field',
	'CPF_FIELD_SELECT_EXPLAIN'			=> 'Select the CPF field that will trigger the Autogroup action.',
	'CPF_FIELD_TYPE'					=> 'Field type',
	'CPF_FIELD_TYPE_EXISTS'				=> 'The CPF field type exists - do you want to continue and overwrite this data?',
	'CPF_GROUP_SELECT'					=> 'Select group',
	'CPF_GROUP_SELECT_EXPLAIN'			=> 'Select the group from which the users will be moved from.',
	'CPF_GROUP_USE'						=> 'Select from a group',
	'CPF_GROUP_USE_EXPLAIN'				=> 'Do users need to be in a specific group before being moved?',
	'CPF_HOURS'							=> 'Hours',
	'CPF_SUMMARY'						=> 'Active CPFs',
	'CPF_USERS'							=> 'Users',
	'CPF_USERS_EXPLAIN'					=> 'Is the Auto Group update to be run for all users or just one user at a time (where possilbe)?',
	'CRON'								=> 'Cron',
	'CRON_EVENT_ERROR'					=> 'You must select either an event trigger or select to run this as a Cron job.',

	'DATE_TIME'							=> 'Time interval',
	'DATE_TYPE_ERROR'					=> 'No date/time interval selected.',

	'E'									=> '<strong>(E)&nbsp;</strong>',
	'EVERY'								=> 'Every',

	'FIELD_NAME_NOT_SELECTED'			=> 'No CPF&nbsp;field name has been selectd.',

	'INVALID_FIELD_TYPE'				=> 'The selected field type is not currently implemented.',

	'NON_ACTIVE_CPF'					=> 'Non active users',
	'CPF_NON_ACTIVE_PERIOD'				=> 'Non active',
	'CPF_NON_ACTIVE_PERIOD_EXPLAIN'		=> 'This option will select users who have not been active on the board for the selected time period.',
	'CPF_NON_POST_PERIOD'				=> 'Non post',
	'CPF_NON_POST_PERIOD_EXPLAIN'		=> 'This option will select users who have not posted on the board for the selected time period.',
	'NON_POST_CPF'						=> 'Non post users',
	'NONE'								=> 'None',
	'NOTES'								=> 'Notes - please read',
	'NOTES_BOOLEAN'						=> '<strong>Notes about boolean CPFs:</strong><br><br>There are no specific requirements for this Custom Profile Field type.',
	'NOTES_DATE'						=> '<strong>Notes about date CPFs:</strong><br><br>When using a date CPF select the date/time interval in the <em>Conditions</em> that the maximun and/or minimum value filds relate to i.e if the minimum value is for 3 years then set 3 as that value in the Auto Group configuration and select “Years” in the Configurations below.<br>To get a user to join the Auto Group then you need to set a minimum value in the Auto Group configuration.<br><br>The time periods for Months and Years are only approximate, they may be a day out.',
	'NOTES_DROPDOWN'					=> '<strong>Notes about dropdown box CPFs:</strong><br><br>This feature is not currently implementd.',
	'NOTES_GENERAL'						=> '<strong>General notes about this extension:</strong><br><br>When configuring this extension you must have created the Custom Profile Field and Group(s) that will be being used before setting these options, and these options must be set before configuring the Auto Group.<br><br>Depending on the specific requirements for the CPF then other conditions may need to be set.<br><br>You can only have <strong>one</strong> configuration for each CPF type. If you add another one then it will overwite the existing one.<br><br>Changing any of the options will not re-synchronise the Auto Groups - that will need to be done via the Auto Groups menu option in the Users and Groups tab.<br><br><strong>»» Please ensure that you read the notes for the specific field types before continuing.</strong>',
	'NOTES_GOOGLE'						=> '<strong>Notes about Google+ CPFs:</strong><br><br>This feature is not currently implementd.',
	'NOTES_NUMBERS'						=> '<strong>Notes about number CPFs:</strong><br><br>There are no specific requirements for this Custom Profile Field type.',
	'NOTES_SINGLE_TEXT'					=> '<strong>Notes about single text CPFs:</strong><br><br>This feature is not currently implementd.',
	'NOTES_TEXTAREA'					=> '<strong>Notes about textarea CPFs:</strong><br><br>This feature is not currently implementd.',
	'NOTES_URL'							=> '<strong>Notes about url CPFs:</strong><br><br>This feature is not currently implementd.',

	'SELECT_CPF_FIELD'					=> 'Select CPF field',
	'SINGLE'							=> 'Single',

	'TAB_BOOLEAN'						=> 'Boolean (Yes/No)',
	'TAB_DATE'							=> 'Date',
	'TAB_DROPDOWN'						=> 'Dropdown',
	'TAB_GENERAL'						=> 'General',
	'TAB_GOOGLEPLUS'					=> 'Google+',
	'TAB_INTEGER'						=> 'Numbers',
	'TAB_STRING'						=> 'Single text',
	'TAB_TEXT'							=> 'Textarea',
	'TAB_URL'							=> 'Url (Link)',

	'VERSION'							=> 'Version',

	'cpf_datetypes'	=>  array(
		'NONE'		=> 'Select date type',
		'MINUTE'	=> 'Minutes',
		'HOUR'		=> 'Hours',
		'DAY'		=> 'Days',
		'WEEK'		=> 'Weeks',
		'MONTH'		=> 'Months',
		'YEAR'		=> 'Years',
	),

	'cpf_event_trigger'	=> array(
		0	=> 'Select event trigger',
		1	=> 'User login',
		2	=> 'User registration',
	),

	'cpf_fieldtypes' => array(
		'bool'			=> 'Boolean',
		'date'			=> 'Date',
		'dropdown'		=> 'Dropdown',
		'googleplus'	=> 'Google+',
		'int'			=> 'Number',
		'string'		=> 'Single text',
		'text'			=> 'Textarea',
		'url'			=> 'Url',
	),
));

// Donate
$lang = array_merge($lang, array(
	'DONATE'					=> 'Donate',
	'DONATE_EXTENSIONS'			=> 'Donate to my extensions',
	'DONATE_EXTENSIONS_EXPLAIN'	=> 'This extension, as with all of my extensions, is totally free of charge. If you have benefited from using it then please consider making a donation by clicking the PayPal donation button opposite - I would appreciate it. I promise that there will be no spam nor requests for further donations, although they would always be welcome.',

	'PAYPAL_BUTTON'				=> 'Donate with PayPal button',
	'PAYPAL_TITLE'				=> 'PayPal - The safer, easier way to pay online!',
));
