<?php
/**
*
* @package CPFs for Autogroups Extension
* @copyright (c) 2019 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\cpfautogroup;

use phpbb\extension\base;

/**
* This ext class is optional and can be omitted if left empty.
* However you can add special (un)installation commands in the
* methods enable_step(), disable_step() and purge_step(). As it is,
* these methods are defined in \phpbb\extension\base, which this
* class extends, but you can overwrite them to give special
* instructions for those cases.
*/
class ext extends base
{
	/**
	* Enable extension if phpBB version requirement is met
	* and the Autogroups extension is installed
	*
	* @return bool
	* @access public
	*/
	public function is_enableable()
	{
		// Set globals for use in the language file
		global $ver_error, $auto_group_error;

		// Requires phpBB 3.2.0 or newer.
		$ver 		= phpbb_version_compare(PHPBB_VERSION, '3.2.0', '>=');
		// Display a custom warning message if this requirement fails.
		$ver_error 	= ($ver) ? false : true;

		// Is the Autogroups extension installed?
		$ext_manager	= $this->container->get('ext.manager');
		$auto_group 	= $ext_manager->is_enabled('phpbb/autogroups');

		// Display a custom warning message if this requirement fails.
		$auto_group_error	= ($auto_group) ? false : true;

		// Need to cater for 3.1 and 3.2
		if (phpbb_version_compare(PHPBB_VERSION, '3.2.0', '>='))
		{
			$this->container->get('language')->add_lang('ext_enable_error', 'david63/cpfautogroup');
		}
		else
		{
			$this->container->get('user')->add_lang_ext('david63/cpfautogroup', 'ext_enable_error');
		}

		return $ver && $auto_group;
	}

	/**
	* This method is required
	*/
	public function purge_step($old_state)
	{
		switch ($old_state)
		{
			case '':
				try
				{
					// Try to remove this extension from auto groups db tables
					$autogroups = $this->container->get('phpbb.autogroups.manager');
					$autogroups->purge_autogroups_type('david63.cpfauotogroup.autogroups.type.cpf_date');
					$autogroups->purge_autogroups_type('david63.cpfauotogroup.autogroups.type.cpf_int');
					$autogroups->purge_autogroups_type('david63.cpfauotogroup.autogroups.type.cpf_bool');
				}
				catch (\InvalidArgumentException $e)
				{
					// Continue
				}
				return 'autogroups';
			break;

			default:
				return parent::purge_step($old_state);
			break;
		}
	}
}
