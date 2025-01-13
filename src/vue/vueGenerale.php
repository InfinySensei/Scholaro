<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php
        /**
         * @var string $titre
         */
        echo $titre;
        /**
         * @var string $cheminCorpsVue
         */
        ?></title>
    <link rel="stylesheet" href="../ressources/css/vueGeneraleStyle.css">
    <link href="../ressources/css/<?= str_replace(".php", "Style.css", $cheminCorpsVue)?>" rel="stylesheet">
</head>
<body>
<header>

    <nav class="navbar">
        <img class="logo" src="../ressources/images/logo_IUT.png" alt="logo">
        <div class="nav_links">
            <ul>
                <?php

                    use App\Sae\Lib\ConnexionUtilisateur;
                use App\Sae\Modele\HTTP\Cookie;
                use App\Sae\Modele\Repository\EtudiantRepository;

                if (ConnexionUtilisateur::estAdministrateur() || (ConnexionUtilisateur::estConnecte() && ConnexionUtilisateur::estEcolePartenaire(ConnexionUtilisateur::getLoginUtilisateurConnecte())) || ConnexionUtilisateur::estProfesseur()) {
                    echo "<li>
                    <a href='controleurFrontal.php?action=afficherListe'>LISTE ETUDIANTS</a>
                </li>
                <li>
                    <a href='controleurFrontal.php?action=afficherFormulaire&controleur=agregation'>CREER AGREGATION</a>
                </li>
                <li>
                    <a href='controleurFrontal.php?action=afficherListe&controleur=agregation'>LISTE AGREGATIONS</a>
                </li>";
                    if (ConnexionUtilisateur::estAdministrateur()) {
                        echo "<li>
                                <a href='controleurFrontal.php?action=afficherPE&controleur=etudiant'>POURSUITE D'ETUDE</a>
                              </li>";
                    }
                }
                if (ConnexionUtilisateur::estEtudiant()) {
                    $login = rawurlencode(ConnexionUtilisateur::getLoginUtilisateurConnecte());
                    echo "<li>
                            <a href=\"controleurFrontal.php?action=afficherEtudiantPage&idEtudiant=$login&controleur=etudiant\">PROFIL</a>
                          </li>";
                }
                if (!ConnexionUtilisateur::estConnecte()) {
                    echo "<li>
                    <a href='controleurFrontal.php?action=afficherFormulaireConnexion'>SE CONNECTER</a>
                </li>";
                } else {
                    echo "<li>
                    <a href='controleurFrontal.php?action=deconnecter'><img class='logout' src='../ressources/images/logout.png' alt='se déconnecter'></a>
                </li>";
                }?>
            </ul>
        </div>
    </nav>

    <div>
        <?php
        /** @var string[][] $messagesFlash */
        foreach($messagesFlash as $type => $messagesFlashPourUnType) {
            // $type est l'une des valeurs suivantes : "success", "info", "warning", "danger"
            // $messagesFlashPourUnType est la liste des messages flash d'un type
            foreach ($messagesFlashPourUnType as $messageFlash) {
                echo <<< HTML
            <div class="alert alert-$type">
               $messageFlash
            </div>
            HTML;
            }
        }
        ?>
    </div>

</header>
<main>
    <?php


    require __DIR__ . "/{$cheminCorpsVue}";

    // Vérifie si l'utilisateur a accepté les CGU via un cookie
    $cgu_accepted = isset($_COOKIE['cgu']) && Cookie::lire('cgu') == true;

    if (!$cgu_accepted && !($cheminCorpsVue == "cgu.php")) {
        echo "<div id='cgu-overlay' class='cgu-overlay'>
        <div class='cgu-popup'>
            <h2>Conditions Générales d'Utilisation</h2>
            <p>Bienvenue sur notre site ! Avant de continuer, veuillez lire et accepter nos
                <a href='controleurFrontal.php?action=afficherCGU' target='_blank'>Conditions Générales d'Utilisation</a>.
            </p>
            <form action='controleurFrontal.php' method='post'>
            <input type='hidden' name='action' value='accepterCGU'>
            <button type='submit' id='accept-cgu'>Accepter</button>
            </form>    
        </div>
    </div>";
    }
    ?>
</main>
<footer>
    <div class="footer-container">
        <p class="co"> Copyright © 2024 All rights reserved (<a href='controleurFrontal.php?action=afficherCGU' target='_blank'>Conditions Générales d'Utilisation</a>)</p>
    </div>
</footer>
</body>
</html>

