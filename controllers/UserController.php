<?php
/**
 * @property SrbacModule $module
 */
class UserController extends SBaseController{

	public $menu = [];
	public $layout = '/layouts/bootstrap';
	public $guestAccessible = ['login', 'logout', 'register', 'quickLogin', 'update'];

	public function init(){
		$this->checkInstallation();
		parent::init();
	}

	/**
	 * @param CAction $action
	 * @return bool
	 */
	protected function beforeAction($action){

		if (Yii::app()->user->checkAccess($this->getSrbac()->superUser) ||
			!SrbacHelper::isAuthorizer()
		) {
			$this->logAccess();
			return true;
		} else {
			return parent::beforeAction($action);
		}
	}


	private function checkInstallation(){

		$adminUser = false;

		try {
			$adminUser = Yii::app()->db->createCommand('select * from '. $this->module->usertable .' where name=:name')
				->queryRow(true, ['name' => SrbacUser::SA_NAME]);
		} catch (CDbException $e) {
			if (strpos($e->getMessage(), 'table or view not found') === false) {
				throw new CHttpException(500, $e->getMessage());
			} else {
				$this->createUserTable();
			}
		}

		try {
			Yii::app()->db->createCommand('delete from srbac_dynamic_pass where expire_time<:time')->execute([':time' => time()]);
		} catch (CDbException $e) {
			if (strpos($e->getMessage(), 'table or view not found') === false) {
				throw new CHttpException(500, $e->getMessage());
			} else {
				$this->createDynamicPassTable();
			}
		}

		if (!$adminUser) {
			SrbacHelper::install(1, 0);
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
		], 'Engine=InnoDb Default Charset=utf8');

		$db->createCommand()->createIndex('user_name', $tableName, 'name', true);
		$db->createCommand()->createIndex('user_stuff_no', $tableName, 'stuff_no', true);
		$db->createCommand()->createIndex('user_email', $tableName, 'email', true);
		$db->createCommand()->createIndex('user_mobile', $tableName, 'mobile', true);

	}

	private function createDynamicPassTable(){
		$db = Yii::app()->db;
		$tableName = 'srbac_dynamic_pass';
		$db->createCommand()->createTable($tableName, [
			'id' => 'int(11) unsigned auto_increment',
			'user_id' => 'int(11) unsigned not null',
			'type' => 'varchar(15) not null',
			'password' => 'char(32) not null',
			'expire_time' => 'int(11) not null default 0',
			'create_time' => 'timestamp not null default CURRENT_TIMESTAMP',

			'Primary Key (`id`)',
		], 'Engine=InnoDb Default Charset=utf8');

		$db->createCommand()->createIndex('dynamic_pass_user_credential', $tableName, 'user_id,type', true);
		$db->createCommand()->createIndex('dynamic_pass_expire_time', $tableName, 'expire_time');
		$db->createCommand()->addForeignKey('dynamic_pass_fk_user', $tableName, 'user_id', $this->getSrbac()->usertable, 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * @throws CDbException
	 * @throws Exception
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
			$user->email = $this->getSrbac()->adminEmail;
			$adminPass = substr(md5(uniqid()), 0, 8);
			Yii::app()->setGlobalState('sapass', $adminPass);
			$user->password_plain = $adminPass;
			$user->password_plain_confirm = $adminPass;
			$user->displayName = 'Admin';
			if (!$user->save()) {
				throw new UnexpectedValueException('创建超级管理员失败。'. SrbacHelper::firstModelError($user));
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
		if ($model->isAdmin) {
			throw new CHttpException(403, '禁止修改管理员帐号。');
		}

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
		if ($user->isAdmin) {
			throw new CHttpException(403, '禁止删除管理员帐号。');
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
	public function actionLogin($email='', $dynamicPassword=''){

		/**
		 * @var CHttpRequest $req
		 * @var CHttpSession $session
		 */
		$req = Yii::app()->request;
		$session = Yii::app()->session;
		$form = new SrbacLoginForm();
		unset($_GET['email'], $_GET['dynamicPassword']);
		if ($email && $email != $this->getSrbac()->adminEmail) {
			$form->email = $email;
		}
		$dynamicPassword = $req->getPost('dynamic_password', $dynamicPassword);

		if (isset($_POST['SrbacLoginForm'])) {
			$form->attributes = $_POST['SrbacLoginForm'];
			if ($form->validate()) {
				$session['password_validated'] = $form->getUserId();
				if ($dynamicPassword) {
					if ($form->validateDynamicPassword($dynamicPassword) && $form->login()) {
						$this->afterLogin();
					}
				} else {
					$error = 0;
					try {
						$form->sendDynamicPassword();
					} catch (Exception $e) {
						$error = 1;
						if ($req->isAjaxRequest) {
							echo json_encode([
								'ret' => 1, 'msg' => $e->getMessage(),
							]);
							Yii::app()->end();
						} else {
							Yii::app()->user->setFlash('error', $e->getMessage());
						}
					}

					if (!$error) {
						if ($req->isAjaxRequest) {
							echo json_encode([
								'ret' => 0, 'msg' => '一封包含动态密码的邮件已发送到您的邮箱，请填入动态密码以完成登录。',
							]);
							Yii::app()->end();
						} else {
							Yii::app()->user->setFlash('success', '一封包含动态密码的邮件已发送到您的邮箱，请填入动态密码以完成登录。');
						}
					}
				}
			}
		}

		if ($req->isAjaxRequest) {
			if ($form->hasErrors()) {
				echo json_encode([
					'ret' => 1, 'msg' => SrbacHelper::firstModelError($form),
				]);
			} else {
				echo json_encode(['ret' => 0, 'msg' => '']);
			}
			Yii::app()->end();
		} else {
			$this->render('login', [
				'model' => $form,
				'dynamicPassword' => $dynamicPassword,
				'showRegisterLink' => $this->getSrbac()->allowRegister,
			]);
		}
	}

	private function afterLogin(){
		if (Yii::app()->user->name == SrbacUser::SA_NAME) {
			$this->redirect(['index']);
		} else {
			$this->redirect($this->module->backendHomeUrl);
		}
		Yii::app()->end(); // actually this line is not necessary
	}

	public function actionQuickLogin($id, $email, $code){

		/**
		 * @var CHttpRequest $req
		 * @var CHttpSession $session
		 */
		$session = Yii::app()->session;

		if (!empty($session['password_validated'])
			&& $session['password_validated'] == $id
			&& $email && $code
		) {
			/** @var SrbacUser $user */
			$user = SrbacUser::model()->findByPk($id);
			if ($user !== null &&
				$user->email == $email &&
				$user->validateDynamicPassword($code, SrbacDynamicPass::TYPE_EMAIL)
			) {
				Yii::app()->user->login(SrbacUserIdentity::createTrusted($user), 86400);
				$this->afterLogin();
			}
		}

		Yii::app()->user->setFlash('warning', '此链接已过期。我们需要重新验证您的密码。');
		$this->redirect(['login', 'email' => $email, 'dynamicPassword' => $code]);
	}

	public function actionChangePassword(){

		/** @var CWebUser $webUser */
		/** @var SrbacUser $user */
		$webUser = Yii::app()->user;
		$user = SrbacUser::model()->findByPk($webUser->id);

		if (!$user) {
			$webUser->logout();
			$webUser->setFlash('warning', '会话已过期，请重新登录。');
			$this->redirect(['login']);
		}

		/** @var SrbacUser $user */
		$user->setScenario('change_password');

		$needValidateOriPass = true;
		if ($user->isAdmin && $user->validatePassword(Yii::app()->getGlobalState('sapass', ''))) {
			// 如果管理员仍然在使用初始密码，那么不需要验证原密码。
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
		$this->redirect(['srbac/user/login']);
	}

	public function actionRegister(){

		if (!$this->getSrbac()->allowRegister) {
			throw new CHttpException(403, '注册功能已被禁用.');
		}

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