<?php

/**
 * @package   yii2-migration-module
 * @author    Barney K <info@barney-k.com>
 * @version   1.0.0
 */
 
namespace barneyk\migration\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use barneyk\migration\models\Migration;

/**
 * @author Barney K <info@barney-k.com>
 */

class ItemsController extends \yii\web\Controller
{
	/** @inheritdoc */
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
	
	/**
     * Initilizes the migration database.
	 * @return yii\web\Response
     */
	public function actionInit(){
		$tableName = $this->module->migrationTable;
		$tableNameDb = '{{%'.$this->module->migrationTable.'}}';
		Yii::$app->db->createCommand()->createTable($tableNameDb, [
			'version' => 'varchar(180) NOT NULL PRIMARY KEY',
			'apply_time' => 'integer',
		])->execute();
			
		return $this->redirect(Url::to(['index']));
	}
	
	/**
     * Lists all created items.
     * @return string
     */
    public function actionIndex(){
		$migrations = $this->getMigrationFiles();
		$olds = $this->getOldMigrations();
		$all_migrations = array_merge($migrations,$olds);
		krsort($all_migrations);
		return $this->render('index',['migrations'=>$all_migrations,'olds'=>$olds]);
    }
	
	/**
     * Creates a new migration item.
     * @return string
     */
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
	
	/**
     * Displays migration data.
     * @return string
     */
	public function actionView($name){
		$model = $this->getMigrationByName($name);
		$result = $this->getStdout();
		return $this->render('view',['model'=>$model,'result'=>$result]);
	}
	
	/**
     * Applies the given migration.
     */
	public function actionApply($name){
		return $this->applyMigration($name, 'apply');
	}
	
	/**
     * Reverts the given migration.
     */
	public function actionRevert($name){
		return $this->applyMigration($name, 'revert');
	}
	
	/**
     * Applies all migrations up to the given migration.
     */
	public function actionTo($name){
		return $this->applyMigration($name, 'to');
	}
	
	/**
     * Marks the given migration as done.
     */
	public function actionMark($name){
		return $this->applyMigration($name, 'mark');
	}
	
	/**
     * Marks the given migration as undone.
     */
	public function actionMarkDown($name){
		return $this->applyMigration($name, 'mark-down');
	}
	
	/**
     * Redos the given migration.
     */
	public function actionRedo($name){
		return $this->applyMigration($name, 'redo');
	}
	
	/**
     * Applies the migration command.
     * @return yii\web\Response
     */
	function applyMigration($name, $type){
		$migration = $this->getMigration();
		$migration->runAction($type, ['migrationPath' => $this->module->migrationPath, 'interactive' => false], ['version'=>$name]);
		
		return $this->redirect(Url::to(['view','name'=>$name]));
	}
	
	/**
     * Reads the migration directory for migration files.
     * @return array
     */
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
	
	/**
     * Converts a filename to infomations about the file.
     * @return array
     */
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
	
	/**
     * Gets a MigrateController for processing.
     * @return MigrateController
     */
	function getMigration(){
		$migration = new \barneyk\migration\controllers\MigrateController('migrate', Yii::$app);
		return $migration;
	}
	
	/**
     * Gets a migration model from database. If that's not possible creates a new one from the give filename.
     * @return Migration
     */
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
	
	/**
     * Gets the migrated migrations.
     * @return array
     */
	function getOldMigrations(){
		$migrations = Migration::find()->all();
		return ArrayHelper::index($migrations,'version');
	}
	
	/**
     * Gets messages generated by the migration processes.
     * @return string
     */
	function getStdout(){
		$stdout = Yii::$app->session->get('migrate_stdout',false);
		Yii::$app->session->remove('migrate_stdout');
		return $stdout;
	}
	
	/**
     * Checks if the migration table was initialized.
     * @return boolean
     */
	function isTableInitialized(){
		$tableName = '{{%'.$this->module->migrationTable.'}}';
		$tableSchema = Yii::$app->db->schema->getTableSchema($tableName);
		return !($tableSchema === null);
	}
}