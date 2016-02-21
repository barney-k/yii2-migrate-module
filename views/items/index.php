<?php
use yii\helpers\Url;

$this->title = Yii::t('migration', 'Migration');
?>

<?= $this->render('_menu'); ?>

<?php if(count($migrations)): ?>

<table class="table">
	<tr><th><?= Yii::t('migration','Filename') ?></th><th><?= Yii::t('migration','Applied on') ?></th></tr>
	<?php foreach($migrations as $migration): ?>
		<tr>
			<td>
			<?php if(!$migration->apply_time): ?>
				<span class="label label-success"><?= Yii::t('migration','New') ?></span>
			<?php endif ?>
				<a href="<?= Url::to(['view','name'=>$migration->version]) ?>"><?= $migration->version ?></a>
			</td>
			<td>
				<?= ($migration->apply_time)?date($this->context->module->dateFormat,$migration->apply_time):'' ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>

<?php endif ?>