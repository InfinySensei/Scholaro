<?php

namespace App\Sae\Controleur;


use App\Sae\Lib\ConnexionUtilisateur;
use App\Sae\Lib\MessageFlash;
use App\Sae\Lib\MotDePasse;
use App\Sae\Lib\Preferences;
use App\Sae\Lib\VerificationEmail;
use App\Sae\Modele\DataObject\Ecole;
use App\Sae\Modele\Repository\AgregationRepository;
use App\Sae\Modele\Repository\EcoleRepository;
use App\Sae\Modele\Repository\EtudiantRepository;

class ControleurEcolePartenaire extends ControleurGenerique
{
    public static function afficherFormulaireCreationCompte()
    {
        ControleurGenerique::afficherVue("vueGenerale.php", ["titre" => "Formulaire création de compte", "cheminCorpsVue" => "ecolePartenaire/formulaireCreationCompte.php"]);
    }

    public static function afficherListe()
    {
        $agregations = (new AgregationRepository())->recupererParUtilisateur(ConnexionUtilisateur::getLoginUtilisateurConnecte());
        $etudiants = (new EtudiantRepository())->recupererEtudiantFavoris(ConnexionUtilisateur::getLoginUtilisateurConnecte());
        ControleurGenerique::afficherVue("vueGenerale.php", ["titre" => "Liste des étudiants", "cheminCorpsVue" => "etudiant/liste.php", "etudiants" => $etudiants, "agregations" => $agregations]);
    }


    private static function construireDepuisFormulaire(array $tableauDonneesFormulaire): Ecole
    {
        return new Ecole(
            $tableauDonneesFormulaire['siret'],
            $tableauDonneesFormulaire['nomEcole'],
            $tableauDonneesFormulaire['villeEcole'],
            $tableauDonneesFormulaire['tel'],
            $tableauDonneesFormulaire['email'],
            false,
            false,
            MotDePasse::genererChaineAleatoire(32), // Génération aléatoire pour le nonce.
            MotDePasse::hacher($tableauDonneesFormulaire['mdp'])
        );
    }

    public static function creerDepuisFormulaire(): void
    {
        // Vérification que toutes les données nécessaires sont présentes
        if (isset($_REQUEST["siret"], $_REQUEST["nomEcole"], $_REQUEST["villeEcole"], $_REQUEST["tel"], $_REQUEST["email"], $_REQUEST["mdp"], $_REQUEST["mdp2"])) {
            // Validation du format de l'email
            $entreprise = (new EcoleRepository())->recupererParClePrimaire($_REQUEST["siret"]);
            if (!$entreprise) {
                if (filter_var($_REQUEST["email"], FILTER_VALIDATE_EMAIL)) {
                    // Vérification des mots de passe
                    if ($_REQUEST["mdp"] === $_REQUEST["mdp2"]) {
                        // Construction de l'objet Ecole

                        $ecole = new Ecole(
                            $_REQUEST['siret'],
                            $_REQUEST['nomEcole'],
                            $_REQUEST['villeEcole'],
                            $_REQUEST['tel'],
                            $_REQUEST['email'],
                            false,
                            false,
                            MotDePasse::genererChaineAleatoire(32), // Génération aléatoire pour le nonce.
                            MotDePasse::hacher($_REQUEST['mdp'])
                        );

                        // Ajout à la base de données
                        (new EcoleRepository())->ajouter($ecole);
                        VerificationEmail::envoiEmailValidation($ecole);
                        MessageFlash::ajouter("success", "Compte créé ! Veuillez valider votre email");

                        self::redirectionVersUrl("controleurFrontal.php?action=afficherFormulaireConnexion");
                    } else {
                        MessageFlash::ajouter("warning", "Les mots de passe ne correspondent pas");
                        self::redirectionVersUrl("controleurFrontal.php?controleur=ecolePartenaire&action=afficherFormulaireCreationCompte");
                    }
                } else {
                    MessageFlash::ajouter("warning", "Format d'email invalide");
                    self::redirectionVersUrl("controleurFrontal.php?controleur=ecolePartenaire&action=afficherFormulaireCreationCompte");
                }
            } else {
                MessageFlash::ajouter("warning", "Erreur l'entreprise existe déjà");
                self::redirectionVersUrl("controleurFrontal.php?controleur=ecolePartenaire&action=afficherFormulaireCreationCompte");
            }
        } else {
            MessageFlash::ajouter("warning", "Données manquantes");
            self::redirectionVersUrl("controleurFrontal.php?controleur=ecolePartenaire&action=afficherFormulaireCreationCompte");
        }
    }

    public static function validerEmail()
    {
        if (isset($_REQUEST['siret']) && isset($_REQUEST['nonce'])) {
            $booleen = VerificationEmail::traiterEmailValidation($_REQUEST['siret'], $_REQUEST['nonce']);
            if ($booleen === true) {
                $ecole = (new EcoleRepository())->recupererParClePrimaire($_REQUEST['siret']);
                VerificationEmail::validerParAdmin($ecole);
                MessageFlash::ajouter("success", "Mail validé ! En attente de vérification de l'administrateur.");
                ControleurGenerique::redirectionVersUrl("controleurFrontal.php");
            } else {
                MessageFlash::ajouter("warning", "Erreur de vérification de mail");
                ControleurGenerique::redirectionVersUrl("controleurFrontal.php");
            }
        } else {
            MessageFlash::ajouter("warning", "Erreur de login ou de nonce");
            ControleurGenerique::redirectionVersUrl("controleurFrontal.php");
        }
    }

    public static function validerEcole()
    {
        if (isset($_REQUEST['siret']) && isset($_REQUEST['nonce'])){
            $ecole = (new EcoleRepository())->recupererParClePrimaire($_REQUEST['siret']);

            if ($ecole && $ecole->getNonce() === $_REQUEST['nonce']) {
                $ecole->setEstValide(true);
                $ecole->setNonce("");
                (new EcoleRepository())->mettreAJour($ecole);
                VerificationEmail::notificationValidation($ecole);
                MessageFlash::ajouter("success", "Mail validé !");
                ControleurGenerique::redirectionVersUrl("controleurFrontal.php");
            }
        }
    }

}