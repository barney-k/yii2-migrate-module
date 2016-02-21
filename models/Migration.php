<?php
namespace barneyk\migration\models;

class Migration extends \yii\db\ActiveRecord
{
	public $name;
	public $created_on;
	
	public static function tableName(){
		$module = \Yii::$app->modules['migration'];
		return '{{%'.$module->migrationTable.'}}';
	}
	
	public function rules()
    {
        return [
            [['version','apply_time','name','created_on'], 'safe'],
        ];
    }
}
