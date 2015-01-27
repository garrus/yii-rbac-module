<?php
/**
 * SrbacModule class file.
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * SrbacModule is the module that loads the srbac module in the application
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac
 * @since 1.0.0
 */
class SrbacModule extends CWebModule {
//Constants
    const ICON_PACKS = "noia,tango";
    const PRIVATE_ATTRIBUTES = "_icons,_cssPublished,_imagesPublished,defaultController,controllerMap,preload,behaviors";
    const TABLE_NAMES_ERROR = "Srbac is installed but the CDBAuthManger table names in the database are different from those
    in the CDBAuthManager configuration.<br />A common mistake is that names in database are in lowercase.<br />Srbac may not work correctly!!!";

    //Private attributes
    /* @var $_icons String The path to the icons */
    private $_icons;
    /* @var $_yiiSupportedVersion String The yii version tha srbac supports */
    private $_yiiSupportedVersion = "1.1.0";
    /* @var $_version string Srbac version */
    private $_version = "1.2";
    /* @var $_cssPublished boolean If css file exists and is published */
    private $_cssPublished = false;
    /* @var $_imagesPublished boolean If images files exists and are published */
    private $_imagesPublished = false;

    // Srbac Attributes
    /* @var $debug boolean If srbac is in debug mode */
    private $_debug = false;
    /* @var $pagesize int The number of items displayed in each page */
    private $_pageSize = 15;
    /* @var $alwaysAllowed mixed The actions that are always allowed */
    private $_alwaysAllowed = array('login', 'register', 'logout');
    /* @var $userActions mixed Operations assigned to users by default */
    private $_userActions = array();
    /* @var $listBoxNumberOfLines integer The number of lines in the assign tabview listboxes */
    private $_listBoxNumberOfLines = 15;
    /* @var $iconText boolean Display text next to the icons */
    private $_iconText = true;
    /* @var $_useHeader boolean Use header or not */
    private $_showHeader = false;
    /* @var $_useFooter boolean Use footer or not */
    private $_showFooter = false;
    /* @var $_cssUrl string The url of the css file to register */
    private $_cssUrl;
    /* @deprecated $useAlwaysAllowedGui boolean */
    public $useAlwaysAllowedGui;
    /* @var $_message string A warning/error message displayed in the top of each page */
    private $_message = "";

    /* @var $userid String The primary column of the users table */
    public $userid = "id";
    /* @var $username String The username column of the users table */
    public $username = "name";
    /* @var $userclass String The name of the users Class */
    public $userclass = "SrbacUser";
	public $usertable = 'srbac_user';
    /* @var $superUser String The name of the superuser */
    public $superUser = "authorizor";
    /* @var $css string The css to use */
    public $css = "srbac.css";
    /* @var $notAuthorizedView String The view to render when unathorized access */
    public $notAuthorizedView = "srbac.views.authitem.unauthorized";
    /* @var $imagesPath string The path to srbac images */
    public $imagesPath = "srbac.images";
    /* @var $imagesPack String The images theme to use */
    public $imagesPack = "tango";
    /* @var $header String Srbac header */
    public $header = "srbac.views.authitem.header";
    /* @var $footer String Srbac footer */
    public $footer = "srbac.views.authitem.footer";
    /* @var $alwaysAllowedPath String */
    public $alwaysAllowedPath = null;
    /* @var $delimeter string The delimeter used in modules between moduleId and itemId */
    public $delimeter = "/";

	public $backendHomeUrl = '/';

	public $mailer = [];

	public $adminEmail = '';

	public $allowRegister = false;

    /**
     * this method is called when the module is being created you may place code
     * here to customize the module or the application
     */
    public function init() {

		if (!$this->adminEmail) {
			throw new InvalidArgumentException('必须指定模块'. $this->id. '的adminEmail属性。');
		}

        // import the module-level models and components
        $this->setImport(array(
            'srbac.models.*',
            'srbac.components.*',
            'srbac.controllers.SBaseController'
        ));
        //Create the translation component
        $this->setComponents(
            [
                'tr' => [
                    'class' => 'CPhpMessageSource',
                    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'messages',
                    'onMissingTranslation' => "SrbacHelper::markWords",
                ],
            ]
        );
    }

	public function getMailer(){

		if ($this->hasComponent('mailer')) {
			return $this->getComponent('mailer');
		}

		if ($this->mailer) {
			if (is_string($this->mailer)) {
				return Yii::app()->getComponent($this->mailer);
			} elseif (is_array($this->mailer)) {
				$config = $this->mailer;
				if (!isset($config['class'])) {
					$config['class'] = 'application.modules.srbac.extensions.MultiMailer.MultiMailer';
				}
				$mailer = Yii::createComponent($this->mailer);
				if ($mailer instanceof CApplicationComponent) {
					$mailer->init();
				}
				$this->setComponent('mailer', $mailer);
				return $mailer;
			}
		}

		throw new Exception('模块 '. $this->id. ' 的 mailer 选项未被正确的配置。找不到可用的邮件组件。');
	}

	public function getAssetsUrl(){

		return Yii::app()->assetManager->publish(__DIR__. DIRECTORY_SEPARATOR. 'assets', false, -1, false);
	}

    // SETTERS & GETTERS

    public function setCssUrl($cssUrl) {
        $this->_cssUrl = $cssUrl;
    }

    public function getCssUrl() {
        return $this->_cssUrl;

    }

    public function setDebug($debug) {
        if (is_bool($debug)) {
            $this->_debug = $debug;
        } else {
            throw new CException("Wrong value for srbac attribute debug in srbac configuration.
      '" . $debug . "' is not a boolean.");
        }
    }

    public function getDebug() {
        return $this->_debug;
    }

    public function setPageSize($pageSize) {
        if (is_numeric($pageSize)) {
            $this->_pageSize = (int)$pageSize;
        } else {
            throw new CException("Wrong value for srbac attribute pageSize in srbac configuration.
      '" . $pageSize . "' is not an integer.");
        }
    }

    public function getPageSize() {
        return $this->_pageSize;
    }

    public function setAlwaysAllowed($alwaysAllowed) {
        $this->_alwaysAllowed = $alwaysAllowed;
    }

    public function getAlwaysAllowed() {
        $paramAllowed = array();
		$filePath = $this->getAlwaysAllowedFile();
        if (!is_file($filePath)) {
            $handle = fopen($filePath, 'wb');
            fwrite($handle, "<?php\n return array();");
            fclose($handle);
        }
        $guiAllowed = include($filePath);
        if (!is_array($guiAllowed)) {
            $guiAllowed = array();
        }
        if (is_array($this->_alwaysAllowed)) {
            $paramAllowed = $this->_alwaysAllowed;
        } else if (is_file(Yii::getPathOfAlias($this->_alwaysAllowed) . '.php')) {
            $paramAllowed = include(Yii::getPathOfAlias($this->_alwaysAllowed) . '.php');
        } else if (is_string($this->_alwaysAllowed)) {
            $paramAllowed = preg_split('/[,]/', $this->_alwaysAllowed, -1, PREG_SPLIT_NO_EMPTY);
        }
        return array_merge($guiAllowed, $paramAllowed);
    }

    public function getAlwaysAllowedFile() {
		$path = $this->alwaysAllowedPath;
		if (!$path) {
			$path = Yii::app()->runtimePath;
		} else {
			$path = Yii::getPathOfAlias($this->alwaysAllowedPath);
		}
        return  $path. DIRECTORY_SEPARATOR . 'srbac_allowed.php';
    }

    public function setUserActions($userActions) {
        if (is_array($userActions)) {
            $this->_userActions = $userActions;
        } else {
            $this->_userActions = explode(',', $userActions);
        }
    }

    public function getUserActions() {
        return $this->_userActions;
    }

    public function setListBoxNumberOfLines($size) {
        if (is_numeric($size)) {
            $this->_listBoxNumberOfLines = (int)$size;
        } else {
            throw new CException("Wrong value for srbac attribute listBoxNumberOfLines in srbac configuration.
      '" . $size . "' is not an integer.");
        }
    }

    public function getListBoxNumberOfLines() {
        return $this->_listBoxNumberOfLines;
    }

    public function setIconText($iconText) {
        if (is_bool($iconText)) {
            $this->_iconText = $iconText;
        } else {
            throw new CException("Wrong value for srbac attribute iconText in srbac configuration.
      '" . $iconText . "' is not a boolean.");
        }
    }

    public function getIconText() {
        return $this->_iconText;
    }

    public function setShowHeader($useHeader) {
        if (is_bool($useHeader)) {
            $this->_showHeader = $useHeader;
        } else {
            throw new CException("Wrong value for srbac attribute useHeader in srbac configuration.
      '" . $useHeader . "' is not a boolean.");
        }
    }

    public function getShowHeader() {
        return $this->_showHeader;
    }

    public function setShowFooter($useFooter) {
        if (is_bool($useFooter)) {
            $this->_showFooter = $useFooter;
        } else {
            throw new CException("Wrong value for srbac attribute footer in srbac configuration.
      '" . $useFooter . "' is not a boolean.");
        }
    }

    public function getShowFooter() {
        return $this->_showFooter;
    }


    /**
     * Checks if srbac is installed by checking if Auth items table exists.
     * @return boolean Whether srbac is installed or not
     */
    public function isInstalled() {
        try {
            $tables = Yii::app()->authManager->db->schema->tableNames;
            $itemTableName = Yii::app()->authManager->itemTable;
            $itemChildTableName = Yii::app()->authManager->itemChildTable;
            $assignmentTableName = Yii::app()->authManager->assignmentTable;
            $tablePrefix = AuthItem::model()->getDbConnection()->tablePrefix;
            if (!is_null($tablePrefix)) {
                $itemTableName = preg_replace('/{{(.*?)}}/', $tablePrefix . '\1', $itemTableName);
                $itemChildTableName = preg_replace('/{{(.*?)}}/', $tablePrefix . '\1', $itemChildTableName);
                $assignmentTableName = preg_replace('/{{(.*?)}}/', $tablePrefix . '\1', $assignmentTableName);
            }
            if (in_array($itemTableName, $tables) &&
                in_array($itemChildTableName, $tables) &&
                in_array($assignmentTableName, $tables)
            ) {
                return true;
            } else {
                $tables = array_map('strtolower', $tables);
                if (in_array(strtolower($itemTableName), $tables) &&
                    in_array(strtolower($itemChildTableName), $tables) &&
                    in_array(strtolower($assignmentTableName), $tables)
                ) {
                    $this->_message = self::TABLE_NAMES_ERROR;
                    return true;
                }
            }
            return false;
        } catch (CDbException  $ex) {
            return false;
        }
    }

    /**
     * Gets the user's class
     * @return string userclass
     */
    public function getUserModel() {
        return new $this->userclass;
    }

    /**
     * this method is called before any module controller action is performed
     * you may place customized code here
     * @param CController $controller
     * @param CAction $action
     * @return boolean
     */
    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            return true;
        } else
            return false;
    }

    /**
     * Gets the path to the icon files
     * @return String The path to the icons
     */
    public function getIconsPath() {
        return $this->getAssetsUrl(). '/images/'. $this->imagesPack;
    }

    public function setIconsPath($path) {
        $this->_icons = $path;
    }

    public function getSupportedYiiVersion() {
        return $this->_yiiSupportedVersion;
    }

    public function getVersion() {
        return $this->_version;
    }

    public function isCssPublished() {
        return $this->_cssPublished;
    }

    public function isImagesPublished() {
        return $this->_imagesPublished;
    }

    public function getAttributes() {
        return get_object_vars($this);
    }

    public function getMessage() {
        if ($this->_message != '') {
            return SrbacHelper::translate('srbac', $this->_message);
        } else {
            return '';
        }
    }
}