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
	 * @return SrbacUserIdentity
	 */
	protected function createUserIdentity(){
		return new SrbacUserIdentity([
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
			Yii::app()->user->login($this->_ui, 86400);
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