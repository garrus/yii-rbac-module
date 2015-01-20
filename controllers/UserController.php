<?php
/**
 * @property SrbacModule $module
 */
class UserController extends SBaseController{

	public $menu = [];
	public $layout = '/layouts/bootstrap';
	public $guestAccessible = ['login', 'logout', 'register'];
	public function init(){

		$this->checkInstallation();
		parent::init();
	}

	public function filters(){
		return [
			'accessControl',
		];
	}


	private function checkInstallation(){

		$tableExists = false;
		$adminUser = false;
		try {
			$adminUser = Yii::app()->db->createCommand('select * from '. $this->module->usertable .' where name=:name')
				->queryRow(true, ['name' => SrbacUser::SA_NAME]);
			$tableExists = true;
		} catch (CDbException $e) {
			if (strpos($e->getMessage(), 'table or view not found') === false) {
				throw new CHttpException(500, $e->getMessage());
			}
		}
		if (!$tableExists) {
			$this->createUserTable();
		}
		if (!$adminUser) {
			Helper::install(1, 0);
			$info = $this->createAdminUser();
			Yii::app()->user->login(SrbacUserIdentity::createTrusted($info['user']), 86400);
			$this->render('after_sa_creation', $info);
			Yii::app()->end();
		}
	}

	private function createUserTable(){

		$db = Yii::app()->db;
		$tableName = $this->module->usertable;
		$db->createCommand()->createTable($tableName, [
			'id' => 'int(11) unsigned auto_increment',
			'name' => 'varchar(15) not null',
			'stuff_no' => 'varchar(15) default null',
			'email' => 'varchar(63) default null',
			'mobile' => 'varchar(15) default null',

			'displayName' => 'varchar(32) not null default ""',
			'password' => 'char(32) not null',
			'salt' => 'char(16) not null',

			'create_time' => 'timestamp not null default 0',
			'update_time' => 'timestamp not null default 0 on update CURRENT_TIMESTAMP',
			'Primary Key(`id`)',
		]);

		$db->createCommand()->createIndex('user_name', $tableName, 'name', true);
		$db->createCommand()->createIndex('user_stuff_no', $tableName, 'stuff_no', true);
		$db->createCommand()->createIndex('user_email', $tableName, 'email', true);
		$db->createCommand()->createIndex('user_mobile', $tableName, 'mobile', true);
	}

	/**
	 * @throws UnexpectedValueException
	 * @internal param \CDbConnection $db
	 * @return array
	 */
	private function createAdminUser(){

		$db = Yii::app()->db;
		$trans = $db->beginTransaction();
		try {
			$user = new SrbacUser();
			$user->name = SrbacUser::SA_NAME;
			$adminPass = substr(md5(uniqid()), 0, 8);
			Yii::app()->setGlobalState('sapass', $adminPass);
			$user->password_plain = $adminPass;
			$user->password_plain_confirm = $adminPass;
			$user->displayName = 'Super Admin';
			if (!$user->save()) {
				throw new UnexpectedValueException('创建超级管理员失败。'. Utils::firstModelError($user));
			}

			Yii::app()->authManager->assign($this->module->superUser, (int)$user->id);
			$trans->commit();
		} catch (CDbException $e) {
			$trans->rollback();
			throw $e;
		}

		return ['user' => $user, 'password' => $adminPass];
	}


	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new SrbacUser;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['SrbacUser']))
		{
			$model->attributes=$_POST['SrbacUser'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['SrbacUser']))
		{
			$model->attributes=$_POST['SrbacUser'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 * @throws CHttpException
	 */
	public function actionDelete($id)
	{
		$user = $this->loadModel($id);
		if ($user->name == SrbacUser::SA_NAME) {
			throw new CHttpException(400, '禁止删除管理员帐号。');
		}
		$user->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model = new SrbacUser('search');
		//$model->with('assignments');
		$_attributes = [];
		if (isset($_GET['SrbacUser'])) {
			$_attributes = $_GET['SrbacUser'];
		} else {
			// somehow the parameters is over encoded.
			foreach ($_GET as $key => $value) {
				$key = urldecode($key);
				if (preg_match('/^SrbacUser\[(.+?)\]$/', $key, $matches)) {
					$_attributes[$matches[1]] = $value;
				}
			}
		}

		if (!empty($_attributes)) {
			$model->attributes = $_attributes;
		}

		$dataProvider = $model->search();
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'model' => $model,
		));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return SrbacUser the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=SrbacUser::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param SrbacUser $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='srbac-user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 *
	 */
	public function actionLogin(){

		$req = Yii::app()->request;
		$form = new SrbacLoginForm();

		if (isset($_POST['SrbacLoginForm'])) {
			$form->attributes = $_POST['SrbacLoginForm'];
			if ($form->validate() && $form->login()) {
				$req->redirect($this->module->backendHomeUrl);
			}
		}

		$this->render('login', ['model' => $form]);
	}

	public function actionChangePassword($name=null, $id=null){

		/** @var CWebUser $webUser */
		$webUser = Yii::app()->user;
		if ($webUser->isGuest) {
			if ($name) {
				$user = SrbacUser::model()->findByAttributes(['name' => $name]);
			} elseif ($id) {
				$user = SrbacUser::model()->findByPk($id);
			} else {
				throw new CHttpException(400, 'Bad request.');
			}
		} else {
			$user = SrbacUser::model()->findByPk($webUser->id);
		}

		if (!$user) {
			throw new CHttpException(404, '找不到用户。');
		}
		/** @var SrbacUser $user */
		$user->setScenario('change_password');

		$needValidateOriPass = true;
		if ($webUser->name == SrbacUser::SA_NAME && $user->validatePassword(Yii::app()->getGlobalState('sapass', ''))) {
			$needValidateOriPass = false;
		}

		$req = Yii::app()->request;
		if (isset($_POST['SrbacUser'])) {

			if ($needValidateOriPass) {
				$oriPass = $req->getPost('ori_pass');
				if (!$user->validatePassword($oriPass)) {
					$user->addError('ori_pass', '原密码校验失败。');
				}
			}

			if (!$user->hasErrors()) {
				$user->setAttributes($_POST['SrbacUser']);
				if ($user->validate(['password_plain', 'password_plain_confirm'])) {
					if ($user->isPasswordModified) {
						$user->saveAttributes(['password']);
					}
					$webUser->logout();
					$this->render('after_change_password', ['url' => $this->createUrl('login')]);
					return;
				}
			}
		}

		$this->render('change_password', ['model' => $user, 'needValidateOriPass' => $needValidateOriPass]);
	}

	public function actionLogout(){
		Yii::app()->user->logout();
		$this->redirect('login');
	}

	public function actionRegister(){

		$model=new SrbacUser;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['SrbacUser']))
		{
			$model->attributes=$_POST['SrbacUser'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('register',array(
			'model'=>$model,
		));
	}
}