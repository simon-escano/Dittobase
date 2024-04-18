<?php

require 'php/connect.php';

if (isset($_POST['free_pokemon'])) {
    $updatedData = array(
        $_POST['pokemonPos'] => null
    );
    if ($db->update('tblTrainerPokemon', $updatedData, "trainerAccountID='" . $_POST['trainerID'] . "'")) {
        header('Location: index.php');
        exit();
    }
}
?>