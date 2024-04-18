<?php

require 'php/connect.php';

if (isset($_POST['free_pokemon'])) {
    $updatedTP = array(
        'trainerAccountID' => $_POST['trainerID'],
        $_POST['pokemonPos'] => null
    );
    $success = $db->update('tblTrainerPokemon', $updatedTP, "trainerAccountID='" . $_POST['trainerID'] . "'");
    if ($success) {
        header('Location: index.php');
        exit();
    }
}
?>