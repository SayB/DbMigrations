<?php
$config['DbMigrations'] = array(
	'table' => 'db_migrations', // without prefix if set in/app/Config/database.php
	'sanityCheck' => true,
	'engine' => 'InnoDB' // default, `MyISAM`
);