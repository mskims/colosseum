<?php
unset($_SESSION["member"]);
session_destroy();
move("/stock");
?>