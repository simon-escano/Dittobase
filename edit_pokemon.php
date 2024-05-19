<?php

require 'php/connect.php';

if (isset($_POST)) {
    $success = null;
    $name = $_POST["name"];
    $type1 = $_POST["type1"];
    $type2 = isset($_POST["type2"]) ? $_POST["type2"] : "None";
    $description = $_POST["description"];
    $move1 = $_POST["move1"];
    $move2 = $_POST["move2"];
    $move3 = $_POST["move3"];

    if (isset($_POST['delete'])) {
        $success = $db->delete("tblPokedex", "pokedexID='$id'");
    } else {
        $id = $_POST["pokedexID"];
        $data = array(
            'name' => $name,
            'type1' => $type1,
            'type2' => $type2,
            'description' => $description,
            'move1' => $move1,
            'move2' => $move2,
            'move3' => $move3
        );
        
        $where = "pokedexID = $id";
        $success = $db->update('tblPokedex', $data, $where);
    }

    if ($success) {
        header('Location: admin.php#pokedex');
        exit();
    } else {
        echo 'Failed to update record';
    }
}
?>