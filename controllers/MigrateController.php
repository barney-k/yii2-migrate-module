<?php
namespace barneyk\migration\controllers;

use Yii;
use yii\console\controllers\MigrateController as OriginalController;
use yii\base\Controller as BaseController;


class MigrateController extends OriginalController
{
	const FG_WHITE  = 1;
	const FG_BLACK  = 30;
    const FG_RED    = 31;
    const FG_GREEN  = 32;
    const FG_YELLOW = 33;
    const FG_BLUE   = 34;
    const FG_PURPLE = 35;
    const FG_CYAN   = 36;
    const FG_GREY   = 37;
	
	
	public function runAction($id, $consoleParams = [], $params = [])
    {
        if (!empty($consoleParams)) {
            // populate options here so that they are available in beforeAction().
            $options = $this->options($id);
            foreach ($consoleParams as $name => $value) {
                if (in_array($name, $options, true)) {
                    $default = $this->$name;
                    $this->$name = is_array($default) ? preg_split('/\s*,\s*/', $value) : $value;
                    unset($consoleParams[$name]);
                } elseif (!is_int($name)) {
                    throw new Exception(Yii::t('yii', 'Unknown option: --{name}', ['name' => $name]));
                }
            }
        }
        return BaseController::runAction($id, $params);
    }
	
	public function actionApply($version){
        if ($this->confirm('Apply '. $version . 'migration?')){
			if (!$this->migrateUp($version)) {
				$this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", self::FG_RED);

				return self::EXIT_CODE_ERROR;
			}
            $this->stdout("\nMigrated up successfully.\n", self::FG_GREEN);
        }
    }
	
	public function actionRevert($version){
        if ($this->confirm('Revert '. $version . 'migration?')){
			if (!$this->migrateDown($version)) {
				$this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", self::FG_RED);

				return self::EXIT_CODE_ERROR;
			}
            $this->stdout("\nMigration reverted successfully.\n", self::FG_GREEN);
        }
    }
	
	public function actionMark($version){
        if ($this->confirm("Set migration history for $version?")){
			$this->addMigrationHistory($version);
            $this->stdout("The migration history is set for $version.\nNo actual migration was performed.\n", self::FG_GREEN);
        }
    }
	
	public function actionMarkDown($version){
        if ($this->confirm("Remove migration history for $version?")){
			$this->removeMigrationHistory($version);
            $this->stdout("The migration history is removed for $version.\nNo actual migration was performed.\n", self::FG_GREEN);
        }
    }
	
	public function actionRedo($version)
    {
         if ($this->confirm("Redo migration for $version?")) {
			if (!$this->migrateDown($version)) {
				$this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", self::FG_RED);

				return self::EXIT_CODE_ERROR;
			}

			if (!$this->migrateUp($version)) {
				$this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", self::FG_RED);

				return self::EXIT_CODE_ERROR;
			}

            $this->stdout("\nMigration redone successfully.\n", self::FG_GREEN);
        }
    }
	
	public function confirm($message, $default = false)
    {
		$this->stdout($message);
		$this->stdout(" YES\n",self::FG_WHITE);
		return parent::confirm($message, $default);
    }
	
	public function stdout($string,$color = 0)
    {
        $stdout = Yii::$app->session->get('migrate_stdout','');
		if($color){
			$stdout .= '<span style="color:'.$this->getColorNameById($color).'">';
		}
		$stdout .= $string;
		if($color){
			$stdout .= '</span>';
		}
		Yii::$app->session->set('migrate_stdout',$stdout);
		return true;
    }
	
	function getColorNameById($id){
		$array = [
			self::FG_WHITE => 'white',
			self::FG_BLACK => 'black',
			self::FG_RED => 'red',
			self::FG_GREEN => 'green',
			self::FG_YELLOW => 'yellow',
			self::FG_BLUE => 'blue',
			self::FG_PURPLE => 'purple',
			self::FG_CYAN => 'cyan',
			self::FG_GREY => 'grey',
		];
		return $array[$id];
	}
}