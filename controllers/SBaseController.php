<?php

/**
 * SBaseController class file.
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * SBaseController must be extended by all of the applications controllers
 * if the auto srbac should be used.
 * You can import it in your main config file as<br />
 * 'import'=>array(<br />
 * 'application.modules.srbac.controllers.SBaseController',<br />
 * ),
 *
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.controllers
 * @since 1.0.2
 *
 * @property SrbacModule $srbac
 */
Yii::import('application.modules.srbac.components.*');

class SBaseController extends CController {

	public $guestAccessible = [];

	/**
	 * @return SrbacModule
	 */
	public function getSrbac(){
		return Yii::app()->getModule('srbac');
	}

    /**
     * Checks if srbac access is granted for the current user
     * @param CAction $action . The current action
     * @return boolean true if access is granted else false
     */
    protected function beforeAction($action) {

		if (in_array($action->id, $this->guestAccessible)) {
			GOTO Access_Allowed;
		}

		if (Yii::app()->user->isGuest) {
			GOTO Access_Disallowed;
		}

		$srbac = $this->getSrbac();

        $del = $srbac->delimeter;
        //srbac access
        $mod = $this->module !== null ? $this->module->id . $del : "";
        $contrArr = explode($del, $this->id);
        $contrArr[sizeof($contrArr) - 1] = ucfirst($contrArr[sizeof($contrArr) - 1]);
        $controller = implode(".", $contrArr);

        //$contr = str_replace($del, ".", $this->id);
        $access = $mod . $controller . ucfirst($this->action->id);

        //Always allow access if $access is in the allowedAccess array
        if (in_array($access, $this->allowedAccess())) {
			GOTO Access_Allowed;
        }

        //Allow access if srbac is not installed yet
        if (!$srbac->isInstalled()) {
			GOTO Access_Allowed;
        }

        //Allow access when srbac is in debug mode
        if ($srbac->getDebug()) {
			GOTO Access_Allowed;
        }

		$webUser = Yii::app()->user;
		if ($webUser->checkAccess($access) /*|| $webUser->checkAccess($srbac->superUser)*/ ) {
			GOTO Access_Allowed;
		}

		Access_Disallowed:
		$this->onUnauthorizedAccess();
		return false;

		Access_Allowed:
		$this->logAccess();
		return true;
    }

	protected function logAccess(){
		if (!Yii::app()->user->isGuest) {
			$req = Yii::app()->request;
			$log = 'User access from address '. $req->userHostAddress;
			$log .= PHP_EOL. 'url='. $req->url;
			if ($req->isAjaxRequest) {
				$log .= '  (request via XMLHttpRequest)';
			}
			if ($req->urlReferrer) {
				$log .= PHP_EOL. 'referrer='. $req->urlReferrer;
			}
			Yii::getLogger()->log($log, CLogger::LEVEL_INFO, 'srbac.access.'. strtolower(Yii::app()->request->getRequestType()));
		}
	}

    /**
     * The auth items that access is always  allowed. Configured in srbac module's
     * configuration
     * @return array The always allowed auth items
     */
    protected function allowedAccess() {
        return $this->srbac->getAlwaysAllowed();
    }

	public function renderFlash(){
		$this->renderPartial('application.modules.srbac.views.layouts._flash', [
			'flashes' => Yii::app()->user->getFlashes(),
		]);
	}


	protected function onUnauthorizedAccess() {
        /**
         *  Check if the unautorizedacces is a result of the user no longer being logged in.
         *  If so, redirect the user to the login page and after login return the user to the page they tried to open.
         *  If not, show the unautorizedacces message.
         */
        if (Yii::app()->user->isGuest) {
            Yii::app()->user->loginRequired();
        } else {
            $error = [
				'code' => 403,
            	'title' => '未被授权的访问',
            	'message' => "您需要获得管理员的授权后才可访问此项目 /{$this->route}",
			];
            //You may change the view for unauthorized access
            if (Yii::app()->request->isAjaxRequest) {

				$acceptTypes = Yii::app()->request->getAcceptTypes();
				if (strpos($acceptTypes, 'application/json') !== false ||
					strpos($acceptTypes, 'text/javascript') !== false) {
					echo json_encode([
						'ret' => $error['code'],
						'title' => $error['title'],
						'msg' => $error['message'],
					]);
					Yii::app()->end();
				} else {
					$this->renderPartial($this->getSrbac()->notAuthorizedView, $error);
				}
            } else {
				$this->layout = 'application.modules.srbac.views.layouts.bootstrap';
                $this->render($this->getSrbac()->notAuthorizedView, $error);
            }
            return false;
        }
    }

}

