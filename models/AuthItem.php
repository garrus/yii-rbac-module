<?php
/**
 * AuthItem class file.
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * AuthItem is the models for authManager items (operations, tasks and roles)
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.models
 * @since 1.0.0
 *
 * The followings are the available columns in table 'authitem':
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $bizrule
 * @property string $data
 *
 */
class AuthItem extends CActiveRecord {


    public static $TYPES = array('Operation', 'Task', 'Role');
	public static $TYPE_LABELS = array('操作', '任务', '角色');
    public $oldName;

    public function getDbConnection() {
        return Yii::app()->authManager->db;
    }


    /**
     * Returns the static model of the specified AR class.
     * @return CActiveRecord the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return Yii::app()->authManager->itemTable;
    }

//  public function safeAttributes() {
//    parent::safeAttributes();
//    return array('name','type','description','bizrule','data');
//  }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('name', 'length', 'max' => 64),
            array('name, type', 'required'),
			array('name', 'unique', 'message' => '已经有名字相同的项目存在了'),
            array('type', 'numerical', 'integerOnly' => true),
            array('name,type,description,bizrule,data', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'name' => SrbacHelper::translate('srbac', 'Name'),
            'type' => SrbacHelper::translate('srbac', 'Type'),
            'description' => SrbacHelper::translate('srbac', 'Description'),
            'bizrule' => SrbacHelper::translate('srbac', 'Bizrule'),
            'data' => SrbacHelper::translate('srbac', 'Data'),
        );
    }

//  protected function beforeSave() {
//    if($this->getIsNewRecord()) {
//      $authItem = AuthItem::model()->findByPk($this->name);
//      if($authItem !== null) {
//        return false;
//      }
//    }
//    parent::beforeSave();
//  }


    protected function beforeSave() {
        $this->data = serialize($this->data);
        return parent::beforeSave();
    }

    protected function afterFind() {
        parent::afterFind();
        $this->data = unserialize($this->data);
    }

    protected function afterSave() {
        parent::afterSave();
        $this->data = unserialize($this->data);
        if ($this->oldName != $this->name) {
            $this->model()->updateByPk($this->oldName, array("name" => $this->name));
            $criteria = new CDbCriteria();
            $criteria->condition = "itemname='" . $this->oldName . "'";
            Assignments::model()->updateAll(array('itemname' => $this->name), $criteria);
            $criteria->condition = "parent='" . $this->oldName . "'";
            ItemChildren::model()->updateAll(array('parent' => $this->name), $criteria);
            $criteria->condition = "child='" . $this->oldName . "'";
            ItemChildren::model()->updateAll(array('child' => $this->name), $criteria);
            Yii::app()->user->setFlash('updateName',
                SrbacHelper::translate('srbac', 'Updating list'));
        }
    }

    protected function afterDelete() {
        parent::afterDelete();
        Assignments::model()->deleteAll("itemname='" . $this->name . "'");
        ItemChildren::model()->deleteAll("parent='" . $this->name . "'");
        ItemChildren::model()->deleteAll("child='" . $this->name . "'");
    }
}