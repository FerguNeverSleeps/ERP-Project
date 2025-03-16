<?php
if (isset($_SESSION['usuario_rhexpress'])) {
	header("location:rhexpress_menu.php");
}else{
	header("location:rhexpress_login.php");
}
?>