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

	/**
	 * @return boolean
	 */
	public function login(){

		if ($this->hasErrors()) {
			return false;
		}

		$userIdentity = new SrbacUserIdentity([
			'name' => $this->name,
			'stuff_no' => $this->stuff_no,
			'email' => $this->email,
			'mobile' => $this->mobile,
		], $this->password);

		if ($userIdentity->authenticate()) {
			Yii::app()->user->login($userIdentity, 86400);
		} else {
			$this->addError('', $userIdentity->errorMessage);
		}

		return !$this->hasErrors();
	}

} 