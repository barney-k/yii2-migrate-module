<?php
namespace barneyk\migration;

use Yii;
use yii\filters\AccessControl;

class MigrationModule extends \yii\base\Module
{
	public $admins = [];
	public $defaultRoute = 'items/index';
	public $migrationPath = '@vendor/barney-k/yii2-migration-module/migrations';
	public $dateFormat = 'Y.m.d. H:i:s';
	public $migrationTable = 'migration';
	
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return in_array(Yii::$app->user->identity->username, $this->admins);
                        },
                    ]
                ],
            ],
        ];
    }
	
	public function init(){
		parent::init();
		
		if (!isset(Yii::$app->get('i18n')->translations['migration*'])) {
            Yii::$app->get('i18n')->translations['migration*'] = [
                'class'    => 'yii\i18n\PhpMessageSource',
                'basePath' => __DIR__ . '/messages',
            ];
        }
	}
}