<?php

require 'php/connect.php';

if (isset($_POST['increment_pokedex']) && isset($_POST['limit'])) {
    if ($_SESSION['pokedexIndex'] < $_POST['limit']) {
        $_SESSION['pokedexIndex']++;
    }
    header("Location: ". $_POST['page'] ."#pokedex");
    exit();
}

?>
