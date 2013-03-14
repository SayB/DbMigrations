<?php
$fileOptions = array();
$currentVersion = null;
if (!empty($data)) {
	$currentVersion = $data['Migration']['version'];
}
foreach ($files as $f) {
	$parts = explode('_', $f);
	$version = intval($parts[0]);
	$fileOptions[$version] = $f;
}
if (!is_null($currentVersion) && $currentVersion > 0) {
	$fileOptions[0] = 'Completely Reset !';
	ksort($fileOptions);
}

?>

<h3>Your database is currently at version <?php echo $currentVersion; ?></h3>

<?php echo $this->Form->create('DbMigrations.Migration', array('action' => 'conform')); ?>

<fieldset>
	<legend>Upgrade or Downgrade DB To Selected Version</legend>

	<?php echo $this->Form->input('Migration.version', array(
		'type' => 'select',
		'options' => $fileOptions,
		'label' => false,
		'value' => $currentVersion,
		'style' => 'width:100%;'
	)); ?>

	<?php echo $this->Form->submit('Update', array('class' => 'btn')); ?>

</fieldset>

<?php echo $this->Form->end(); ?>