<?php

require 'php/connect.php';

if (isset($_POST)) {
    $data = array(
        "name" => $_POST["name"],
        "type1" => $_POST["type1"],
        "type2" => $_POST["type2"],
        "move1" => $_POST["move1"],
        "move2" => $_POST["move2"],
        "move3" => $_POST["move3"],
        "description" => $_POST["description"],
        "region" => $_POST["region"],
        "image" => $_POST["image"],
        "isStarter" => $_POST["isStarter"]
    );
    
    $success = $db->insert("tblPokedex", $data);

    if ($success) {
        header('Location: admin.php');
        exit();
    }
}
?>