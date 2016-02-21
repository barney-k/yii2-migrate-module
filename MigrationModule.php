<?php

/**
 * @package   yii2-migration-module
 * @author    Barney K <info@barney-k.com>
 * @version   1.0.0
 */
 
namespace barneyk\migration;

use Yii;
use yii\filters\AccessControl;

/**
 * Migration module for managing migrations
 *
 * @author Barney K <info@barney-k.com>
 */
 
class MigrationModule extends \yii\base\Module
{
	/** @var array */
	public $admins = [];
	
	/** @var string */
	public $defaultRoute = 'items/index';
	
	/** @var string */
	public $migrationPath = '@vendor/barney-k/yii2-migration-module/migrations';
	
	/** @var string */
	public $dateFormat = 'Y.m.d. H:i:s';
	
	/** @var string */
	public $migrationTable = 'migration';
	
	/** @inheritdoc */
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
	
	/** @inheritdoc */
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
