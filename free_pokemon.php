<?php

require 'php/connect.php';

if (isset($_POST['free_pokemon'])) {
    $pokemonTeam = $db->select("tblTrainerPokemon", "*", "trainerAccountID='$currentUser'")[0];
    for ($i = 1; $i <= 10; $i++) {
        $chosenPokemon = $pokemonTeam['pokemon' . $i];
        if ($chosenPokemon == $_POST['spawnID']) {
            break;
        }
    }
    $updatedTP = array(
        'trainerAccountID' => $currentUser,
        'pokemon' . $i => null
    );
    $success = $db->update('tblTrainerPokemon', $updatedTP, "trainerAccountID='$currentUser'");
    if ($success) {
        header('Location: index.php');
        exit();
    }
}
?>