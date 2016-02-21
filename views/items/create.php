<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = Yii::t('migration', 'Migration');
?>

<?= $this->render('_menu'); ?>

<?php if(!$result): ?>

<?php $form = ActiveForm::begin(); ?>

	<div class="form-group">
		<label class="control-label" for="name"><?= Yii::t('migration','Name'); ?></label>
		<?= Html::input('text','name','',['class'=>'form-control','id'=>'name']); ?>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('migration', 'Create'), ['class' => 'btn btn-success']); ?>
	</div>

<?php ActiveForm::end(); ?>

<?php else: ?>
	<pre><?= $result ?></pre>
	<a href="<?= Url::to(['create']) ?>" class="btn btn-success"><?= Yii::t('migration','New') ?></a>
<?php endif; ?>