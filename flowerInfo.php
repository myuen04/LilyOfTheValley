<?php 
    require('database.php');
     $sql = "SELECT * FROM Flower LIMIT 1";
     $flowerInfo = $db->query($sql)
?>