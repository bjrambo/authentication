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
		$obj = Context::getRequestVars();

		if (!trim(strip_tags($obj->agreement)))
		{
			$agreement_file = _XE_PATH_ . 'files/authentication/agreement_' . Context::get('lang_type') . '.txt';
			FileHandler::removeFile($agreement_file);
			$obj->agreement = NULL;
		}

		// check agreement value exist
		if ($obj->agreement)
		{
			$agreement_file = _XE_PATH_ . 'files/authentication/agreement_' . Context::get('lang_type') . '.txt';
			$output = FileHandler::writeFile($agreement_file, $obj->agreement);

			unset($obj->agreement);
		}

		if (!$obj->sender_no)
		{
			$obj->sender_no = NULL;
		}
		if (!$obj->message_content)
		{
			$obj->message_content = NULL;
		}
		if (!$obj->list)
		{
			$obj->list = NULL;
		}
		if (!$obj->cellphone_fieldname)
		{
			$obj->cellphone_fieldname = NULL;
		}

		// save module configuration.
		$oModuleController = getController('module');
		$output = $oModuleController->updateModuleConfig('authentication', $obj);

		$this->setMessage('success_saved');

		$redirectUrl = getNotEncodedUrl('', 'module', 'admin', 'act', 'dispAuthenticationAdminConfig');
		$this->setRedirectUrl($redirectUrl);
	}

	function procAuthenticationAdminDesignConfig()
	{
		$obj = Context::getRequestVars();
		/** @var moduleController $oModuleController */
		$oModuleController = getController('module');

		if (!$obj->width)
		{
			$obj->width = NULL;
		}

		$output = $oModuleController->updateModuleConfig('authentication', $obj);
		if(!$output->toBool())
		{
			return $output;
		}

		$this->setMessage('success_saved');

		$redirectUrl = getNotEncodedUrl('', 'module', 'admin', 'act', 'dispAuthenticationAdminDesign');
		$this->setRedirectUrl($redirectUrl);
	}

	function procAuthenticationAdminMigrateFromMXE()
	{
		$oMemberModel = getModel('member');
		$output = executeQueryArray('authentication.getMXEAllMappingData');
		if (!$output->toBool())
		{
			return $output;
		}
		foreach ($output->data as $key => $val)
		{
			$member_info = $oMemberModel->getMemberInfoByUserID($val->user_id);
			$args = new stdClass();
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
