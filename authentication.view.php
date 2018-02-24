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
		$oAuthenticationModel = &getModel('authentication');
		$config = $oAuthenticationModel->getModuleConfig();
		$config->agreement = $oAuthenticationModel->_getAgreement();
		if (!$config->skin)
		{
			$config->skin = "default";
		}
		$this->setTemplatePath($this->module_path . "skins/{$config->skin}");
	}

	/*
	function dispAuthenticationCompare()
	{
		$vars = Context::get('phone');
		Context::set('phone', Context::get('phone'));
		Context::set('authentication_srl', Context::get('authentication_srl'));
		$this->setTemplateFile('index');
	}
	 */
}
/* End of file authentication.view.php */
/* Location: ./modules/authentication/authentication.view.php */
