<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>DbMigrations For CakePHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="/db_migrations/css/bootstrap.min.css" rel="stylesheet">
    <script src="/db_migrations/js/jquery-1.7.2.min.js"></script>
    <script src="/db_migrations/js/bootstrap.min.js"></script>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

  </head>

  <body>

    <div class="container">

		<div class="page-header">
			<h1>DbMigrations For CakePHP</h1>
		</div>

		<?php echo $this->Session->flash(); ?>

		<?php echo $this->fetch('content'); ?>

    </div> <!-- /container -->

  </body>
</html>