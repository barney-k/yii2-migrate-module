<?php

/**
 * @package   yii2-migration-module
 * @author    Barney K <info@barney-k.com>
 * @version   1.0.0
 */
 
namespace barneyk\migration\models;

/**
 * @author Barney K <info@barney-k.com>
 */
 
class Migration extends \yii\db\ActiveRecord
{
	/** @var string */
	public $name;
	
	/** @var string */
	public $created_on;
	
	/** @inheritdoc */
	public static function tableName(){
		$module = \Yii::$app->modules['migration'];
		return '{{%'.$module->migrationTable.'}}';
	}
	
	/** @inheritdoc */
	public function rules()
    {
        return [
            [['version','apply_time','name','created_on'], 'safe'],
        ];
    }
}
