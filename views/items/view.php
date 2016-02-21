<?php
use yii\helpers\Url;

$this->title = Yii::t('migration', 'Migration') . ' - ' . $model->name;
?>

<?= $this->render('_menu'); ?>

<table class="table table-bordered">
	<tr><td><?= Yii::t('migration','Filename')?></td><td><?= $model->version ?></td></tr>
	<tr><td><?= Yii::t('migration','Name')?></td><td><?= $model->name ?></td></tr>
	<tr><td><?= Yii::t('migration','Created on')?></td><td><?= $model->created_on ?></td></tr>
	<?php if($model->apply_time): ?>
		<tr><td><?= Yii::t('migration','Applied on')?></td><td><?= date($this->context->module->dateFormat,$model->apply_time) ?></td></tr>
	<?php endif ?>
</table>

<?php if($result): ?>
<pre style="background-color: black;font-weight: bold;color: lightgrey;"><?= $result ?></pre>
<?php endif; ?>
<?php if(!$model->apply_time): ?>
	<a href="<?= Url::to(['apply','name'=>$model->version]) ?>" class="btn btn-success"><?= Yii::t('migration','Apply')?></a>
	<a href="<?= Url::to(['up','name'=>$model->version]) ?>" class="btn btn-success"><?= Yii::t('migration','Apply up to this')?></a>
	<a href="<?= Url::to(['mark','name'=>$model->version]) ?>" class="btn btn-primary"><?= Yii::t('migration','Mark as done')?></a>
<?php else: ?>
	<a href="<?= Url::to(['redo','name'=>$model->version]) ?>" class="btn btn-warning"><?= Yii::t('migration','Redo')?></a>
	<a href="<?= Url::to(['revert','name'=>$model->version]) ?>" class="btn btn-danger"><?= Yii::t('migration','Revert')?></a>
	<a href="<?= Url::to(['mark-down','name'=>$model->version]) ?>" class="btn btn-primary"><?= Yii::t('migration','Mark as undone')?></a>
<?php endif; ?>
