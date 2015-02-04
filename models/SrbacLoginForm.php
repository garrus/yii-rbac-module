<?php
/**
 * Created by PhpStorm.
 * User: 胡耀文Garrus
 * Date: 15-1-19
 * Time: 下午7:28
 */

class SrbacLoginForm extends CFormModel{

	public $name;
	public $stuff_no;
	public $email;
	public $mobile;
	public $password;

	/** @var  SrbacUserIdentity */
	private $_ui;

	public function rules(){
		return [
			['password', 'required'],
			['name,stuff_no,mobile', 'length', 'max' => 16],
			['email', 'email'],
		];
	}

	public function attributeLabels(){
		return SrbacUser::model()->attributeLabels();
	}

	protected function beforeValidate(){
		$this->_ui = null;
		return parent::beforeValidate();
	}

	protected function afterValidate(){
		if (!$this->hasErrors()) {
			$this->getUserIdentity();
		}
		$this->password = null;
	}

	/**
	 * @throws InvalidArgumentException
	 * @return SrbacUserIdentity
	 */
	protected function createUserIdentity(){
		$class = Yii::app()->getModule('srbac')->userIdentityClass;
		if (!$class) {
			$class = 'SrbacUserIdentity';
		} elseif (!is_subclass_of($class, 'SrbacUserIdentity')) {
			throw new InvalidArgumentException('Module srbac.userIdentityClass should be a sub class of srbac.components.SrbacUserIdentity');
		}

		return new $class([
			'name' => $this->name,
			'stuff_no' => $this->stuff_no,
			'email' => $this->email,
			'mobile' => $this->mobile,
		], $this->password);
	}

	/**
	 * @return SrbacUserIdentity
	 */
	private function getUserIdentity(){
		if (!$this->_ui) {
			$this->_ui = $ui = $this->createUserIdentity();
			if (!$ui->authenticate()) {
				$this->addError('', $ui->errorMessage);
			}
		}
		return $this->_ui;
	}

	public function sendDynamicPassword(){

		$ui = $this->getUserIdentity();
		if ($ui->getIsAuthenticated()) {
			$ui->getUser()->createDynamicPassword();
		}
	}

	/**
	 * @param string $pass
	 * @return bool
	 */
	public function validateDynamicPassword($pass){

		if ($this->hasErrors()) {
			return false;
		}
		$ui = $this->getUserIdentity();
		if ($ui->getIsAuthenticated()) {
			$user = $ui->getUser();
			if (!$user->validateDynamicPassword($pass)) {
				$this->addError('dynamic_password', '动态密码验证失败。');
			}
		}

		return !$this->hasErrors();
	}

	/**
	 * @return boolean
	 */
	public function login(){

		if ($this->hasErrors()) {
			return false;
		}
		$ui = $this->getUserIdentity();
		if ($ui->getIsAuthenticated()) {
			$webUser = Yii::app()->user;
			if ($webUser->allowAutoLogin) {
				$duration = Yii::app()->getModule('srbac')->authCookieDuration;
				if (is_int($duration) && $duration > 0) {
					Yii::app()->user->login($this->_ui, $duration);
					return true;
				}
			}
			Yii::app()->user->login($this->_ui);
			return true;
		}

		return !$this->hasErrors();
	}

	public function getUserId(){
		$ui = $this->getUserIdentity();
		if ($ui->getIsAuthenticated()) {
			return $ui->getId();
		} else {
			return null;
		}
	}

} 