<?php
App::uses('Controller', 'DbMigrations.DbMigrationsAppController');

class MigrationsController extends DbMigrationsAppController {

	public function index() {
		$files = $this->Migration->getFiles();
		$data = $this->Migration->find('first', array(
			'recursive' => -1,
			'contain' => false
		));
		$this->set(compact('files', 'data'));
	}

	public function conform() {
		if (!$this->request->is('post')) {
			$msg = __('Invalid request');
			$this->Session->setFlash($msg, 'default', array('class' => 'label label-success'));
			$this->redirect($this->referer());
			return;
		}

		$version = intval($this->request->data['Migration']['version']);
		if ($version === 0) {
			$this->Migration->reset();
			$msg = __("Db successfully reset to version ZERO");
		} else {
			$operation = $this->Migration->conform($version);
			$msg = __("Db successfully %s to version %s", $operation, $version);
		}

		$this->Session->setFlash($msg, 'default', array('class' => 'label label-success'));
		$this->redirect($this->referer());
		return;
	}
}