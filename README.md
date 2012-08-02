CakePHP: Versioned Db Migrations Plugin for CakePHP 2.0
============================================================

Installation
-------------------------------------------------------
Install this plugin following the simple steps below:

1. Copy this plugin in a directory called "DbMigrations" inside your app/Plugin directory.

2. Now load the plugin in your application's bootstrap file (typical location: app/Config/bootstrap.php)
by pasting this line:

	CakePlugin::load('DbMigrations');

3. After this, you may load the schema using CakePHP's shell, like:

	cake DbMigrations.create

	OR you can leave this step and let DbMigrations do the job for you automagically !

4. After following the above steps, all you need to do to start using DbMigrations is simply put these
lines in your AppController::beforeFilter() - or where ever you see it fit.
For example you can write a seperate controller for it so that you can upgrade / downgrade manually.

	ClassRegistry::init('DbMigrations.Migration')->upgrade(true);

And you're all set ! - Also, I would always advise to keep this kind of auto-upgrades for environments like Test and Development.
It won't be good practice at all to use this for production. Fits the dev environment best though.


How this works
-------------------------------------------------------
Now since the plugin is ready to use. When the code pasted in step 4 is executed, it first checks to see
if the schema necessary for DbMigrations is loaded (basically it creates a table called "migrations).
If it's not, then it creates it itself, if you pass boolean TRUE as a parameter to
DbMigrations.Migration::upgrade().

To further explain how exactly this works, let me tell you concept behind this first. I needed a dirty
quick solution to keep db changes seamlessly up to date. I ended up porting CodeIgniter styled migrations
somewhat, as in, that this plugin borrows the concept at whole from CI but the implementation is a bit more
free styled.

So to get started, after when you have followed the installation steps make a folder "Migrations" in your
"app" directory. You will find a "Migrations" folder already in the "DbMigrations" directory. That is the
default folder. You'd be better off keeping your files organized in a folder in your app dir for better
organization.

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


After making all the changes (following steps in the installation) and making these files, when you run your
application for the first time, it will run all these files, hence pushing all changes in your database.

If you notice you will that all "DbMigration_{$revisionNumber}" classes are instances of the CakePHP Model.
Hence you can use CakePHP's model layer to it's full potential !

If at any point in time, say you need to downgrade your changes to a specific revision number, assuming 2
for this explanation. All you need to do is execute this statement at any suitable place in your code:

	ClassRegistry::init('DbMigrations.Migration')->downgrade(2);

In this case, it will rollback all changes till revision 2. Which means it will drop the table "samples"
(you should code the drop statement in the downgrade() function of DbMigration_3 class).

If you wish to make your database up-to-date again, then just issuing the upgrade() statement, it will
re-create the samples table and push all changes for issue 336. It won't execute changes for files
001 and 002.

Important Notes
-------------------------------------------------------

There are no checks implemented at this point. No exceptions if you screw up. So you will really need
to be careful in coding the up() and down() functions for your DbMigration_X classes.

You can also even insert data into tables and issue update statements.

You can also extend this to use CakeSchema objects. But since I like my SQL raw, therefore I find it
much easier to just code raw SQL statements :)


Hope this plugin makes your life easier and makes development fast !
