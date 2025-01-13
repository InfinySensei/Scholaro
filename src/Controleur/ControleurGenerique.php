<?php

namespace App\Sae\Controleur;

use App\Sae\Configuration\ConfigurationLDAP;
use App\Sae\Configuration\ConfigurationSite;
use App\Sae\Lib\ChoixControleur;
use App\Sae\Lib\MotDePasse;
use App\Sae\Lib\Preferences;
use App\Sae\Lib\ConnexionUtilisateur;
use App\Sae\Lib\MessageFlash;
use App\Sae\Lib\VerificationEmail;
use App\Sae\Modele\HTTP\Cookie;
use App\Sae\Modele\Repository\AgregationRepository;
use App\Sae\Modele\Repository\EcoleRepository;
class ControleurGenerique
{

    /**
     * @param string $cheminVue Le chemin de la vue à utiliser
     * @param array $parametres des paramètres supplémentaire pour des informations spécifiques aux pages
     * @return void fonctions à appeler pour afficher une vue
     */
    protected static function afficherVue(string $cheminVue, array $parametres = []): void
    {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        $messagesFlash = MessageFlash::lireTousMessages();
        require __DIR__ . "/../vue/$cheminVue"; // Charge la vue
    }

    public static function afficherFormulaireConnexion(): void
    {
        if (ConnexionUtilisateur::estConnecte()) {
            return;
        }
        self::afficherVue("vueGenerale.php", ["titre" => "Connexion", "cheminCorpsVue" => "formulaireConnexion.php"]);
    }

    public static function enregistrerSemestre(): void
    {
        $semestres = [];
        for ($i = 1; $i < 6; $i++) {
            $numSemestre = 'numSemestre' . $i;
            if (isset($_REQUEST[$numSemestre])) {
                $semestres[] = $_REQUEST[$numSemestre];
            }
        }
        Preferences::enregistrer("choixSemestre", $semestres);
        MessageFlash::ajouter("success", "Changements appliqués");
        self::redirectionVersUrl("controleurFrontal.php?action=afficherFormulaire&controleur=agregation");
    }

    public static function enregistrerFiltres(): void
    {
        $filtres = [];
        foreach ((new AgregationRepository())->recuperer() as $agregation) {
            $id = $agregation->getIdAgregation();
            $idAgregation = 'idAgregations' . $id;
            if (isset($_REQUEST[$idAgregation])) {
                $filtres[] = $_REQUEST[$idAgregation];
            }
        }
        Preferences::enregistrer("choixFiltres", $filtres);
        MessageFlash::ajouter("success", "Filtres appliqués");
        self::redirectionVersUrl("controleurFrontal.php?action=afficherListe&controleur=etudiant");
    }

    /**
     * @throws \Exception
     */
    public static function connecter(): void
    {

        if (ConfigurationSite::getDebug()) {
            ConnexionUtilisateur::connecter("desertg");
            MessageFlash::ajouter("success", "Connexion réussie");
            if (ConnexionUtilisateur::estAdministrateur() || ConnexionUtilisateur::estProfesseur()) {
                self::redirectionVersUrl("controleurFrontal.php?action=afficherListe");
            } else {
                self::redirectionVersUrl("controleurFrontal.php?action=afficherEtudiantPage");
            }
            return;
        }

        if (!isset($_REQUEST['login']) || !isset($_REQUEST['mdp'])) {
            MessageFlash::ajouter("warning", "Login et/ou mot de passe manquant(s)");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (ConnexionUtilisateur::estEcolePartenaire($_REQUEST['login'])) {
            $ep = (new EcoleRepository())->recupererParClePrimaire($_REQUEST['login']);
            if ($ep && MotDePasse::verifier($_REQUEST['mdp'], $ep->getMdpHache())) {
                if($ep->isEstValide()) {
                    if ($ep->isMailValider()) {
                        ConnexionUtilisateur::connecter($ep->getSiret());
                        MessageFlash::ajouter("success", "Connexion réussie");
                        self::redirectionVersUrl("controleurFrontal.php");
                    } else {
                        MessageFlash::ajouter("warning", "Vous n'avez pas validé votre mail, regardez votre boite mail");
                        self::redirectionVersUrl("controleurFrontal.php");
                    }
                }else{
                    MessageFlash::ajouter("warning", "L'Admin n'a pas validé votre compte");
                    self::redirectionVersUrl("controleurFrontal.php");
                }
            } else {
                MessageFlash::ajouter("warning", "Le mot de passe ou l'identifiant est incorrecte");
                self::redirectionVersUrl("controleurFrontal.php");
            }

        }
        else if ($_SERVER["HTTP_HOST"] == "webinfo.iutmontp.univ-montp2.fr") {
            $ldapConnection = ConfigurationLDAP::connecterServeur();

            // Login / mot de passe ˋa tester
            $ldapLogin = $_REQUEST['login'];
            $ldapPassword = $_REQUEST['mdp'];

            // DN (distinguished name) de base ˋa l’IUT
            $ldapBaseDN = ConfigurationLDAP::getLdapBaseDN();

            // Filtre par uid (idenfiant unique)
            $ldapSearchFilter = "(uid=$ldapLogin)";
            $ldapSearch = ldap_search($ldapConnection, $ldapBaseDN, $ldapSearchFilter, array());

            // Recherche des utilisateurs avec cet identifiant
            $ldapUserResult = ldap_get_entries($ldapConnection, $ldapSearch);

            // Vérification que le login existe bien
            if ($ldapUserResult["count"] != 1) {
                MessageFlash::ajouter("warning", "Utilisateur inconnu");
                self::redirectionVersUrl("controleurFrontal.php");
                return;
            }

            // Récupération du DN complet de l’utilisateur
            $ldapUserDN = $ldapUserResult[0]["dn"];

            // Tentative de connexion avec login / mdp
            // Le @ sert à éviter l’écriture d’un message de Warning en cas d’identifiants incorrects
            $ldapBindSuccessful = @ldap_bind($ldapConnection, $ldapUserDN, $ldapPassword);
            if ($ldapBindSuccessful) {
                ConnexionUtilisateur::connecter($ldapLogin);
                MessageFlash::ajouter("success", "Connexion réussie");
                if (ConnexionUtilisateur::estAdministrateur()) {
                    self::redirectionVersUrl("controleurFrontal.php?action=afficherListe");
                } else {
                    self::redirectionVersUrl("controleurFrontal.php?action=afficherEtudiantPage");
                }
            } else {
                MessageFlash::ajouter("warning", "Identifiants incorrects");
                self::redirectionVersUrl("controleurFrontal.php");
            }

        } else {
            self::afficherErreur("Connexion LDAP impossible");
            MessageFlash::ajouter("warning", "Connexion LDAP impossible");
            self::redirectionVersUrl("controleurFrontal.php");
        }
    }

    public static function deconnecter(): void
    {
        // Fermeture de la connection au LDAP
        ConnexionUtilisateur::deconnecter();
        ConfigurationLDAP::deconnecterServeur();
        MessageFlash::ajouter("success", "Déconnexion réussie !");
        self::redirectionVersUrl("controleurFrontal.php");
    }

    public static function afficherErreur(string $erreur = ""): void
    {
        ControleurGenerique::afficherVue("vueGenerale.php", ["titre" => "Erreur", "cheminCorpsVue" => "erreur.php", "erreur" => $erreur]);
    }

    public static function redirectionVersUrl($url): void
    {
        header("Location: $url");
        exit();
    }

    public static function afficherCGU() {
        self::afficherVue("vueGenerale.php", ["titre" => "Condition Générales d'Utilisation", "cheminCorpsVue" => "cgu.php"]);
    }

    public static function accepterCGU() {
        Cookie::enregistrer("cgu", [true]);
        self::redirectionVersUrl("controleurFrontal.php");
    }
}