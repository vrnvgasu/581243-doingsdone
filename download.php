<?php
header('Content-Disposition: attachment; filename='.$_GET['fileName']);
readfile('uploads/'.$_GET['fileName']);
exit();
