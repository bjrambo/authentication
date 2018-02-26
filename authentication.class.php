<?php

/**
 * vi:set sw=4 ts=4 noexpandtab fileencoding=utf8:
 * @class  authentication
 * @author NURIGO(contact@nurigo.net)
 * @brief  authentication
 */
class authentication extends ModuleObject
{
	/**
	 * @brief Object를 텍스트의 %...% 와 치환.
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

	function registerTriggers()
	{
		$oModuleController = getController('module');
		$oModuleModel = getModel('module');

		if (!$oModuleModel->getTrigger('moduleHandler.proc', 'authentication', 'controller', 'triggerModuleHandlerProc', 'after'))
		{
			$oModuleController->insertTrigger('moduleHandler.proc', 'authentication', 'controller', 'triggerModuleHandlerProc', 'after');
		}

		if (!$oModuleModel->getTrigger('member.insertMember', 'authentication', 'controller', 'triggerMemberInsert', 'after'))
		{
			$oModuleController->insertTrigger('member.insertMember', 'authentication', 'controller', 'triggerMemberInsert', 'after');
		}

		if (!$oModuleModel->getTrigger('member.updateMember', 'authentication', 'controller', 'triggerMemberUpdate', 'after'))
		{
			$oModuleController->insertTrigger('member.updateMember', 'authentication', 'controller', 'triggerMemberUpdate', 'after');
		}

		// 2014/04/16 추가 외부 페이지에서 바로 procMemberInsert로 가는것을 방지
		if (!$oModuleModel->getTrigger('member.insertMember', 'authentication', 'controller', 'triggerMemberInsertBefroe', 'before'))
		{
			$oModuleController->insertTrigger('member.insertMember', 'authentication', 'controller', 'triggerMemberInsertBefore', 'before');
		}

		// 2015/05/27 회원 삭제시 트리거
		if (!$oModuleController->insertTrigger('member.deleteMember', 'authentication', 'controller', 'triggerMemberDelete', 'before'))
		{
			$oModuleController->insertTrigger('member.deleteMember', 'authentication', 'controller', 'triggerMemberDelete', 'before');
		}
	}

	/**
	 * @brief 모듈 설치 실행
	 */
	function moduleInstall()
	{
		$oModuleController = getController('module');
		$oModuleModel = getModel('module');

		$this->registerTriggers();
	}

	/**
	 * @brief 설치가 이상없는지 체크
	 */
	function checkUpdate()
	{
		$oDB = DB::getInstance();
		$oModuleModel = getModel('module');
		$oModuleController = &getController('module');

		if (!$oModuleModel->getTrigger('moduleHandler.proc', 'authentication', 'controller', 'triggerModuleHandlerProc', 'after'))
		{
			return true;
		}
		if (!$oModuleModel->getTrigger('member.insertMember', 'authentication', 'controller', 'triggerMemberInsert', 'after'))
		{
			return true;
		}
		if (!$oModuleModel->getTrigger('member.updateMember', 'authentication', 'controller', 'triggerMemberUpdate', 'after'))
		{
			return true;
		}

		// 2014/04/16 추가 외부 페이지에서 바로 procMemberInsert로 가는것을 방지
		if (!$oModuleModel->getTrigger('member.insertMember', 'authentication', 'controller', 'triggerMemberInsertBefore', 'before'))
		{
			return true;
		}

		// 2015/05/27 회원 삭제시 트리거
		if (!$oModuleModel->getTrigger('member.deleteMember', 'authentication', 'controller', 'triggerMemberDelete', 'before'))
		{
			return true;
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

		$this->registerTriggers();
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
