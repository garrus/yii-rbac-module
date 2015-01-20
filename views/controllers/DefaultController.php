<?php

/**
 * The default srbac controller
 */
class DefaultController extends CController {
    /**
     * The default action if no route is specified
     */
    public function actionIndex() {
        //$this->render('index');
        $this->redirect(array('authitem/frontpage'));
    }

	public function actionRepublishAssets(){
		Yii::app()->assetManager->publish(dirname(__DIR__). DIRECTORY_SEPARATOR. 'assets', false, -1, true);
		sleep(3);
		echo 'OK';
		die;
	}

	public function actionCreateAuthTables(){

		/** @var SDbAuthManager $authManager */
		$authManager = Yii::app()->authManager;

		$method = new ReflectionMethod('CDbAuthManager', 'getDbConnection');
		$method->setAccessible(true);
		/** @var CDbConnection $db */
		$db = $method->invoke($authManager);
		unset($method);

		$sqlFile = Yii::getPathOfAlias('system.web.auth'). DIRECTORY_SEPARATOR. 'schema-'. $db->getDriverName(). '.sql';
		if (!file_exists($sqlFile)) {
			echo 'Db driver "'. $db->getDriverName(). '" is not supported.';die;
		}
		$search = ['AuthAssignment', 'AuthItemChild', 'AuthItem'];
		$placeholder = [':assignment_table:', ':item_child_table:', ':item_table:'];
		$replace = [$authManager->assignmentTable, $authManager->itemChildTable, $authManager->itemTable];

		$sqlText = file_get_contents($sqlFile);
		$sqlText = trim(preg_replace(['#/\*\*([\s\S]+?)\*/#', '/^drop table(.+)/m'], '', $sqlText));
		$sqlText = str_replace($search, $placeholder, $sqlText);
		$sqlText = str_replace($placeholder, $replace, $sqlText);

		header('Content-Type: text/plain; charset=utf8');
		try {
			$db->createCommand($sqlText)->execute();
		} catch (CDbException $e) {
			echo $e->getMessage();
			Yii::app()->end();
		}

		header('Content-Type: text/plain; charset=utf8');
		echo <<<'TEXT'
-- -----------------------------
-- Tables created successfully!
-- -----------------------------

TEXT;
		echo $sqlText;
		sleep(2);
		Yii::app()->end();
	}
}