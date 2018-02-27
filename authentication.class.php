<?php

/**
 * vi:set sw=4 ts=4 noexpandtab fileencoding=utf8:
 * @class  authentication
 * @author NURIGO(contact@nurigo.net)
 * @brief  authentication
 */
class authentication extends ModuleObject
{

	var $triggers = array(
		array('moduleHandler.proc', 'authentication', 'controller', 'triggerModuleHandlerProc', 'after'),
		array('member.insertMember', 'authentication', 'controller', 'triggerMemberInsert', 'after'),
		array('member.updateMember', 'authentication', 'controller', 'triggerMemberUpdate', 'after'),
		array('member.insertMember', 'authentication', 'controller', 'triggerMemberInsertBefroe', 'before'),
		array('member.deleteMember', 'authentication', 'controller', 'triggerMemberDelete', 'before'),
		array('authentication.procAuthenticationSendAuthCode', 'authentication', 'controller', 'triggerSendAuthCode', 'after'),
	);

	/**
	 * @brief Object를 텍스트의 %...% 와 치환.
	 * @param $text
	 * @param $obj
	 * @return null|string|string[]
	 */
	function mergeKeywords($text, &$obj)
	{
		if (!is_object($obj))
		{
			return $text;
		}

		foreach ($obj as $key => $val)
		{
			if (is_array($val))
			{
				$val = join($val);
			}
			if (is_string($key) && is_string($val))
			{
				if (substr($key, 0, 10) == 'extra_vars')
				{
					$val = str_replace('|@|', '-', $val);
				}
				$text = preg_replace("/%" . preg_quote($key) . "%/", $val, $text);
			}
		}
		return $text;
	}

	/**
	 * @brief 모듈 설치 실행
	 */
	function moduleInstall()
	{
		$oModuleController = getController('module');
		$oModuleModel = getModel('module');

		foreach($this->triggers as $trigger)
		{
			if(!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
			{
				$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
			}
		}
	}

	/**
	 * @brief 설치가 이상없는지 체크
	 */
	function checkUpdate()
	{
		$oDB = DB::getInstance();
		$oModuleModel = getModel('module');

		foreach($this->triggers as $trigger)
		{
			if(!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4])) return true;
		}

		return false;
	}

	/**
	 * @brief 업데이트(업그레이드)
	 */
	function moduleUpdate()
	{
		$oDB = DB::getInstance();
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');

		foreach($this->triggers as $trigger)
		{
			if(!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
			{
				$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
			}
		}
	}

	/**
	 * @brief Unintall
	 */
	function moduleUninstall()
	{
		return new Object();
	}

	/**
	 * @brief 캐시파일 재생성
	 */
	function recompileCache()
	{
	}

	public function makeObject($code = 0, $msg = 'success')
	{
		return class_exists('BaseObject') ? new BaseObject($code, $msg) : new Object($code, $msg);
	}
}
/* End of file authentication.class.php */
/* Location: ./modules/authentication/authentication.class.php */
