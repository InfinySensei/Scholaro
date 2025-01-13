<?php

namespace App\Sae\Controleur;

use App\Sae\Exception\ArgNullException;
use App\Sae\Exception\DroitException;
use App\Sae\Lib\ConnexionUtilisateur;
use App\Sae\Lib\MessageFlash;
use App\Sae\Lib\Preferences;
use App\Sae\Modele\DataObject\Agregation;
use App\Sae\Modele\DataObject\Etudiant;
use App\Sae\Modele\Repository\AgregationRepository;
use App\Sae\Modele\Repository\EcoleRepository;
use App\Sae\Modele\Repository\EtudiantRepository;
use App\Sae\Modele\Repository\RessourceRepository;
use App\Sae\Service\ServiceAgregation;

class ControleurAgregation extends ControleurGenerique
{
    /**
     * @return void
     * affiche la liste des agregations
     */
    public static function afficherListe(): void
    {
        try {
            $login = ConnexionUtilisateur::getLoginUtilisateurConnecte();
            $agregations = (new ServiceAgregation())->recupererListe($login);
            ControleurGenerique::afficherVue("vueGenerale.php", ["titre" => "Liste des agregations", "cheminCorpsVue" => "agregation/liste.php", "agregations" => $agregations]);
        } catch (DroitException $e) {
            MessageFlash::ajouter("danger", $e->getMessage());
            self::redirectionVersUrl("controleurFrontal.php");
        }
    }

    /**
     * @return void
     * affiche la compostion d'une agrégation
     */
    public static function afficherDetail(): void
    {
        try {
            $details = (new ServiceAgregation())->detail($_REQUEST['id']);

            // Affichage de la vue
            self::afficherVue("vueGenerale.php", [
                "titre" => "Page Agrégation",
                "cheminCorpsVue" => "agregation/detail.php",
                "agregation" => $details['agregation'],
                "listeRessources" => $details['listeRessources'],
                "listeAgregations" => $details['listeAgregations'],
                "moyenne" => $details['moyenne']
            ]);
        } catch (DroitException $e) {
            MessageFlash::ajouter("danger", $e->getMessage());
            self::redirectionVersUrl("controleurFrontal.php?action=afficherListe&controleur=agregation");
        } catch (ArgNullException $e) {
            MessageFlash::ajouter("warning", $e->getMessage());
            self::redirectionVersUrl("controleurFrontal.php?action=afficherListe&controleur=agregation");
        }
    }

    /**
     * @return void
     * affiche la vue liste agregation et appelle la méthode supprimer
     * */
    public static function supprimer(): void
    {
        try {
            (new ServiceAgregation())->supprimer($_REQUEST['id']);
            MessageFlash::ajouter("success", "Agrégation supprimée");
            self::redirectionVersUrl("controleurFrontal.php?action=afficherListe&controleur=agregation");

        } catch (DroitException $e) {
            MessageFlash::ajouter("danger", $e->getMessage());
            self::redirectionVersUrl("controleurFrontal.php?action=afficherListe&controleur=agregation");
        } catch (ArgNullException $e) {
            MessageFlash::ajouter("warning", $e->getMessage());
            self::redirectionVersUrl("controleurFrontal.php?action=afficherListe&controleur=agregation");
        }
    }

    /**
     * @return void
     * affiche la vue formulaire de créer agrégation
     */
    public static function afficherFormulaire(): void
    {
        try {
            $info = (new ServiceAgregation())->preparerFormulaire();
            ControleurGenerique::afficherVue("vueGenerale.php", [
                'titre' => "Créer une agrégation",
                'cheminCorpsVue' => "agregation/agregationFormulaire.php",
                'agregations' => $info['agregations'],
                'ressources' => $info['ressources']
            ]);
        } catch (DroitException $e) {
            MessageFlash::ajouter("danger", $e->getMessage());
            self::redirectionVersUrl("controleurFrontal.php");
        }
    }


    /**
     * @return void
     * Permet d'enregistrer une agrégation dans la bd
     */
    public
    static function construireDepuisFormulaire(): void
    {
        try {
            $idAgregation = (new ServiceAgregation())->enregistrerNote();
            MessageFlash::ajouter("success", "Agrégation créée avec succès !");
            self::redirectionVersUrl('controleurFrontal.php?action=afficherDetail&controleur=agregation&id=' . $idAgregation);
        }
        catch (DroitException $e) {
            MessageFlash::ajouter("danger", $e->getMessage());
            self::redirectionVersUrl("controleurFrontal.php");
        }
        catch (ArgNullException $e){
            MessageFlash::ajouter("warning", $e->getMessage());
            self::redirectionVersUrl("controleurFrontal.php?action=afficherFormulaire&controleur=agregation");
        }
    }


}