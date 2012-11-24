CakePHP: Versioned Db Migrations Plugin for CakePHP 2.0
============================================================

Installation
-------------------------------------------------------
Install this plugin following the simple steps below:

1. Copy this plugin in a directory called "DbMigrations" inside your app/Plugin directory.

2. Now load the plugin in your application's bootstrap file (typical location: app/Config/bootstrap.php)
by pasting this line:

	CakePlugin::load('DbMigrations');

3. After this, take a look at DbMigrations/Config/config.php. Here you will find two setting:

	table => 'db_migrations'
	and sanityCheck => true
	
	These settings are as simple as they sound. The "table" is the name of the table the DbMigrations plugin is going to use.
	You can change it to your liking.
	The second setting: "sanityCheck", when turned to "TRUE" lets DbMigrations check if the table specified in the "table"
	property exists. If not, it simply creates it so that you won't have to do a thing :)

	
4. After following the above steps, there are two ways in which you can keep your databases up-to-date.

	4.1. You can simply paste this line "ClassRegistry::init('DbMigrations.Migration')->upgrade(true)" in your AppController::beforeFilter().
		 Following this way, whenever your you hit a page, the AppController::beforeFilter() will be executed, hence, the DbMigrations plugin
		 will update your database to the latest version if it's not already.
	
	4.2. OR you can go to the plugin's GUI, http://<your_website_url>/db_migrations/migrations and choose the revision you'd like
		 your database to be updgraded or downgraded to.

NOTE: I won't recommend using both 4.1 or 4.2 at the same time.

And you're all set ! - Also, I would always advise to keep this kind of auto-upgrades for environments like Test and Development.
It won't be good practice at all to use this for production. Fits the dev environment best though.


How this works
-------------------------------------------------------

So to get started, after when you have followed the installation steps make a folder "Migrations" in your
"app" directory. You will find a "Migrations" folder already in the "DbMigrations" directory. That is the
default folder. You'd be better off keeping your files organized in a folder in your app dir for better
organization. (In future versions I can have that folder name and path configurable too).

A sample file list in the "Migrations" folder would look like this:

	001_initial_db_dump.php
	002_users_table_add_deleted_field.php
	003_create_samples_table.php
	004_changes_for_issue_336.php

There are two parts to the files you create. As you can see, the name of the files that is prefixed by a
version number. These version numbers need to be unique as it's going to be an incremental updates for
the database.

Now to look inside a sample file. For, say, the second file, "002_users_table_add_deleted_field.php", the
contents would be simply:


	class DbMigration_2 extends DbMigrationsAppModel {

		public function up() {
			$sql = "ALTER TABLE  `users` ADD  `deleted` TINYINT( 1 ) NOT NULL";
			$this->query($sql);
		}

		public function down() {
			$sql = "ALTER TABLE `users` DROP `deleted`";
			$this->query($sql);
		}
	}


After making all the changes (following steps in the installation) and making these files, if you are following step 4.1
to update your database, then when you run your application for the first time, it will run all these files, hence pushing all changes in your database.

If you notice, you'll see that all "DbMigration_{$revisionNumber}" classes are instances of the CakePHP Model.
Hence you can use CakePHP's model layer to it's full potential !

If at any point in time, say you need to downgrade your changes to a specific revision number, assuming 2
for this explanation. All you need to do is execute this statement at any suitable place in your code:

	ClassRegistry::init('DbMigrations.Migration')->downgrade(2);

This will rollback all changes till revision 2. Which means it will drop the table "samples"
(you should code the drop statement in the downgrade() function of DbMigration_3 class).

If you wish to make your database up-to-date again, then just issuing the upgrade() statement, it will
re-create the samples table and push all changes for issue 336. It won't execute changes for files
001 and 002.

OR alternatively, you can follow step 4.2.

Important Notes
-------------------------------------------------------

You will need to be carefuly to name your DbMigration_X classes, that "X" should correspond to the revision number that
is prepended to every migration file.

There are no checks implemented at this point. No exceptions if you screw up. So you will really need
to be careful in coding the up() and down() functions for your DbMigration_X classes.

You can also even insert data into tables and issue update statements.


Hope this plugin makes your life easier and makes development fast !
