<?php
App::uses('DbMigrationsAppModel', 'DbMigrations.Model');
App::uses('Folder', 'Utility');

class Migration extends DbMigrationsAppModel {

	public $path = null;
	public $useTable = 'migrations';
	public $setEngine = 'MyISAM';

	public function __construct() {
		parent::__construct();
		$options = Configure::read('DbMigrations');
		if (empty($options)) {
			return;
		}

		if (!empty($options['table'])) {
		
			App::import('Core', 'ConnectionManager');
			$dataSource = ConnectionManager::getDataSource('default');
			$prefix= $dataSource->config['prefix'];

			$this->useTable = $options['table'];
		}
		
		if (!empty($options['engine'])) {
			$this->setEngine = $options['engine'];
		}

		if (!empty($options['sanityCheck']) && $options['sanityCheck'] === true) {
			$sql = "CREATE TABLE IF NOT EXISTS `$prefix{$this->useTable}` (`version` int(6) NOT NULL DEFAULT '0') ENGINE={$this->setEngine} DEFAULT CHARSET=utf8;";
			ClassRegistry::init('DbMigrationsAppModel')->query($sql);
		}
	}

	public function conform($version = null) {
		if (is_null($version)) return;
		
		$m = $this->find('first');
		
		$currentVersion = $m['Migration']['version'];

		if ($version >= $currentVersion) {
			$this->upgrade(false, $version);
			return 'upgraded';
		}

		$this->downgrade($version);
		return 'downgraded';
	}

	public function getFiles() {
		return $this->_getFiles();
	}

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
		$sql = "CREATE TABLE IF NOT EXISTS `$prefix{$this->useTable}` (`version` int(6) NOT NULL DEFAULT '0') ENGINE={$this->setEngine} DEFAULT CHARSET=utf8;";
		$this->query($sql);
	}

	public function upgrade($sanityCheck = false, $toVersion = null) {

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
			if (is_null($toVersion)) {
				if ($_version > $version) {
					$toProcess[$_version] = $name;
				}
			} else {
				if ($_version > $version && $_version <= $toVersion) {
					$toProcess[$_version] = $name;
				}
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

		$this->_setMigrationVersion($lastVersionProcessed);
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

		$this->_setMigrationVersion($version);
	}

	public function reset() {
		$files = $this->_getFiles();
		$toProcess = array();
		foreach ($files as $f) {
			$parts = explode('_', $f);
			$version = intval($parts[0]);
			$toProcess[$version] = $f;
		}
		krsort($toProcess);
		foreach ($toProcess as $k => $v) {
			$this->_process($k, $v, 'down');
		}

		$this->_setMigrationVersion(0);
	}

	protected function _setMigrationVersion($version) {
		//$this->query("TRUNCATE TABLE {$this->useTable}");
		if ($version === 0) return;
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