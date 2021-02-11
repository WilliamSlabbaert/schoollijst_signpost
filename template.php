<!-- HTML Template -->
<!DOCTYPE html>
<html>

<!-- Load in HEAD, CSS, Includes, Roles, PHP Fuctions, Open BODY Tag -->
<?php include('head.php'); ?>

<!-- Load in NAV -->
<?php include('nav.php'); ?>

<div class="body container">
	<!-- PAGE CONTENT HERE determined by $this_page value -->
	<!-- 'content_home.php', 'content_about.php'... have the content-->
	<?php include ("$this_page"); ?>
</div>

<!-- Footer, Table js, Close BODY Tag -->
<?php include "footer.php" ?>

</html>
