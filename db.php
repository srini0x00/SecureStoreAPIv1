<?php
 $mysqli = new mysqli('localhost', 'root', 'toor', 'securestore');
  if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
?>