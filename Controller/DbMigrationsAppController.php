<?php
App::uses('Controller', 'AppController');
Configure::load('DbMigrations.config', 'default', true);

class DbMigrationsAppController extends AppController {

//	public function beforeFilter() {
//		parent::beforeFilter();
//		$options = Configure::read('DbMigrations');
//		if (!empty($options)) {
//			if (!empty($options['sanityCheck']) && $options['sanityCheck'] === true) {
//				//
//			}
//		}
//	}
}