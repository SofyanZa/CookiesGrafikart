<?php

// Objectif définir un cookie pour l'utilisateur John pour 1 journée

var_dump($_COOKIE);
setcookie('utilisateur', 'John', time() + 60 * 60 * 24);
