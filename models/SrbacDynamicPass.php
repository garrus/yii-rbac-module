<?php

/**
 * This is the model class for table "srbac_dynamic_pass".
 *
 * The followings are the available columns in table 'srbac_dynamic_pass':
 * @property string $id
 * @property string $user_id
 * @property string $type
 * @property string $password
 * @property integer $expire_time
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property SrbacUser $user
 */
class SrbacDynamicPass extends CActiveRecord
{

	const TYPE_EMAIL = 'email';
	const TYPE_MOBILE = 'mobile';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SrbacDynamicPass the static model class
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
		return 'srbac_dynamic_pass';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type', 'in', 'range' => [self::TYPE_EMAIL, self::TYPE_MOBILE]),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, type, password, expire_time, create_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'SrbacUser', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'type' => 'Type',
			'password' => 'Password',
			'expire_time' => 'Expire Time',
			'create_time' => 'Create Time',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('expire_time',$this->expire_time);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @param SrbacUser $user
	 * @param string $type
	 * @throws CException
	 * @throws UnexpectedValueException
	 */
	public static function generateForUser(SrbacUser $user, $type){

		SrbacDynamicPass::model()->deleteAllByAttributes(['user_id' => $user->id, 'type' => $type]);

		$model = new SrbacDynamicPass();
		$model->type = $type;
		$model->user_id = $user->id;

		$code = self::generatePassword($user->salt);
		$model->password = SrbacUser::encryptPassword($code, $user->salt);
		$model->expire_time = time() + 1800; // expired in 30 minutes
		$model->user = $user;

		if ($model->save()) {

			if ($type == self::TYPE_EMAIL) {
				/** @var MultiMailer $mailer */
				$mailer = Yii::app()->getModule('srbac')->getMailer();
				$mail = $mailer->to($user->email, $user->displayName);
				$appName = Yii::app()->name;
				$mail->subject($appName. '-管理后台动态密码验证');

				$loginUrl = Yii::app()->createAbsoluteUrl('/srbac/user/quickLogin', [
					'email' => $user->email, 'code' => $code,
				]);
				$body = <<<BODY
$user->displayName:<br><br>
您此次登录 {$appName}-管理后台 的动态密码是<br>
<h3>$code</h3>
此动态密码在30分钟内一直有效，将其填入动态密码验证框内以完成验证。<br><br>
您也可以使用下面的一次性链接进行快速登录（需要在同一个浏览器中打开）<br>
$loginUrl
BODY;

				$mail->body($body);
				if (!$mail->send()) {
					throw new CException('发送验证邮件失败。错误：'. $mail->getMultiError());
				}
			} elseif ($type === self::TYPE_MOBILE) {
				// to be done;
				throw new CException('暂不支持手机动态密码验证。请使用邮箱验证。');
			} else {
				throw new UnexpectedValueException('Unexpected execution point.');
			}
		}
	}

	public static function generatePassword($salt=''){
		$str = substr(md5(microtime().uniqid($salt, true)), 5, 12);

		$charList = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-';
		$pass = '';
		foreach (str_split($str, 3) as $chunk) {
			$index = hexdec($chunk);
			$pass .= $charList[$index >> 8]. $charList[$index % 64];
		}
		return $pass;
	}

	public function validatePassword($pass){
		return $this->expire_time > time() &&
			$this->password === SrbacUser::encryptPassword($pass, $this->user->salt);
	}
}