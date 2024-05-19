<?php

require 'php/connect.php';

if (isset($_POST['decrement_pokedex'])) {
    if ($_SESSION['pokedexIndex'] > 0) {
        $_SESSION['pokedexIndex']--;
    }
    header("Location: ". $_POST['page'] ."#pokedex");
    exit();
}

?>