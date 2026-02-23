<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Déconnexion de l'administrateur.

session_start();
session_destroy();

header('Location: index.php');
exit;
