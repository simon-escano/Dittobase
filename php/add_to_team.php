<?php

require 'php/connect.php';

if (isset($_POST['add_to_team'])) {
    $updatedData = array(
        'chosenPokemon1' => $_POST['pokemonID']
    );
    if ($db->update('tblPokemonTeam', $updatedData, "trainerAccountID='" . $_POST['trainerID']. "'")) {
        header('Location: index.php');
        exit();
    }
}
?>
