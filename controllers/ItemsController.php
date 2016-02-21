<?php
namespace barneyk\migration\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use barneyk\migration\models\Migration;

class ItemsController extends \yii\web\Controller
{
	public function beforeAction($action){
		if (!parent::beforeAction($action)) {
			return false;
		}
		
		if($action->id == 'init'){
			if($this->isTableInitialized()){
				return $this->redirect(Url::to(['index']));
			}
		}
		else{
			if(!$this->isTableInitialized()){
				return $this->redirect(Url::to(['init']));
			}
		}

		return true;
	}
	
	public function actionInit(){
		$tableName = $this->module->migrationTable;
		$tableNameDb = '{{%'.$this->module->migrationTable.'}}';
		Yii::$app->db->createCommand()->createTable($tableNameDb, [
			'version' => 'varchar(180) NOT NULL PRIMARY KEY',
			'apply_time' => 'integer',
		])->execute();
			
		return $this->redirect(Url::to(['index']));
	}
	
    public function actionIndex(){
		$migrations = $this->getMigrationFiles();
		$olds = $this->getOldMigrations();
		$all_migrations = array_merge($migrations,$olds);
		krsort($all_migrations);
		return $this->render('index',['migrations'=>$all_migrations,'olds'=>$olds]);
    }
	
	public function actionCreate(){
		$name = Yii::$app->request->post('name',false);
		
		if($name){
			$migration = $this->getMigration();
			$migration->runAction('create', ['migrationPath' => $this->module->migrationPath, 'interactive' => false], ['name'=>$name]);
			$result = $this->getStdout();
			return $this->render('create',['result'=>$result]);
		}
		else{
			return $this->render('create',['result'=>false]);
		}
	}
	
	public function actionView($name){
		$model = $this->getMigrationByName($name);
		$result = $this->getStdout();
		return $this->render('view',['model'=>$model,'result'=>$result]);
	}
	
	public function actionApply($name){
		return $this->applyMigration($name, 'apply');
	}
	
	public function actionRevert($name){
		return $this->applyMigration($name, 'revert');
	}
	
	public function actionTo($name){
		return $this->applyMigration($name, 'to');
	}
	
	public function actionMark($name){
		return $this->applyMigration($name, 'mark');
	}
	
	public function actionMarkDown($name){
		return $this->applyMigration($name, 'mark-down');
	}
	
	public function actionRedo($name){
		return $this->applyMigration($name, 'redo');
	}
	
	function applyMigration($name, $type){
		$migration = $this->getMigration();
		$migration->runAction($type, ['migrationPath' => $this->module->migrationPath, 'interactive' => false], ['version'=>$name]);
		
		return $this->redirect(Url::to(['view','name'=>$name]));
	}
	
	function getMigrationFiles(){
		$migration_files = \yii\helpers\FileHelper::findFiles(Yii::getAlias($this->module->migrationPath),['only'=>['*.php']]);
		$migrations = array();
		
		foreach($migration_files as $file){
			$file = str_replace('\\','/',$file);
			$parts = explode('/',$file);
			$file_name = str_replace('.php','',$parts[count($parts)-1]);
			$migration = new Migration;
			$migration->version = $file_name;
			
			$migrations[$file_name] = $migration;
		}
		return $migrations;
	}
	
	function generateFileInfo($name){
		$name = ltrim($name, 'm');
		$parts = explode('_',$name);
		$year = substr($parts[0], 0, 2);
		$month = substr($parts[0], 2, 2);
		$day = substr($parts[0], 4, 2);
		$hour = substr($parts[1], 0, 2);
		$min = substr($parts[1], 2, 2);
		$sec = substr($parts[1], 4, 2);
		$date = '20'.$year.'-'.$month.'-'.$day.' '.$hour.':'.$min.':'.$sec;
		$date_int = strtotime($date);
		$date_formatted = date($this->module->dateFormat,$date_int);
		
		return ['created_on'=>$date_formatted, 'name'=>$parts[2]];
	}
	
	function getMigration(){
		$migration = new \barneyk\migration\controllers\MigrateController('migrate', Yii::$app);
		return $migration;
	}
	
	function getMigrationByName($name){
		if(!$model = Migration::find()->where(['version'=>$name])->one()){
			$model = new Migration;
			$model->version = $name;
		}
		$infos = $this->generateFileInfo($model->version);
		$model->name = $infos['name'];
		$model->created_on = $infos['created_on'];
		
		return $model;
	}
	
	function getOldMigrations(){
		$migrations = Migration::find()->all();
		return ArrayHelper::index($migrations,'version');
	}
	
	function getStdout(){
		$stdout = Yii::$app->session->get('migrate_stdout',false);
		Yii::$app->session->remove('migrate_stdout');
		return $stdout;
	}
		
	function isTableInitialized(){
		$tableName = '{{%'.$this->module->migrationTable.'}}';
		$tableSchema = Yii::$app->db->schema->getTableSchema($tableName);
		return !($tableSchema === null);
	}
}