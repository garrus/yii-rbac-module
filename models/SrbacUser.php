<?php

/**
 * This is the model class for table "srbac_user".
 *
 * The followings are the available columns in table 'srbac_user':
 * @property string $id
 * @property string $name
 * @property string $displayName
 * @property string $password
 * @property string $salt
 * @property string $stuff_no
 * @property string $email
 * @property string $mobile
 * @property string $create_time
 * @property string $update_time
 *
 * @property boolean $isPasswordModified
 * @property boolean $isAdmin
 *
 * @property Assignments[] $assignments
 * @property SrbacDynamicPass[] $dynamicPasses
 */
class SrbacUser extends CActiveRecord
{
	const SA_NAME = 'administrator'; // the name of super admin

	public $password_plain = '';
	public $password_plain_confirm = '';

	private $_isPasswordModified = false;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SrbacUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'srbac_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,displayName,email,stuff_no,mobile,password_plain,password_plain_confirm', 'filter', 'filter' => 'trim'),

			array('name,displayName,email', 'required'),
			array('name, stuff_no, mobile', 'length', 'max'=>15),
			array('displayName', 'length', 'max'=>32),
			array('email', 'length', 'max'=>63),
			array('email', 'email'),
			array('mobile', 'length', 'max' => 15),
			array('password_plain', 'validatePasswordStrength'),
			array('password_plain_confirm', 'compare', 'compareAttribute' => 'password_plain'),

			array('name,email,mobile,stuff_no', 'unique'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, displayName, stuff_no, email, mobile', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * Validate password's strength
	 */
	public function validatePasswordStrength(){

		if ($this->hasErrors()) return;

		$pass = $this->password_plain;
		if (empty($pass)) {
			if ($this->scenario == 'update') {
				return;
			} else {
				$this->addError('password_plain', '密码不能为空');
			}
			return;
		}
		if (strlen($pass) < 8) {
			$this->addError('password_plain', '密码过于简单。长度不能低于8位。');
			return;
		}
		$chars = count_chars($pass, 3); // 返回由所有使用了的字节值组成的字符串
		if (strlen($chars) < 3) {
			$this->addError('password_plain', '密码过于简单。所用字符数不能低于3个。');
		}

		$chars = preg_replace('/[0-9]/', '', $chars, -1, $numberCharCount);
		$chars = preg_replace('/[a-zA-Z]/', '', $chars, -1, $alphaCharCount);
		$specialCharCount = strlen($chars);
		if (($numberCharCount == 0 && $alphaCharCount == 0) ||
			($numberCharCount == 0 && $specialCharCount == 0) ||
			($alphaCharCount == 0 && $specialCharCount == 0)) {
			$this->addError('password_plain', '密码过于简单。必须包含数字、字母和特殊字符中的两种及以上。');
		}

	}

	/**
	 *
	 */
	protected function afterValidate(){
		if (!$this->hasErrors()) {
			if (!$this->salt) {
				$this->salt = substr(md5(uniqid()), 2, 16);
			}
			if ($this->password_plain) {
				$newPass = self::encryptPassword($this->password_plain, $this->salt);
				if ($newPass !== $this->password) {
					$this->password = $newPass;
					$this->_isPasswordModified = true;
				}
			}
		}
	}

	/**
	 * @return bool
	 */
	public function getIsPasswordModified(){
		return $this->_isPasswordModified;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'assignments' => [self::HAS_MANY, 'Assignments', 'userid'],
			'dynamicPasses' => [self::HAS_MANY, 'SrbacDynamicPass', 'user_id', 'index' => 'type'],
		);
	}

	public function getRoleNames(){
		return array_map(function($assignment){
			return $assignment->itemname;
		}, $this->assignments);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => '登录名',
			'displayName' => '姓名',
			'password' => '密码',
			'salt' => 'Salt',
			'stuff_no' => '工号',
			'email' => '邮箱',
			'mobile' => '手机',
			'create_time' => '创建时间',
			'update_time' => '修改时间',
			'password_plain' => $this->scenario == 'change_password' ? '新密码' : '密码',
			'password_plain_confirm' => '确认密码',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('displayName',$this->displayName,true);
		$criteria->compare('stuff_no',$this->stuff_no,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('mobile',$this->mobile,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return bool
	 */
	protected function beforeSave(){
		if ($this->isNewRecord) {
			$this->create_time = new CDbExpression('CURRENT_TIMESTAMP');
		}
		$nullableFields = ['stuff_no', 'email', 'mobile'];
		foreach ($nullableFields as $field) {
			$val = $this->getAttribute($field);
			if ($val !== null && strlen($val) === 0) {
				$this->setAttribute($field, null);
			}
		}
		if ($this->isAdmin) {
			$this->email = null;
		}

		return parent::beforeSave();
	}

	/**
	 * @param string $type
	 */
	public function createDynamicPassword($type=SrbacDynamicPass::TYPE_EMAIL){
		SrbacDynamicPass::generateForUser($this, $type);
		unset($this->dynamicPasses); // unset related records
	}

	/**
	 *
	 */
	protected function afterSave(){
		if ($this->update_time instanceof CDbExpression ||
			$this->create_time instanceof CDbExpression) {
			$this->refresh();
		}
		if ($this->isAdmin) {
			$this->email = Yii::app()->getModule('srbac')->adminEmail;
		}
	}

	protected function afterFind(){
		if ($this->isAdmin) {
			$this->email = Yii::app()->getModule('srbac')->adminEmail;
		}
	}

	/**
	 * @param string $pass
	 * @param string $type
	 * @return boolean
	 */
	public function validateDynamicPassword($pass, $type=SrbacDynamicPass::TYPE_EMAIL){
		$relations = $this->dynamicPasses;
		if (isset($relations[$type])) {
			$record = $relations[$type];
			$record->user = $this;
			return $record->validatePassword($pass);
		} else {
			return false;
		}
	}

	/**
	 * @param string $pass
	 * @return bool
	 */
	public function validatePassword($pass){
		return $this->password === self::encryptPassword($pass, $this->salt);
	}

	/**
	 * @param string $pass
	 * @param string $salt
	 * @return string
	 */
	public static function encryptPassword($pass, $salt){
		return md5(md5($pass. $salt));
	}

	/**
	 * @return bool
	 */
	public function getIsAdmin(){
		return $this->name == self::SA_NAME;
	}

}