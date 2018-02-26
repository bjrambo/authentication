<?php

/**
 * vi:set sw=4 ts=4 noexpandtab fileencoding=utf8:
 * @class  authenticationController
 * @author NURIGO(contact@nurigo.net)
 * @brief  authenticationController
 */
class authenticationController extends authentication
{
	function getRandNumber($e)
	{
		$rand = '';
		for ($i = 0; $i < $e; $i++)
		{
			$rand = $rand . rand(0, 9);
		}
		return $rand;
	}

	function procAuthenticationSendAuthCode()
	{
		$oAuthenticationModel = &getModel('authentication');
		$oMemberModel = &getModel('member');
		$config = $oAuthenticationModel->getModuleConfig();
		$target_action = Context::get('target_action');

		// check variables
		$phonenum = Context::get('phonenum');

		if (preg_match('/[^0-9]/i', $phonenum))
		{
			return $this->makeObject(-1, "숫자만 입력 가능합니다.");
		}

		$country_code = Context::get('country_code');

		if (!$phonenum || !$country_code)
		{
			return $this->makeObject(-1, '국가 및 휴대폰 번호를 전부 입력해주세요.');
		}
		$reqvars = Context::getRequestVars();

		// check duplicated.
		if ($config->number_overlap == 'N' && $target_action == 'dispMemberSignUpForm')
		{
			$args = new stdClass();
			$args->clue = $phonenum;
			$output = executeQuery('authentication.getAuthenticationMemberCountByClue', $args);
			if (!$output->toBool())
			{
				return $output;
			}

			/**
			 * 중복검사시 회원 탈퇴유무도 검색한다.
			 */
			if ($output->data->count > 0)
			{
				$output = executeQueryArray('authentication.getAuthenticationMemberByClue', $args);

				foreach ($output->data as $k => $v)
				{
					$member_info = $oMemberModel->getMemberInfoByMemberSrl($v->member_srl);
					if ($member_info)
					{
						return $this->makeObject(-1, '가입하신 휴대폰 번호로 중복 가입이 불가능합니다.');
					}
				}
			}
		}

		/**
		 * 회원수정시 중복번호 검사
		 */
		if ($config->number_overlap == 'N' && $target_action == 'dispMemberModifyInfo')
		{
			$logged_info = Context::get('logged_info');
			$args = new stdClass();
			$args->member_srl = $logged_info->member_srl;
			$output = executeQuery('authentication.getAuthenticationMember', $args);
			if (!$output->toBool())
			{
				return $output;
			}

			$clue = $output->data->clue;
			if (!$output->data)
			{
				$clue = "";
			}

			/**
			 * 저장된 번호와 입력받은 번호가 다르다면 중복번호 검사를 한다.
			 */
			if ($clue != $phonenum)
			{
				$args = new stdClass();
				$args->clue = $phonenum;
				$output = executeQuery('authentication.getAuthenticationMemberCountByClue', $args);
				if (!$output->toBool())
				{
					return $output;
				}

				/**
				 * 중복검사시 회원 탈퇴유무도 검색한다.
				 */
				if ($output->data->count > 0)
				{
					$output = executeQueryArray('authentication.getAuthenticationMemberByClue', $args);

					foreach ($output->data as $k => $v)
					{
						$member_info = $oMemberModel->getMemberInfoByMemberSrl($v->member_srl);
						if ($member_info)
						{
							return $this->makeObject(-1, '가입하신 휴대폰 번호로 중복 가입이 불가능합니다.');
						}
					}
				}
			}
		}

		$trigger_output = ModuleHandler::triggerCall('authentication.procAuthenticationSendAuthCode', 'before', $reqvars);
		if (!$trigger_output->toBool())
		{
			return $trigger_output;
		}

		// generate auth-code
		$keystr = $this->getRandNumber($config->digit_number);

		// TODO(BJRambo): what the???.. make a simple.
		$today = date("Ymd", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
		$args = new stdClass();
		$args->clue = $phonenum;
		$args->regdate = $today;
		$output = executeQuery('authentication.getTryCountByClue', $args);
		if (!$output->toBool())
		{
			return $output;
		}
		unset($args);
		if ($output->data->count > $config->day_try_limit)
		{
			return $this->makeObject(-1, '잦은 인증번호 요청으로 금지되셨습니다. 1일뒤에 다시 시도해주십시오.');
		}

		// check day try limit
		$today = date("YmdHis", time() - $config->authcode_time_limit);
		$args = new stdClass();
		$args->clue = $phonenum;
		$args->regdate = $today;
		$output = executeQuery('authentication.getTryCountByClue', $args);
		if (!$output->toBool())
		{
			return $output;
		}
		unset($args);
		if ($output->data->count > 0)
		{
			return $this->makeObject(-1, $config->authcode_time_limit . '초 동안 다시 받으실 수 없습니다. 전송확인 버튼을 눌러 수신받지 못하는 사유를 확인하세요.');
		}

		// save auth info
		$args = new stdClass();
		$args->authentication_srl = getNextSequence();
		$args->country_code = $country_code;
		$args->clue = $phonenum;
		$args->authcode = $keystr;
		$args->ipaddress = $_SERVER['REMOTE_ADDR'];
		$output = executeQuery('authentication.insertAuthentication', $args);
		if (!$output->toBool())
		{
			return $output;
		}

		$_SESSION['authentication_srl'] = $args->authentication_srl;
		$this->add('authentication_srl', $args->authentication_srl);
		Context::set('authentication_srl', $_SESSION['authentication_srl']);

		$args->recipient_no = $phonenum;
		$args->sender_no = $config->sender_no;
		if ($config->message_content)
		{
			$content = str_replace(array("%authcode%"), array($keystr), $config->message_content);
			$args->content = $content;
		}
		else
		{
			$args->content = $keystr;
		}
		$args->country = $country_code;
		/** @var textmessageController $textmessageController */
		$textmessageController = getController('textmessage');

		$output = $textmessageController->sendMessage($args);
		if (!$output->toBool())
		{
			return $output;
		}
		if ($output->get('error_code'))
		{
			$error_message = $oAuthenticationModel->getErrorMessage($output->get('error_code'));
			return $this->makeObject(-1, $error_message);
		}
		$group_id = $output->get('group_id');

		$this->add('group_id', $group_id);
		$trigger_output = ModuleHandler::triggerCall('authentication.procAuthenticationSendAuthCode', 'after', $args);
		if (!$trigger_output->toBool())
		{
			return $trigger_output;
		}
		$this->setMessage('인증번호를 발송하였습니다.');
	}

	function procAuthenticationVerifyAuthCode()
	{
		$reqvars = Context::getRequestVars();

		$authentication_srl = Context::get('authentication_srl');
		$args = new stdClass();
		$args->authentication_srl = $authentication_srl;
		$output = executeQuery('authentication.getAuthentication', $args);
		if (!$output->toBool())
		{
			return $output;
		}

		$authentication_1 = Context::get('authcode');
		$authentication_2 = $output->data->authcode;

		if ($authentication_1 == $authentication_2)
		{
			$_SESSION['authentication_pass'] = 'Y';
			$args = new stdClass();
			$args->passed = 'Y';
			$args->authentication_srl = $_SESSION['authentication_srl'];
			$output = executeQuery('authentication.updateAuthentication', $args);
			if (!$output->toBool())
			{
				return $output;
			}
			$this->setMessage('인증이 완료되었습니다. 다음페이지로 이동합니다.');

			$reqvars->passed = 'Y';
			$reqvars->authentication_srl = $args->authentication_srl;
			$trigger_output = ModuleHandler::triggerCall('authentication.procAuthenticationVerifyAuthCode', 'after', $reqvars);
			if (!$trigger_output->toBool())
			{
				return $trigger_output;
			}
		}
		else
		{
			$reqvars->passed = 'N';
			$reqvars->authentication_srl = $args->authentication_srl;
			$trigger_output = ModuleHandler::triggerCall('authentication.procAuthenticationVerifyAuthCode', 'after', $reqvars);
			if (!$trigger_output->toBool())
			{
				return $trigger_output;
			}
			return $this->makeObject(-1, '인증코드가 올바르지 않습니다.');
		}
	}

	function procAuthenticationUpdateStatus()
	{
		$oTextmessageModel = &getModel('textmessage');
		$sms = $oTextmessageModel->getCoolSMS();

		$args = new stdClass();
		$args->gid = Context::get('group_id');
		$result = $sms->sent($args);
		if ($result->data)
		{
			$result = $result->data[0];
		}

		$this->add('result', $result);
	}

	function startAuthentication(&$oModule)
	{
		$oAuthenticationModel = getModel('authentication');
		$config = $oAuthenticationModel->getModuleConfig();
		$config->agreement = $oAuthenticationModel->_getAgreement();
		if (Mobile::isFromMobilePhone())
		{
			$oModule->setTemplatePath(sprintf($this->module_path . 'm.skins/%s/', $config->mskin));
		}
		else
		{
			$oModule->setTemplatePath(sprintf($this->module_path . 'skins/%s/', $config->skin));
		}

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
		Context::set('target_action', $oModule->act);

		$oLayoutModel = &getModel('layout');
		$layout_info = $oLayoutModel->getLayout($config->layout_srl);
		if ($layout_info)
		{
			$oModule->setLayoutPath($layout_info->path);
			$oModule->setLayoutFile("layout");
		}
		$oModule->setTemplateFile('index');
	}

	/**
	 * @brief 모듈핸들러 실행 후 트리거 (애드온의 after_module_proc에 대응)
	 **/
	function triggerModuleHandlerProc(&$oModule)
	{
		$oAuthenticationModel = &getModel('authentication');
		$config = $oAuthenticationModel->getModuleConfig();

		$action_list = array_filter(explode(',', $config->list));

		if (in_array(Context::get('act'), $action_list) && $_SESSION['authentication_pass'] != 'Y')
		{
			$this->startAuthentication($oModule);
		}
		return $this->makeObject();
	}

	/*
	 * 외부페이지에서 직접 procMemberInsert를 호출하지 못하게 막는다. 
	 */
	function triggerMemberInsertBefore($obj)
	{
		$oAuthenticationModel = &getModel('authentication');
		$config = $oAuthenticationModel->getModuleConfig();

		$action_list = array_filter(explode(',', $config->list));

		// 관리자가 회원추가를 할 경우 이 루틴을 패스한다.
		if (Context::get('logged_info'))
		{
			$logged_info = Context::get('logged_info');
			if ($logged_info->is_admin == 'Y')
			{
				return $this->makeObject();
			}
		}

		if (!in_array("dispMemberSignUpForm", $action_list))
		{
			return $this->makeObject();
		}

		if ($_SESSION['authentication_pass'] != 'Y')
		{
			return $this->makeObject(-1, "msg_invalid_request");
		}

		return $this->makeObject();
	}

	/*
	 * 회원가입후 member_srl과 인증정보들을 authentication_member table에 넣는다.
	 */
	function triggerMemberInsert(&$obj)
	{
		if ($_SESSION['authentication_srl'])
		{
			$args = new stdClass();
			$args->authentication_srl = $_SESSION['authentication_srl'];
			$output = executeQuery('authentication.getAuthentication', $args);
			if (!$output->toBool())
			{
				return $output;
			}
			$authinfo = $output->data;

			$args->member_srl = $obj->member_srl;
			$args->authcode = $authinfo->authcode;
			$args->clue = $authinfo->clue;
			$args->country_code = $authinfo->country_code;
			$output = executeQuery('authentication.insertAuthenticationMember', $args);
			if (!$output->toBool())
			{
				return $output;
			}
		}
	}

	/**
	 * this function will be triggered by member module after module.updateMember called.
	 */
	function triggerMemberUpdate(&$in_args)
	{
		if ($_SESSION['authentication_srl'])
		{
			$oAuthenticationModel = &getModel('authentication');;
			$authentication_config = $oAuthenticationModel->getModuleConfig();

			$args->authentication_srl = $_SESSION['authentication_srl'];
			$output = executeQuery('authentication.getAuthentication', $args);
			if (!$output->toBool())
			{
				return $output;
			}
			$authinfo = $output->data;

			$args->authcode = $authinfo->authcode;
			$args->member_srl = $in_args->member_srl;
			$args->clue = $authinfo->clue;
			$args->country_code = $authinfo->country_code;

			$output = executeQuery('authentication.deleteAuthenticationMember', $args);
			if (!$output->toBool())
			{
				return $output;
			}

			$output = executeQuery('authentication.insertAuthenticationMember', $args);
			if (!$output->toBool())
			{
				return $output;
			}
		}
	}

	/**
	 * 멤버 탈퇴/삭제시 인증받은 회원 제거
	 */
	function triggerMemberDelete(&$in_args)
	{
		if (!$in_args->member_srl)
		{
			return $this->makeObject(-1, 'msg_invalid_request');
		}
		$args->member_srl = $in_args->member_srl;
		$output = executeQuery('authentication.deleteAuthenticationMember', $args);
		if (!$output->toBool())
		{
			return $output;
		}
	}
}
/* End of file authentication.controller.php */
/* Location: ./modules/authentication/authentication.controller.php */
