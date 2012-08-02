<?php
class DbMigrationsSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $migrations = array(
		'version' => array('type' => 'integer', 'length' => 6, 'null' => false, 'default' => 0)
	);
}