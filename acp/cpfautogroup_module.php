<?php
/**
*
* @package CPFs for Autogroups Extension
* @copyright (c) 2019 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\cpfautogroup\acp;

class cpfautogroup_module
{
	public $u_action;

	function main($id, $mode)
	{
		global $phpbb_container;

		$this->tpl_name 	= 'cpfautogroup_manage';
		$this->page_title	= $phpbb_container->get('language')->lang('CPF_AUTOGROUP');

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('david63.cpfautogroup.acp.manage.controller');

		// Make the $u_action url available in the admin controller
		$admin_controller->set_page_url($this->u_action);

		$admin_controller->display_options();
	}
}
