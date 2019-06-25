<?php
/**
*
* @package CPFs for Autogroups Extension
* @copyright (c) 2019 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\cpfautogroup\acp;

class cpfautogroup_info
{
	function module()
	{
		return array(
			'filename'	=> '\david63\cpfautogroup\acp\cpfautogroup_module',
			'title'		=> 'CPF_AUTOGROUP_MANAGE',
			'modes'		=> array(
				'manage'	=> array('title' => 'CPF_AUTOGROUP_MANAGE', 'auth' => 'ext_david63/cpfautogroup && acl_a_profile', 'cat' => array('ACP_GROUPS')),
			),
		);
	}
}
