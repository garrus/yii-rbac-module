<?php
/**
 * Created by PhpStorm.
 * User: 胡耀文Garrus
 * Date: 15-1-19
 * Time: 下午5:58
 */

class SrbacUserIdentity extends CComponent implements IUserIdentity{

	public $errorMessage='';

	public $name;
	public $stuff_no;
	public $email;
	public $mobile;
	protected $password;

	/**
	 * @var SrbacUser
	 */
	protected $user;

	/**
	 * @param array $loginNames
	 * @param string $password
	 */
	public function __construct($loginNames=[], $password=''){
		foreach ($loginNames as $key => $value){
			$this->$key = $value;
		}
		$this->password = (string)$password;
	}

	/**
	 * Create an authenticated UserIdentity that can be directly put into Yii::app()->user->login().
	 *
	 * @param SrbacUser $user
	 * @return SrbacUserIdentity
	 */
	public static function createTrusted(SrbacUser $user){
		$ui = new self();
		$ui->user = $user;
		$ui->password = false;
		return $ui;
	}

	/**
	 * @return SrbacUser|null
	 * @throws BadMethodCallException
	 */
	protected function findUser(){

		$criteria = new CDbCriteria();
		$criteria->compare('name', $this->name);
		$criteria->compare('stuff_no', $this->stuff_no);
		$criteria->compare('email', $this->email);
		$criteria->compare('mobile', $this->mobile);
		return SrbacUser::model()->find($criteria);
	}


	/**
	 * Authenticates the user.
	 * The information needed to authenticate the user
	 * are usually provided in the constructor.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {

		if ($this->user && $this->password === false) {
			return true;
		}

		$user = $this->findUser();
		if (!$user) {
			$this->errorMessage = '无效的用户名。';
		} elseif ($user->validatePassword($this->password)) {
			$this->user = $user;
		} else {
			$this->errorMessage = '密码不正确。';
		}

		return $this->errorMessage === '';
	}

	/**
	 * Returns a value indicating whether the identity is authenticated.
	 * @return boolean whether the identity is valid.
	 */
	public function getIsAuthenticated() {
		return $this->user instanceof SrbacUser;
	}

	/**
	 * Returns a value that uniquely represents the identity.
	 * @return mixed a value that uniquely represents the identity (e.g. primary key value).
	 */
	public function getId() {
		return $this->user ? $this->user->id : null;
	}

	/**
	 * Returns the display name for the identity (e.g. username).
	 * @return string the display name for the identity.
	 */
	public function getName() {
		return $this->user ? $this->user->name : null;
	}

	/**
	 * Returns the additional identity information that needs to be persistent during the user session.
	 * @return array additional identity information that needs to be persistent during the user session (excluding {@link id}).
	 */
	public function getPersistentStates() {
		if ($this->user) {
			return [
				'displayName' => $this->user->displayName,
			];
		} else {
			return [];
		}

	}
}