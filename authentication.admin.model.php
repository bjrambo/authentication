<?php

/**
 * vi:set sw=4 ts=4 noexpandtab fileencoding=utf8:
 * @class  authenticationAdminModel
 * @author diver(diver@coolsms.co.kr)
 * @brief  authenticationAdminModel
 */
class authenticationAdminModel extends authentication
{
	function getAuthenticationAdminNumber()
	{
		$args = new stdClass();
		$args->member_srl = Context::get('target_srl');
		$output = executeQuery('authentication.getAuthenticationMember', $args);
		if (!$output->toBool())
		{
			return;
		}
		$this->add('number', $output->data->clue);
	}
}
/* End of file authentication.admin.model.php */
/* Location: ./modules/authentication/authentication.admin.model.php */
