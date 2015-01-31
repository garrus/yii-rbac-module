<?php

/**
 * AuthitemController class file.
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * AuthitemController is the main controller for all of the srbac actions
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.controllers
 * @since 1.0.0
 */
class AuthitemController extends SBaseController {

    /**
     * @var string specifies the default action to be 'list'.
     */
    public $defaultAction = 'frontpage';

	public $layout = '/layouts/bootstrap';
    /**
     * @var CActiveRecord the currently loaded data model instance.
     */
    private $_model;

	/**
	 * @var array
	 */
	private $_scanExcludePaths;

    public function init() {
        parent::init();
    }

    /**
     * Checks if the user has the authority role
     * @param CAction $action The current action
     * @return Boolean true if user has the authority role
     */
    protected function beforeAction($action) {

        if (!$this->getSrbac()->isInstalled() && $action->id != "install") {
            $this->redirect(array("install"));
            return false;
        }

        if ($this->getSrbac()->debug) {
			$this->logAccess();
            return true;
        }
        if (Yii::app()->user->checkAccess($this->getSrbac()->superUser) ||
            !SrbacHelper::isAuthorizer()
        ) {
			$this->logAccess();
            return true;
        } else {
            return parent::beforeAction($action);
        }
    }

    /**
     * Assigns roles to a user
     *
     * @param int $userid The user's id
     * @param String $roles The roles to assign
     * @param String $bizRules Not used yet
     * @param String $data Not used yet
     */
    private function _assignUser($userid, $roles, $bizRules, $data) {
        if ($userid) {
            $auth = Yii::app()->authManager;
            /* @var $auth CDbAuthManager */
            foreach ($roles as $role) {
                $auth->assign($role, $userid, $bizRules, $data);
            }
        }
    }

    /**
     * Revokes roles from a user
     * @param int $userid The user's id
     * @param String $roles The roles to revoke
     */
    private function _revokeUser($userid, $roles) {
        if ($userid) {
            $auth = Yii::app()->authManager;
            /* @var $auth CDbAuthManager */
            foreach ($roles as $role) {
                if ($role == $this->module->superUser) {
                    $count = Assignments::model()->count("itemname='" . $role . "'");
                    if ($count == 1) {
                        return false;
                    }
                }
                $auth->revoke($role, $userid);
            }
        }
		return true;
    }

    /**
     * Assigns child items to a parent item
     * @param String $parent The parent item
     * @param String $children The child items
     */
    private function _assignChild($parent, $children) {
        if ($parent) {
            $auth = Yii::app()->authManager;
            /* @var $auth CDbAuthManager */
            foreach ($children as $child) {
                $auth->addItemChild($parent, $child);
            }
        }
    }

    /**
     * Revokes child items from a parent item
     * @param String $parent The parent item
     * @param String $children The child items
     */
    private function _revokeChild($parent, $children) {
        if ($parent) {
            $auth = Yii::app()->authManager;
            /* @var $auth CDbAuthManager */
            foreach ($children as $child) {
                $auth->removeItemChild($parent, $child);
            }
        }
    }

    /**
     * The assignment action
     * First checks if the user is authorized to perform this action
     * Then initializes the needed variables for the assign view.
     * If there's a post back it performs the assign action
     */
    public function actionAssign() {
        //CVarDumper::dump($_POST, 5, true);
        $userid = isset($_POST[SrbacHelper::findModule('srbac')->userclass][$this->module->userid]) ?
            $_POST[SrbacHelper::findModule('srbac')->userclass][$this->module->userid] :
            "";

        //Init values
        $model = AuthItem::model();
        $data['userAssignedRoles'] = array();
        $data['userNotAssignedRoles'] = array();
        $data['roleAssignedTasks'] = array();
        $data['roleNotAssignedTasks'] = array();
        $data['taskAssignedOpers'] = array();
        $data['taskNotAssignedOpers'] = array();
        $data["assign"] = array("disabled" => true);
        $data["revoke"] = array("disabled" => true);
        $this->_setMessage("");

        $auth = Yii::app()->authManager;
        /* @var $auth CDbAuthManager */
        $authItemAssignName = isset($_POST['AuthItem']['name']['assign']) ?
            $_POST['AuthItem']['name']['assign'] : "";


        $assBizRule = isset($_POST['Assignments']['bizrule']) ?
            $_POST['Assignments']['bizrule'] : "";
        $assData = isset($_POST['Assignments']['data']) ?
            $_POST['Assignments']['data'] : "";


        $authItemRevokeName = isset($_POST['AuthItem']['name']['revoke']) ?
            $_POST['AuthItem']['name']['revoke'] : "";

        if (isset($_POST['AuthItem']['name'])) {
            if (isset($_POST['AuthItem']['name'][0])) {
                $authItemName = $_POST['AuthItem']['name'][0];
            } else {
                $authItemName = $_POST['AuthItem']['name'];
            }
        }

        $assItemName = isset($_POST['Assignments']['itemname']) ? $_POST['Assignments']['itemname'] : "";

        $assignRoles = Yii::app()->request->getParam('assignRoles', 0);
        $revokeRoles = Yii::app()->request->getParam('revokeRoles', 0);
        $assignTasks = isset($_GET['assignTasks']) ? $_GET['assignTasks'] : 0;
        $revokeTasks = isset($_GET['revokeTasks']) ? $_GET['revokeTasks'] : 0;
        $assignOpers = isset($_GET['assignOpers']) ? $_GET['assignOpers'] : 0;
        $revokeOpers = isset($_GET['revokeOpers']) ? $_GET['revokeOpers'] : 0;


        if ($assignRoles && is_array($authItemAssignName)) {

            $this->_assignUser($userid, $authItemAssignName, $assBizRule, $assData);
            $this->_setMessage(SrbacHelper::translate('srbac', 'Role(s) Assigned'));
        } else if ($revokeRoles && is_array($authItemRevokeName)) {
            $revoke = $this->_revokeUser($userid, $authItemRevokeName);
            if ($revoke) {
                $this->_setMessage(SrbacHelper::translate('srbac', 'Role(s) Revoked'));
            } else {
                $this->_setMessage(SrbacHelper::translate('srbac', 'Can\'t revoke this role'));
            }
        } else if ($assignTasks && is_array($authItemAssignName)) {
            $this->_assignChild($authItemName, $authItemAssignName);
            $this->_setMessage(SrbacHelper::translate('srbac', 'Task(s) Assigned'));
        } else if ($revokeTasks && is_array($authItemRevokeName)) {
            $this->_revokeChild($authItemName, $authItemRevokeName);
            $this->_setMessage(SrbacHelper::translate('srbac', 'Task(s) Revoked'));
        } else if ($assignOpers && is_array($authItemAssignName)) {
            $this->_assignChild($assItemName, $authItemAssignName);
            $this->_setMessage(SrbacHelper::translate('srbac', 'Operation(s) Assigned'));
        } else if ($revokeOpers && is_array($authItemRevokeName)) {
            $this->_revokeChild($assItemName, $authItemRevokeName);
            $this->_setMessage(SrbacHelper::translate('srbac', 'Operation(s) Revoked'));
        }
        //If not ajax show the assign page
        if (!Yii::app()->request->isAjaxRequest) {
            $this->render('assign', array(
                'model' => $model,
                'message' => $this->_getMessage(),
                'userid' => $userid,
                'data' => $data
            ));
        } else {
            // assign to user show the user tab
            if ($userid != "") {
                $this->_getTheRoles();
            } else if ($assignTasks != 0 || $revokeTasks != 0) {
                $this->_getTheTasks();
            } else if ($assignOpers != 0 || $revokeOpers != 0) {
                $this->_getTheOpers();
            }
        }
    }

    /**
     * Used by Ajax to get the roles of a user when he is selected in the Assign
     * roles to user tab
     */
    public function actionGetRoles() {
        $this->_setMessage("");
        $this->_getTheRoles();
    }

    /**
     * Gets the assigned and not assigned roles of the selected user
     */
    private function _getTheRoles() {
        $model = new AuthItem();
        $userid = $_POST[SrbacHelper::findModule('srbac')->userclass][$this->module->userid];
        $data['userAssignedRoles'] = SrbacHelper::getUserAssignedRoles($userid);
        $data['userNotAssignedRoles'] = SrbacHelper::getUserNotAssignedRoles($userid);
        if ($data['userAssignedRoles'] == array()) {
            $data['revoke'] = array("name" => "revokeUser", "disabled" => true);
        } else {
            $data['revoke'] = array("name" => "revokeUser");
        }
        if ($data['userNotAssignedRoles'] == array()) {
            $data['assign'] = array("name" => "assignUser", "disabled" => true);
        } else {
            $data['assign'] = array("name" => "assignUser");
        }
        $this->renderPartial('tabViews/userAjax',
            array('model' => $model, 'userid' => $userid, 'data' => $data, 'message' => $this->_getMessage()),
            false, true);
    }

    /**
     * Used by Ajax to get the tasks of a role when it is selected in the Assign
     * tasks to roles tab
     */
    public function actionGetTasks() {
        $this->_setMessage("");
        $this->_getTheTasks();
    }

    /**
     * Gets the assigned and not assigned tasks of the selected user
     */
    private function _getTheTasks() {
        $model = new AuthItem();
        $name = isset($_POST["AuthItem"]["name"][0]) ? $_POST["AuthItem"]["name"][0] : "";
        $data['roleAssignedTasks'] = SrbacHelper::getRoleAssignedTasks($name);
        $data['roleNotAssignedTasks'] = SrbacHelper::getRoleNotAssignedTasks($name);
        if ($data['roleAssignedTasks'] == array()) {
            $data['revoke'] = array("name" => "revokeTask", "disabled" => true);
        } else {
            $data['revoke'] = array("name" => "revokeTask");
        }
        if ($data['roleNotAssignedTasks'] == array()) {
            $data['assign'] = array("name" => "assignTasks", "disabled" => true);
        } else {
            $data['assign'] = array("name" => "assignTasks");
        }
        $this->renderPartial('tabViews/roleAjax',
            array('model' => $model, 'name' => $name, 'data' => $data, 'message' => $this->_getMessage()), false, true);
    }

    /**
     * Used by Ajax to get the operations of a task when he is selected in the Assign
     * operations to tasks tab
     */
    public function actionGetOpers() {
        $this->_setMessage("");
        $this->_getTheOpers();
    }

    /**
     * Gets the assigned and not assigned operations of the selected user
     */
    private function _getTheOpers() {
        $model = new AuthItem();
        $data['taskAssignedOpers'] = array();
        $data['taskNotAssignedOpers'] = array();
        $name = isset($_POST["Assignments"]["itemname"]) ?
            $_POST["Assignments"]["itemname"] :
            Yii::app()->getGlobalState("cleverName");
        if (Yii::app()->getGlobalState("cleverAssigning") && $name) {
            $data['taskAssignedOpers'] = SrbacHelper::getTaskAssignedOpers($name, true);
            $data['taskNotAssignedOpers'] = SrbacHelper::getTaskNotAssignedOpers($name, true);
        } else if ($name) {
            $data['taskAssignedOpers'] = SrbacHelper::getTaskAssignedOpers($name, false);
            $data['taskNotAssignedOpers'] = SrbacHelper::getTaskNotAssignedOpers($name, false);
        }
        if ($data['taskAssignedOpers'] == array()) {
            $data['revoke'] = array("name" => "revokeOpers", "disabled" => true);
        } else {
            $data['revoke'] = array("name" => "revokeOpers");
        }
        if ($data['taskNotAssignedOpers'] == array()) {
            $data['assign'] = array("name" => "assignOpers", "disabled" => true);
        } else {
            $data['assign'] = array("name" => "assignOpers");
        }
        $this->renderPartial('tabViews/taskAjax',
            array('model' => $model, 'name' => $name, 'data' => $data, 'message' => $this->_getMessage()), false, true);
    }

    /**
     * Shows a particular model.
     */
    public function actionShow() {
        $deleted = Yii::app()->request->getParam('deleted', false);
        $delete = Yii::app()->request->getParam('delete', false);
        $model = $this->loadAuthItem();
        $this->renderPartial('manage/show', [
			'model' => $model,
            'deleted' => $deleted,
            'updateList' => false,
            'delete' => $delete
		], false, true);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'show' page.
     */
    public function actionCreate() {
        $model = new AuthItem;
        if (isset($_POST['AuthItem'])) {
            $model->attributes = $_POST['AuthItem'];
            try {
                if ($model->save()) {

                    Yii::app()->user->setFlash('updateSuccess',
                        "'" . $model->name . "' " .
                        SrbacHelper::translate('srbac', 'created successfully'));
                    $model->data = unserialize($model->data);
                    $this->renderPartial('manage/update', array('model' => $model));
                } else {
                    $this->renderPartial('manage/create', array('model' => $model));
                }
            } catch (CDbException $exc) {
                Yii::app()->user->setFlash('updateError',
                    SrbacHelper::translate('srbac', 'Error while creating')
                    . ' ' . $model->name . "<br />" .
                    SrbacHelper::translate('srbac', 'Possible there\'s already an item with the same name'));
                $this->renderPartial('manage/create', array('model' => $model));
            }
        } else {
            $this->renderPartial('manage/create', array('model' => $model));
        }
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'show' page.
     */
    public function actionUpdate() {
        $model = $this->loadAuthItem();
        $message = "";
        if (isset($_POST['AuthItem'])) {
            $model->oldName = isset($_POST["oldName"]) ? $_POST["oldName"] : $_POST["name"];
            $model->attributes = $_POST['AuthItem'];

            if ($model->save()) {
                Yii::app()->user->setFlash('updateSuccess',
                    "'" . $model->name . "' " .
                    SrbacHelper::translate('srbac', 'updated successfully'));
            } else {

            }
        }
        $this->renderPartial('manage/update', array('model' => $model));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'list' page.
     */
    public function actionDelete() {
        if (Yii::app()->request->isAjaxRequest) {

            $this->loadAuthItem()->delete();
            //$this->processAdminCommand();
            //$criteria = new CDbCriteria;
            //$pages = new CPagination(AuthItem::model()->count($criteria));
            //$pages->pageSize = $this->module->pageSize;
            //$pages->applyLimit($criteria);
            //$sort = new CSort('AuthItem');
            //$sort->applyOrder($criteria);
            //$models = AuthItem::model()->findAll($criteria);

            Yii::app()->user->setFlash('updateName',
                SrbacHelper::translate('srbac', 'Updating list'));
            $this->renderPartial('manage/show', array(
                //'models' => $models,
                //'pages' => $pages,
                //'sort' => $sort,
                'updateList' => true,
            ), false, false);
        } else {
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Show the confirmation view for deleting auth items
     */
    public function actionConfirm() {
        $this->renderPartial('manage/show',
            array('model' => $this->loadAuthItem(), 'updateList' => false, 'delete' => true),
            false, true);
    }

    /**
     * Lists all models.
     */
    public function actionList() {
        // Get selected type
        $selectedType =
            Yii::app()->request->getParam('selectedType',
                Yii::app()->user->getState("selectedType"));
        Yii::app()->user->setState("selectedType", $selectedType);

        //Get selected name
        $selectedName =
            Yii::app()->request->getParam('name',
                Yii::app()->user->getState("selectedName"));
        Yii::app()->user->setState("selectedName", $selectedName);

        if (!Yii::app()->request->isAjaxRequest) {
            Yii::app()->user->setState("currentPage", Yii::app()->request->getParam('page', 0) - 1);
        }
        $criteria = new CDbCriteria;
        $criteria->condition = "1=1";
        if ($selectedName != "") {
            $criteria->condition .= " AND name LIKE '%" . $selectedName . "%'";
        }
        if ($selectedType != "") {
            $criteria->condition .= " AND type = " . $selectedType;
        }

		$dataProvider = new CActiveDataProvider('AuthItem', [
			'criteria' => $criteria,
			'pagination' => [
				'pageSize' => $this->module->pageSize,
				'route' => 'manage',
				'pageVar' => 'page',
				'currentPage' => Yii::app()->user->getState('currentPage'),
			]
		]);

        $this->renderPartial('manage/list', array(
			'dataProvider' => $dataProvider,
        ), false, true);
    }

    /**
     * Installs srbac (only in debug mode)
     */
    public function actionInstall() {
        if ($this->module->debug) {
            $action = Yii::app()->getRequest()->getParam("action", "");
            $demo = Yii::app()->getRequest()->getParam("demo", 0);
            if ($action) {
                $error = SrbacHelper::install($action, $demo);
                if ($error == 1) {
                    $this->render('install/overwrite', array("demo" => $demo));
                } else if ($error == 0) {
                    $this->render('install/success', array("demo" => $demo));
                } else if ($error == 2) {
                    $error = SrbacHelper::translate("srbac", "Error while installing srbac.<br />Please check your database and try again");
                    $this->render('install/error', array("demo" => $demo, "error" => $error));
                }
            } else {
                $this->render('install/install');
            }
        } else {
            $error = SrbacHelper::translate("srbac", "srbac must be in debug mode");
            $this->render("install/error", array("error" => $error));
        }
    }

    /**
     * Displayes the authitem manage page
     */
    public function actionManage() {
        $this->processAdminCommand();
        $page = Yii::app()->getRequest()->getParam("page", "");
		$isAjaxRequest = Yii::app()->request->isAjaxRequest;
        if ($isAjaxRequest || $page != "") {
            $selectedType = Yii::app()->request->getParam('selectedType', Yii::app()->user->getState("selectedType"));
        } else {
            $selectedType = "";
        }
        Yii::app()->user->setState("selectedType", $selectedType);
        $criteria = new CDbCriteria;
        if ($selectedType != "") {
            $criteria->condition = "type = " . $selectedType;
        }

        if (!$isAjaxRequest) {
            Yii::app()->user->setState("currentPage", Yii::app()->request->getParam('page', 0) - 1);
        }

		$full = Yii::app()->request->getParam('full');
		unset($_GET['full']);
		$dataProvider = new CActiveDataProvider('AuthItem', [
			'criteria' => $criteria,
			'pagination' => [
				'route' => 'manage',
				'pageSize' => $this->module->pageSize,
				'currentPage' => Yii::app()->user->getState('currentPage'),
				'pageVar' => 'page',
			],
			'sort' => [
				'attributes' => ['name', 'type'],
			]
		]);


        if ($isAjaxRequest && !$full) {
            $this->renderPartial('manage/list', array(
                'dataProvider' => $dataProvider,
                'full' => $full,
            ), false, true);
        } else if ($isAjaxRequest && $full) {
            $this->renderPartial('manage/manage', array(
				'dataProvider' => $dataProvider,
                'full' => $full,
            ), false, true);
        } else {
            $this->render('manage/manage', array(
				'dataProvider' => $dataProvider,
                'full' => $full,
            ));
        }
    }

    /**
     * Gets the authitems for the CAutocomplete textbox
     */
    public function actionAutocomplete() {
        $criteria = new CDbCriteria();
        $criteria->condition = "name LIKE :name";
        $criteria->params = array(":name" => "%" . Yii::app()->request->getParam('q') . "%");
        $items = AuthItem::model()->findAll($criteria);
		$valuesArray = [];
        foreach ($items as $item) {
            $valuesArray[] = $item->name;
        }
        echo implode("\n", $valuesArray);
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
     */
    public function loadAuthItem($id = null) {
        if ($this->_model === null) {
            $r_id = urldecode(Yii::app()->getRequest()->getParam("id", ""));
            if ($id !== null || $r_id != "")
                $this->_model = AuthItem::model()->findbyPk($id !== null ? $id : $r_id);
            if ($this->_model === null) {
				throw new CHttpException(404, 'The requested page does not exist. id = '. $id);
			}
        }
        return $this->_model;
    }

    /**
     * Executes any command triggered on the admin page.
     */
    protected function processAdminCommand() {
        if (isset($_POST['command'], $_POST['id']) && $_POST['command'] === 'delete') {
            // $this->loadAuthItem($_POST['id'])->delete();
            // reload the current page to avoid duplicated delete actions
            //$this->refresh();
        }
    }

    //TODO These messages should be replaced by flash messages

    /**
     * Sets the message that is displayed to the user
     * @param String $mess The message to show
     */
    private function _setMessage($mess) {
        Yii::app()->user->setState("message", $mess);
    }

    /**
     *
     * @return String Gets the message that will be displayed to the user
     */
    private function _getMessage() {
        return Yii::app()->user->getState("message");
    }

    /**
     * Displayes the assignments page with no user selected
     */
    public function actionAssignments() {
        $this->render('assignments', array("id" => 0));
    }

    /**
     * Show a user's assignments.The user is passed by $_GET
     */
    public function actionShowAssignments() {
        $userid = isset($_GET["id"]) ? $_GET["id"] :
            $_POST[SrbacHelper::findModule('srbac')->userclass][$this->module->userid];
        $user = $this->module->getUserModel()->findByPk($userid);
        $username = $user->{$this->module->username};
        $r = array(0 => array(0 => array()));
        if ($userid > 0) {
            $auth = Yii::app()->authManager;
            /* @var $auth CDbAuthManager */
            $ass = $auth->getAuthItems(2, $userid);
            $r = array();
            foreach ($ass as $i => $role) {
                $curRole = $role->name;
                $r[$i] = $curRole;
                $children = $auth->getItemChildren($curRole);
                $r[$i] = array();
                foreach ($children as $j => $task) {
                    $curTask = $task->name;
                    $r[$i][$j] = $curTask;
                    $grandchildren = $auth->getItemChildren($curTask);
                    $r[$i][$j] = array();
                    foreach ($grandchildren as $k => $oper) {
                        $curOper = $oper->name;
                        $r[$i][$j][$k] = $curOper;
                    }
                }
            }
            // Add always allowed opers
            $r[SrbacHelper::translate('srbac', 'AlwaysAllowed')][''] = $this->module->getAlwaysAllowed();
            $this->renderPartial('userAssignments', array('data' => $r, 'username' => $username));
        }
    }

    /**
     * Scans applications controllers and find the actions for autocreating of
     * authItems
     */
    public function actionScan() {
        if (Yii::app()->request->getParam('module') != '') {
            $controller = Yii::app()->request->getParam('module') .
                SrbacHelper::findModule('srbac')->delimeter
                . Yii::app()->request->getParam('controller');
        } else {
            $controller = Yii::app()->request->getParam('controller');
        }
        $controllerInfo = $this->_getControllerInfo($controller);
        $this->renderPartial("manage/createItems",
            array("actions" => $controllerInfo[0],
                "controller" => $controller,
                "delete" => $controllerInfo[2],
                "task" => $controllerInfo[3],
                "taskViewingExists" => $controllerInfo[4],
                "taskAdministratingExists" => $controllerInfo[5],
                "allowed" => $controllerInfo[1]),
            false, true);
    }

    /**
     * Getting a controllers actions and also th actions that are always allowed
     * return array
     * */
    private function _getControllerInfo($controller, $getAll = false) {
        $del = SrbacHelper::findModule('srbac')->delimeter;
        $actions = array();
        $allowed = array();
        $auth = Yii::app()->authManager;

        //Check if it's a module controller
        if (substr_count($controller, $del)) {
            $c = explode($del, $controller);
            $controller = $c[1];
            $modulePrefix = $c[0] . $del;
            $contPath = Yii::app()->getModule($c[0])->getControllerPath();
            $control = $contPath . DIRECTORY_SEPARATOR . str_replace(".", DIRECTORY_SEPARATOR, $controller) . ".php";
        } else {
            $modulePrefix = "";
            $contPath = Yii::app()->getControllerPath();
            $control = $contPath . DIRECTORY_SEPARATOR . str_replace(".", DIRECTORY_SEPARATOR, $controller) . ".php";
        }
		$tokens = explode('.', $controller);
		$className = array_pop($tokens);
		$controllerId = substr($className, 0, -10);
		$itemNamePrefix = $modulePrefix. (count($tokens) ? implode('.', $tokens). '.' : ''). $controllerId;

		$taskViewingExists = $auth->getAuthItem($itemNamePrefix . 'Viewing') !== null ? true : false;
        $taskAdministratingExists = $auth->getAuthItem($itemNamePrefix . 'Administrating') !== null ? true : false;
        $delete = Yii::app()->request->getParam('delete');

		if (!class_exists($className, false)) {
			require_once $control;
			if (!class_exists($className, false)) {
				throw new CHttpException(404, '未找到名为'. $className. '的控制器。');
			}
		}
		$rflClass = new ReflectionClass($className);
        foreach ($rflClass->getMethods(ReflectionMethod::IS_PUBLIC) as $rflMethod) {
			$methodName = $rflMethod->name;

			if (strpos($methodName, 'action') !== 0) continue;
			if ($methodName != 'actions') {
				$actionId = substr($methodName, 6);
				$itemName = $itemNamePrefix. $actionId;
				if ($getAll) {
					$actions[$modulePrefix . $methodName] = $itemName;
					if (in_array($itemName, $this->allowedAccess())) {
						$allowed[] = $itemName;
					}
				} else {
					if (in_array($itemName, $this->allowedAccess())) {
						$allowed[] = $itemName;
					} else {
						if ($auth->getAuthItem($itemName) === null && !$delete) {
							if (!in_array($itemName, $this->allowedAccess())) {
								$actions[$modulePrefix. $methodName] = $itemName;
							}
						} else if ($auth->getAuthItem($itemName) !== null && $delete) {
							if (!in_array($itemName, $this->allowedAccess())) {
								$actions[$modulePrefix. $methodName] = $itemName;
							}
						}
					}
				}
			} else {
				//Get actions
				$controllerObj = $rflClass->newInstanceArgs([$controllerId, $modulePrefix]);
				foreach ($rflMethod->invoke($controllerObj) as $cAction => $value) {
					$itemName = $modulePrefix. $controllerId. ucfirst($cAction);
					if ($getAll) {
						$actions[$cAction] = $itemName;
						if (in_array($itemName, $this->allowedAccess())) {
							$allowed[] = $itemName;
						}
					} else {
						if (in_array($itemName, $this->allowedAccess())) {
							$allowed[] = $itemName;
						} else {
							if ($auth->getAuthItem($itemName) === null && !$delete) {
								if (!in_array($itemName, $this->allowedAccess())) {
									$actions[$cAction] = $itemName;
								}
							} else if ($auth->getAuthItem($itemName) !== null && $delete) {
								if (!in_array($itemName, $this->allowedAccess())) {
									$actions[$cAction] = $itemName;
								}
							}
						}
					}
				}
			}
		}

		return array($actions, $allowed, $delete, $itemNamePrefix, $taskViewingExists, $taskAdministratingExists);
    }

    /**
     * Deletes autocreated authItems
     */
    public function actionAutoDeleteItems() {
        $del = SrbacHelper::findModule('srbac')->delimeter;
        $cont = str_replace("Controller", "", $_POST["controller"]);

        //Check for module controller
        $controllerArr = explode($del, $cont);
        $controller = $controllerArr[sizeof($controllerArr) - 1];


        $actions = isset($_POST["actions"]) ? $_POST["actions"] : array();
        $deleteTasks = isset($_POST["createTasks"]) ? $_POST["createTasks"] : 0;
        $tasks = isset($_POST["tasks"]) ? $_POST["tasks"] : array();
        $message = "<div style='font-weight:bold'>" . SrbacHelper::translate('srbac', 'Delete operations') . "</div>";
        foreach ($actions as $key => $action) {
            if (substr_count($action, "action") > 0) {
                //controller action
                $action = trim(preg_replace("/action/", $controller, $action, 1));
            } else {
                // actions actionstr_replace
                $action = $controller . ucfirst($action);
            }
            $auth = AuthItem::model()->findByPk($action);
            if ($auth !== null) {
                $auth->delete();
                $message .= "<div>" . $action . " " . SrbacHelper::translate('srbac', 'deleted') . "</div>";
            } else {
                $message .= "<div style='color:red;font-weight:bold'>" . SrbacHelper::translate('srbac',
                        'Error while deleting')
                    . ' ' . $action . "</div>";
            }
        }

        if ($deleteTasks) {
            $message .= "<div style='font-weight:bold'>" . SrbacHelper::translate('srbac', 'Delete tasks') . "</div>";
            foreach ($tasks as $key => $taskname) {
                $auth = AuthItem::model()->findByPk($taskname);
                if ($auth !== null) {
                    $auth->delete();
                    $message .= "<div>" . $taskname . " " . SrbacHelper::translate('srbac', 'deleted') . "</div>";
                } else {
                    $message .= "<div style='color:red;font-weight:bold'>" . SrbacHelper::translate('srbac',
                            'Error while deleting')
                        . ' ' . $taskname . "</div>";
                }
            }
        }
        echo $message;
    }

    /**
     * Autocreating of authItems
     */
    public function actionAutoCreateItems() {
        $controller = str_replace("Controller", "", $_POST["controller"]);
        $actions = isset($_POST["actions"]) ? $_POST["actions"] : array();
        $message = "";
        $createTasks = isset($_POST["createTasks"]) ? $_POST["createTasks"] : 0;
        $tasks = isset($_POST["tasks"]) ? $_POST["tasks"] : array("");

        if ($createTasks == "1") {
            $message = "<div style='font-weight:bold'>" . SrbacHelper::translate('srbac', 'Creating tasks') . "</div>";
            foreach ($tasks as $key => $taskname) {
                $auth = new AuthItem();
                $auth->name = $taskname;
                $auth->type = 1;
                try {
                    if ($auth->save()) {
                        $message .= "'" . $auth->name . "' " .
                            SrbacHelper::translate('srbac', 'created successfully') . "<br />";
                    } else {
                        $message .= "<div style='color:red;font-weight:bold'>" . SrbacHelper::translate('srbac',
                                'Error while creating')
                            . ' ' . $auth->name . '<br />' .
                            SrbacHelper::translate('srbac', 'Possible there\'s already an item with the same name') . "</div><br />";
                    }
                } catch (Exception $e) {
                    $message .= "<div style='color:red;font-weight:bold'>" . SrbacHelper::translate('srbac',
                            'Error while creating')
                        . ' ' . $auth->name . '<br />' .
                        SrbacHelper::translate('srbac', 'Possible there\'s already an item with the same name') . "</div><br />";
                }
            }
        }
        $message .= "<div style='font-weight:bold'>" . SrbacHelper::translate('srbac', 'Creating operations') . "</div>";
        foreach ($actions as $action) {
            $act = explode("action", $action, 2);
            $a = trim($controller . (count($act) > 1 ? $act[1] : ucfirst($act[0])));
            $auth = new AuthItem();
            $auth->name = $a;
            $auth->type = 0;
            try {
                if ($auth->save()) {
                    $message .= "'" . $auth->name . "' " .
                        SrbacHelper::translate('srbac', 'created successfully') . "<br />";
                    if ($createTasks == "1") {
                        if ($this->_isUserOperation($auth->name)) {
                            $this->_assignChild($tasks["user"], array($auth->name));
                        }
                        $this->_assignChild($tasks["admin"], array($auth->name));
                    }
                } else {
                    $message .= "<div style='color:red;font-weight:bold'>" . SrbacHelper::translate('srbac',
                            'Error while creating')
                        . ' ' . $auth->name . '<br />' .
                        SrbacHelper::translate('srbac', 'Possible there\'s already an item with the same name') . "</div><br />";
                }
            } catch (Exception $e) {
                $message .= "<div style='color:red;font-weight:bold'>" . SrbacHelper::translate('srbac',
                        'Error while creating')
                    . ' ' . $auth->name . '<br />' .
                    SrbacHelper::translate('srbac', 'Possible there\'s already an item with the same name') . "</div><br />";
            }
        }
        echo $message;
    }

    /**
     * Gets the controllers and the modules' controllers for the autocreating of
     * authItems
     */
    public function actionAuto() {
        $controllers = $this->_getControllers();
        $this->renderPartial("manage/wizard", array(
            'controllers' => $controllers), false, true);
    }

    /**
     * Geting all the application's and  modules controllers
     * @return array The application's and modules controllers
     */
    private function _getControllers() {
        $contPath = Yii::app()->getControllerPath();

        $controllers = $this->_scanDir($contPath);

        //Scan modules
        $modules = Yii::app()->getModules();
        $modControllers = array();
        foreach ($modules as $mod_id => $mod) {
            $moduleControllersPath = Yii::app()->getModule($mod_id)->controllerPath;
            $modControllers = $this->_scanDir($moduleControllersPath, $mod_id, "", $modControllers);
        }
        return array_merge($controllers, $modControllers);
    }

    private function _scanDir($contPath, $module = "", $subdir = "", $controllers = array()) {

        $handle = opendir($contPath);
        $del = SrbacHelper::findModule('srbac')->delimeter;
        while (($file = readdir($handle)) !== false) {
            $filePath = $contPath . DIRECTORY_SEPARATOR . $file;
			if ($this->_isPathExcluded($filePath)) {
				continue;
			}
            if (is_file($filePath)) {
                if (preg_match("/^(.+)Controller.php$/", basename($file))) {
                    if ($this->_extendsSBaseController($filePath)) {
                        $controllers[] = (($module) ? $module . $del : "") .
                            (($subdir) ? $subdir . "." : "") .
                            str_replace(".php", "", $file);
                    }
                }
            } else if (is_dir($filePath) && $file != "." && $file != "..") {
                $controllers = $this->_scanDir($filePath, $module, $file, $controllers);
            }
        }
        return $controllers;
    }

	private function _isPathExcluded($path){
		return in_array($path, $this->getScanExcludePaths());
	}

	private function getScanExcludePaths(){
		if (null === $this->_scanExcludePaths) {
			$excludePaths = [];
			$fileAlias = $this->getModule()->scanExclude;
			if ($fileAlias) {
				$filePath = Yii::getPathOfAlias($fileAlias). '.php';
				if ($filePath && file_exists($filePath)) {
					$aliases = require $filePath;
					if (!is_array($aliases)) {
						throw new Exception('File '. $filePath. ' should return an array.');
					}
					foreach ($aliases as $alias) {
						$controllerPath = Yii::getPathOfAlias($alias);
						if (!$controllerPath) {
							trigger_error('Invalid path alias found in srbac.scanExclude file: '. $alias, E_USER_WARNING);
						} else {
							$excludePaths[] = is_dir($controllerPath) ? $controllerPath : $controllerPath. '.php';
						}
					}
				} else {
					throw new InvalidArgumentException('Module srbac.scanExclude should be a valid aliases that refers to a php file.');
				}
			}
			$this->_scanExcludePaths = $excludePaths;
		}
		return $this->_scanExcludePaths;
	}

    private function _extendsSBaseController($controller) {
        $c = basename(str_replace(".php", "", $controller));
        if (!class_exists($c, false)) {
            include_once $controller;
        } else {

        }
        $cont = new $c($c);

        if ($cont instanceof SBaseController) {
            return true;
        }
        return false;
    }

    public function actionGetCleverOpers() {
        $cleverAssigning = Yii::app()->getRequest()->getParam("checked") == "true" ? 1 : 0;
        $cleverName = Yii::app()->getRequest()->getParam("name");
        Yii::app()->setGlobalState("cleverAssigning", $cleverAssigning);
        Yii::app()->setGlobalState("cleverName", $cleverName);
        $this->_getTheOpers();
    }

    /**
     *
     * @param <type> $operation
     * @return <type> Checks if an operations should be assigned to using task or not
     */
    function _isUserOperation($operation) {
        foreach ($this->module->userActions as $oper) {
            if (strpos(strtolower($operation), strtolower($oper)) > -1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Displays srbac frontpage
     */
    public function actionFrontPage() {
        $this->render('frontpage', array());
    }

    /**
     * Displays the editor for the alwaysAllowed items
     */
    public function actionEditAllowed() {
        if (!SrbacHelper::isAlwaysAllowedFileWritable()) {
            echo SrbacHelper::translate("srbac", "The always allowed file is not writeable by the server") . "<br />";
            echo "File : " . $this->module->getAlwaysAllowedFile();
            return;
        }
        $controllers = $this->_getControllers();
        foreach ($controllers as $n => $controller) {
            $info = $this->_getControllerInfo($controller, true);
            $c[$n]["title"] = $controller;
            $c[$n]["actions"] = $info[0];
            $c[$n]["allowed"] = $info[1];
        }
        $this->renderPartial('allowed', array('controllers' => $c), false, true);
    }

    public function actionSaveAllowed() {
        if (!SrbacHelper::isAlwaysAllowedFileWritable()) {
            echo SrbacHelper::translate("srbac", "The always allowed file is not writable by the server") . "<br />";
            echo "File : " . $this->module->getAlwaysAllowedFile();
            return;
        }
        $allowed = array();
        foreach ($_POST as $controller) {
            foreach ($controller as $action) {
                //Delete items
                $auth = AuthItem::model()->findByPk($action);
                if ($auth !== null) {
                    $auth->delete();
                }
                $allowed[] = $action;
            }
        }

        $handle = fopen($this->module->getAlwaysAllowedFile(), "wb");
        fwrite($handle, "<?php \n return array(\n\t'" . implode("',\n\t'", $allowed) . "'\n);\n?>");
        fclose($handle);
        $this->renderPartial("saveAllowed", array("allowed" => $allowed));
    }

    public function actionClearObsolete() {
        $obsolete = array();
        $controllers = $this->_getControllers();
        $controllers = array_map(array($this, "replace"), $controllers);
        /* @var $auth CDbAuthManager */
        $auth = Yii::app()->authManager;
        $items = array_merge($auth->tasks, $auth->operations);
        foreach ($controllers as $contId => $cont) {
            foreach ($items as $item => $val) {
                $length = strlen($cont);
                $contItem = substr($item, 0, $length);
                if ($cont == $contItem) {
                    unset($items[$item]);
                }
            }
        }
        foreach ($items as $key => $value) {
            $obsolete[$key] = $key;
        }
        $this->renderPartial("manage/clearObsolete", array("items" => $obsolete), false, true);
    }

    private function replace($value) {
        return str_replace("Controller", "", $value);
    }

    public function actionDeleteObsolete() {
        $removed = array();
        $notRemoved = array();
        if (isset($_POST["items"])) {
            $auth = Yii::app()->authManager;
            foreach ($_POST["items"] as $item) {
                if ($auth->removeAuthItem($item)) {
                    $removed[] = $item;
                } else {
                    $notRemoved[] = $item;
                }
            }
        }
        $this->renderPartial("manage/obsoleteRemoved", array("removed" => $removed, "notRemoved" => $notRemoved));
    }

}

