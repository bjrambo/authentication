<?php

/**
 * vi:set sw=4 ts=4 noexpandtab fileencoding=utf8:
 * @class  authenticationAdminController
 * @author wiley(wiley@nurigo.net)
 * @brief  authenticationAdminController
 */
class authenticationAdminController extends authentication
{
	/**
	 * @brief constructor
	 */
	function init()
	{
	}

	function procAuthenticationAdminConfig()
	{
		$args = Context::getRequestVars();

		if (!trim(strip_tags($args->agreement)))
		{
			$agreement_file = _XE_PATH_ . 'files/authentication/agreement_' . Context::get('lang_type') . '.txt';
			FileHandler::removeFile($agreement_file);
			$args->agreement = NULL;
		}

		// check agreement value exist
		if ($args->agreement)
		{
			$agreement_file = _XE_PATH_ . 'files/authentication/agreement_' . Context::get('lang_type') . '.txt';
			$output = FileHandler::writeFile($agreement_file, $args->agreement);

			unset($args->agreement);
		}

		if (!$args->sender_no)
		{
			$args->sender_no = NULL;
		}
		if (!$args->message_content)
		{
			$args->message_content = NULL;
		}
		if (!$args->list)
		{
			$args->list = NULL;
		}
		if (!$args->cellphone_fieldname)
		{
			$args->cellphone_fieldname = NULL;
		}

		// save module configuration.
		$oModuleController = getController('module');
		$output = $oModuleController->updateModuleConfig('authentication', $args);

		$this->setMessage('success_saved');

		$redirectUrl = getNotEncodedUrl('', 'module', 'admin', 'act', 'dispAuthenticationAdminConfig');
		$this->setRedirectUrl($redirectUrl);
	}

	function procAuthenticationAdminDesignConfig()
	{
		$args = Context::getRequestVars();
		$oModuleController = getController('module');

		if (!$args->width)
		{
			$args->width = NULL;
		}

		$output = $oModuleController->updateModuleConfig('authentication', $args);

		$this->setMessage('success_saved');

		$redirectUrl = getNotEncodedUrl('', 'module', 'admin', 'act', 'dispAuthenticationAdminDesign');
		$this->setRedirectUrl($redirectUrl);
	}

	function procAuthenticationAdminMigrateFromMXE()
	{
		$oMemberModel = &getModel('member');
		$output = executeQueryArray('authentication.getMXEAllMappingData');
		if (!$output->toBool())
		{
			return $output;
		}
		foreach ($output->data as $key => $val)
		{
			$member_info = $oMemberModel->getMemberInfoByUserID($val->user_id);
			$args->authentication_srl = 0;
			$args->member_srl = $member_info->member_srl;
			$args->clue = $val->phone_num;
			$args->country_code = $val->country;
			$args->authcode = '01234';
			executeQuery('authentication.insertAuthenticationMember', $args);
		}
	}
}
/* End of file authentication.admin.controller.php */
/* Location: ./modules/authentication/authentication.admin.controller.php */
