<?php

/**
 * vi:set sw=4 ts=4 noexpandtab fileencoding=utf8:
 * @class  authenticationModel
 * @author NURIGO(contact@nurigo.net)
 * @brief  authenticationModel
 */
class authenticationModel extends authentication
{
	/**
	 * @brief constructor
	 */
	function init()
	{
	}

	function getModuleConfig()
	{
		if (!$GLOBALS['__authentication_config__'])
		{
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('authentication');
			if (!$config->skin)
			{
				$config->skin = 'default';
			}
			if (!$config->digit_number)
			{
				$config->digit_number = 5;
			}
			if (!$config->country_code)
			{
				$config->country_code = '82';
			}
			if (!$config->resend_interval)
			{
				$config->resend_interval = 20;
			}
			if (!$config->day_try_limit)
			{
				$config->day_try_limit = 10;
			}
			if (!$config->message_content)
			{
				$config->message_content = '[핸드폰인증] %authcode% ☜  인증번호를 정확히 입력해 주세요';
			}
			if (!$config->number_overlap)
			{
				$config->number_overlap = 'Y';
			}
			$GLOBALS['__authentication_config__'] = $config;
		}
		return $GLOBALS['__authentication_config__'];
	}

	function getAuthenticationInfo($authentication_srl)
	{
		$args = new stdClass();
		$args->authentication_srl = $authentication_srl;
		$output = executeQuery('authentication.getAuthentication', $args);
		if (!$output->toBool())
		{
			return;
		}
		return $output->data;
	}

	function getAuthenticationMember($member_srl)
	{
		$args = new stdClass();
		$args->member_srl = $member_srl;
		$output = executeQuery('authentication.getAuthenticationMember', $args);
		if (!$output->toBool())
		{
			return;
		}
		return $output->data;
	}

	function getAuthenticationMemberListByClue($clue)
	{
		$args = new stdClass();
		$args->clue = $clue;
		$output = executeQueryArray('authentication.getAuthenticationMemberListByClue', $args);
		if (!$output->toBool())
		{
			return;
		}
		return $output->data;
	}

	// TODO(BJRambo): what's this. we need this?
	function _getAgreement()
	{
		$agreement_file = _XE_PATH_ . 'files/authentication/agreement_' . Context::get('lang_type') . '.txt';
		if (is_readable($agreement_file))
		{
			return FileHandler::readFile($agreement_file);
		}

		$db_info = Context::getDBInfo();
		$agreement_file = _XE_PATH_ . 'files/authentication/agreement_' . $db_info->lang_type . '.txt';
		if (is_readable($agreement_file))
		{
			return FileHandler::readFile($agreement_file);
		}

		$lang_selected = Context::loadLangSelected();
		foreach ($lang_selected as $key => $val)
		{
			$agreement_file = _XE_PATH_ . 'files/authentication/agreement_' . $key . '.txt';
			if (is_readable($agreement_file))
			{
				return FileHandler::readFile($agreement_file);
			}
		}

		return null;
	}

	function getContent()
	{
		$tpl_path = sprintf('%sskins/default/', $this->module_path);
		$tpl_file = 'index.html';

		$oTemplate = TemplateHandler::getInstance();
		return $oTemplate->compile($tpl_path, $tpl_file);
	}

	/**
	 * $obj : member info object.
	 */
	function getConfigValue(&$obj, $fieldname, $type = null)
	{
		$return_value = null;
		$config = $this->getModuleConfig();

		// 기본필드에서 확인
		if ($obj->{$fieldname})
		{
			$return_value = $obj->{$fieldname};
		}

		// 확장필드에서 확인
		if ($obj->extra_vars)
		{
			$extra_vars = unserialize($obj->extra_vars);
			if ($extra_vars->{$fieldname})
			{
				$return_value = $extra_vars->{$fieldname};
			}
		}
		if ($type == 'tel' && is_array($return_value))
		{
			$return_value = implode($return_value);
		}

		return $return_value;
	}

	function getDelayStatus()
	{
		$oTextmessageModel = &getModel('textmessage');
		$sms = $oTextmessageModel->getCoolSMS();
		$options = new stdClass();
		$options->count = 1;
		$status = $sms->status($options);

		if ($status->code || !$status)
		{
			return NULL;
		}

		if (gettype($status) == "array")
		{
			if (count($status) > 0)
			{
				return $status[0];
			}
		}
		else
		{
			return $status;
		}

		return NULL;
	}

	function getDelayStatusString($sms)
	{
		$string = NULL;
		if ($sms <= 60)
		{
			$string = "양호";
		}
		else if ($sms > 60 && $sms <= 120)
		{
			$string = "보통";
		}
		else
		{
			$string = "정체";
		}

		return $string;
	}

	function getErrorMessage($error_code)
	{
		switch ($error_code)
		{
			case "InvalidAPIKey":
				$error_message = "문자메시지 모듈의 관리자 설정을 확인해주세요.";
				break;
			case "SignatureDoesNotMatch":
				$error_message = "문자메시지 모듈의 관리자 설정을 확인해주세요.";
				break;
			case "NotEnoughBalance":
				$error_message = "잔액이 부족합니다.";
				break;
			case "InternalError":
				$error_message = "서버오류";
				break;
			default:
				$error_message = "메시지 전송 오류";
				break;
		}
		$error_message = sprintf("%s(%s)", $error_message, $error_code);

		return $error_message;
	}

	function triggerMemberMenu($in_args)
	{
		$url = getUrl('', 'module', 'authentication', 'act', 'dispAuthenticationSendMessage', 'member_srl', Context::get('target_srl'));
		$oMemberController = getController('member');
		// TODO(BJRambo): What is this... why name is test........................ 알다가도 모르겠구나..
		$oMemberController->addMemberPopupMenu($url, 'test', '', 'popup');
	}
}
/* End of file authentication.model.php */
/* Location: ./modules/authentication/authentication.model.php */
