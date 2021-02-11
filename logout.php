<?php

	header("Cache-Control: no-cache, must-revalidate");
	session_destroy();
	header('Location: index.php');
