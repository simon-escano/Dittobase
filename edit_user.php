<?php

require 'php/connect.php';

if (isset($_POST)) {
    $success = null;
    $acctID = $_POST["acctID"];
    $username = $_POST["username"];
    $userID = $_POST["userID"];
    $trainerID = $_POST["trainerID"];

    switch ($_POST["query"]) {
        case "update":
            $data = array(
                "acctID" => $acctID,
                "username" => $username
            );
            $success = $db->update("tblUserAccount", $data, "acctID='$acctID'");
            if ($success) {
                $data = array(
                    "username" => $username
                );
                $success = $db->update("tblUser", $data, "userID='$userID'");
            }
            break;
        case "delete":
            $success = $db->delete("tblPokemonTeam", "trainerAccountID='$trainerID'");
            if ($success) {
                $success = $db->delete("tblTrainerPokemon", "trainerAccountID='$trainerID'");
                if ($success) {
                    $success = $db->delete("tblUserAccount", "acctID='$acctID'");
                    if ($success) {
                        $success = $db->delete("tblUser", "userID='$userID'");
                        if ($success) {
                            $success = $db->delete("tblTrainerAccount", "trainerAccountID='$trainerID'");
                        }
                    }
                }
            }
            break;
    }

    if ($success) {
        header('Location: admin.php');
        exit();
    } else {
        echo 'Failed to update record';
    }
}
?>