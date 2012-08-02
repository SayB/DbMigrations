<?php
App::uses('Folder', 'Utility');
class Migration extends Model {

	public $path = null;

	protected function _getFiles() {
		$defaultMigrations = dirname(dirname(__FILE__)) . DS . 'Migrations';
		$appMigrations = APP . 'Migrations';
		$this->path = $appMigrations;

		if (!is_dir($appMigrations)) {
			$this->path = $defaultMigrations;
		}

		$Folder = new Folder($this->path);
		$files = $Folder->find();

		return $files;
	}

	protected function _sanityCheck() {
		$sql = "CREATE TABLE IF NOT EXISTS `migrations` (`version` int(6) NOT NULL DEFAULT '0') ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->query($sql);
	}

	public function upgrade($sanityCheck = false) {

		if ($sanityCheck) {
			$this->_sanityCheck();
		}

		$files = $this->_getFiles();
		if (empty($files)) {
			return;
		}

		$m = $this->find('first');
		$version = $m['Migration']['version'];

		$toProcess = array();
		foreach ($files as $file) {
			$parts = explode('_', $file);
			$_version = intval($parts[0]);
			$name = $file;
			if ($_version > $version) {
				$toProcess[$_version] = $name;
			}
		}

		if (empty($toProcess)) {
			return;
		}

		ksort($toProcess);
		$lastVersionProcessed = null;
		foreach ($toProcess as $k => $v) {
			$this->_process($k, $v);
			$lastVersionProcessed = $k;
		}

		$this->query("TRUNCATE TABLE migrations");
		$this->set(array('Migration' => array('version' => $lastVersionProcessed)));
		$this->save();
	}

	public function downgrade($version = 0) {
		$files = $this->_getFiles();
		if (empty($files)) {
			return;
		}

		$m = $this->find('first');
		$latestVersion = $m['Migration']['version'];

		$toProcess = array();
		foreach ($files as $file) {
			$parts = explode('_', $file);
			$_version = intval($parts[0]);
			$name = implode('_', $parts);
			if ($_version <= $latestVersion && $_version > $version) {
				$toProcess[$_version] = $name;
			}
		}

		krsort($toProcess);
		foreach ($toProcess as $k => $v) {
			$this->_process($k, $v, 'down');
		}

		$this->query("TRUNCATE TABLE migrations");
		$this->set(array('Migration' => array('version' => $version)));
		$this->save();
	}

	protected function _process($version, $name, $type = 'up') {
		$file = $this->path . DS . $name;
		require_once $file;

		$className = 'DbMigration_' . $version;
		$DbMigration = new $className();
		$DbMigration->{$type}();
	}
}
?>