<?php
function MsgBox($str)
{
$language = "language=\"javascript\"";
echo "<script $language>\n";
echo " alert('$str');\n";
echo "</script>\n";
}


?>
