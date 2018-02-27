<?php

/**
 * vi:set sw=4 ts=4 noexpandtab fileencoding=utf8:
 * @class  authenticationView
 * @author NURIGO(contact@nurigo.net)
 * @brief  authenticationView
 */
class authenticationView extends authentication
{
	function init()
	{
		$oAuthenticationModel = getModel('authentication');
		$config = $oAuthenticationModel->getModuleConfig();
		$config->agreement = $oAuthenticationModel->_getAgreement();
		if (!$config->skin)
		{
			$config->skin = "default";
		}
		$this->setTemplatePath($this->module_path . "skins/{$config->skin}");
	}
	
	function dispAuthenticationAuthNumber()
	{
		$oAuthenticationModel = getModel('authentication');
		$config = $oAuthenticationModel->getModuleConfig();
		$config->agreement = $oAuthenticationModel->_getAgreement();

		if ($config->authcode_time_limit)
		{
			Context::set('time_limit', $config->authcode_time_limit);
		}

		// 전송지연 현황 보여주기 
		$status = $oAuthenticationModel->getDelayStatus();
		if ($status != NULL)
		{
			$status->sms_sk = $oAuthenticationModel->getDelayStatusString($status->sms_sk_average);
			$status->sms_kt = $oAuthenticationModel->getDelayStatusString($status->sms_kt_average);
			$status->sms_lg = $oAuthenticationModel->getDelayStatusString($status->sms_lg_average);
			Context::set('status', $status);
		}

		Context::set('number_limit', $config->number_limit);
		Context::set('config', $config);
		Context::set('target_action', 'logged_auth');

		$this->setTemplateFile('index');
	}
}
/* End of file authentication.view.php */
/* Location: ./modules/authentication/authentication.view.php */
