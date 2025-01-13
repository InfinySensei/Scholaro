<?php

namespace App\Sae\Controleur;


use App\Sae\Configuration\ConfigurationLDAP;
use App\Sae\Configuration\ConfigurationSite;
use App\Sae\Lib\ConnexionUtilisateur;
use App\Sae\Lib\MessageFlash;
use App\Sae\Lib\MotDePasse;
use App\Sae\Lib\Preferences;
use App\Sae\Modele\DataObject\Agregation;
use App\Sae\Modele\DataObject\Avis;
use App\Sae\Modele\DataObject\Formation;
use App\Sae\Modele\DataObject\Noter;
use App\Sae\Modele\Repository\AgregationRepository;
use App\Sae\Modele\Repository\AvisRepository;
use App\Sae\Modele\Repository\EcoleRepository;
use App\Sae\Modele\Repository\EtudiantRepository;
use App\Sae\Modele\DataObject\Etudiant;
use App\Sae\Modele\Repository\NoterRepository;
use App\Sae\Modele\Repository\FormationRepository;
use mysql_xdevapi\Exception;


class ControleurEtudiant extends ControleurGenerique
{

    /**
     * @return void afficher la liste des étudiants
     */
    public static function afficherListe(): void
    {
        $login = ConnexionUtilisateur::getLoginUtilisateurConnecte();
        if (!ConnexionUtilisateur::estAdministrateur() && !ConnexionUtilisateur::estEcolePartenaire($login) && !ConnexionUtilisateur::estProfesseur()) {
            MessageFlash::ajouter("danger", "Vous n'avez pas les droits administrateurs");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }

        if (ConnexionUtilisateur::estEcolePartenaire($login)){
            $agregations = (new AgregationRepository())->recupererParUtilisateur($login);
            $etudiants = (new EtudiantRepository())->recupererEtudiantFavoris($login);
        } else {
            $agregations = (new AgregationRepository())->recupererParUtilisateur("prof");
            $etudiants = (new EtudiantRepository())->recuperer();
        }
        ControleurGenerique::afficherVue("vueGenerale.php", ["titre" => "Listes test des étudiants", "cheminCorpsVue" => "etudiant/liste.php", "etudiants" => $etudiants, "agregations" => $agregations]);
    }
    public static function triDecroissant(): void
    {
        $login = ConnexionUtilisateur::getLoginUtilisateurConnecte();
        if (!ConnexionUtilisateur::estAdministrateur() && !ConnexionUtilisateur::estEcolePartenaire($login) && !ConnexionUtilisateur::estProfesseur()) {
            MessageFlash::ajouter("danger", "Vous n'avez pas les droits administrateurs");
            self::redirectionVersUrl("controleurFrontal.php?action=afficherEtudiantPage");
            return;
        }

        if (isset($_REQUEST['idAgregation'])) {
            if (ConnexionUtilisateur::estEcolePartenaire($login)){
                $agregations = (new AgregationRepository())->recupererParUtilisateur($login);
                $etudiants = (new EtudiantRepository())->triDecroissantNoteEtudiants($_REQUEST['idAgregation'], $login);
            } else {
                $agregations = (new AgregationRepository())->recupererParUtilisateur("prof");
                $etudiants = (new EtudiantRepository())->triDecroissantNoteEtudiants($_REQUEST['idAgregation'], "prof");
            }
            if ($etudiants != null) {
                ControleurGenerique::afficherVue("vueGenerale.php", ["titre" => "Liste des étudiants", "cheminCorpsVue" => "etudiant/liste.php", "etudiants" => $etudiants, "agregations" => $agregations]);
            } else {
                self::afficherListe();
            }
        } else {
            self::afficherListe();
        }
    }

    public static function triCroissant(): void
    {
        $login = ConnexionUtilisateur::getLoginUtilisateurConnecte();
        if (!ConnexionUtilisateur::estAdministrateur() && !ConnexionUtilisateur::estEcolePartenaire($login) && !ConnexionUtilisateur::estProfesseur()) {
            MessageFlash::ajouter("danger", "Vous n'avez pas les droits administrateurs");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (isset($_REQUEST['idAgregation'])) {
            if (ConnexionUtilisateur::estEcolePartenaire($login)){
                $agregations = (new AgregationRepository())->recupererParUtilisateur($login);
                $etudiants = (new EtudiantRepository())->triCroissantNoteEtudiants($_REQUEST['idAgregation'], $login);
            } else {
                $agregations = (new AgregationRepository())->recupererParUtilisateur("prof");
                $etudiants = (new EtudiantRepository())->triCroissantNoteEtudiants($_REQUEST['idAgregation'], "prof");
            }
            if ($etudiants != null) {
                ControleurGenerique::afficherVue("vueGenerale.php", ["titre" => "Liste des étudiants", "cheminCorpsVue" => "etudiant/liste.php", "etudiants" => $etudiants, "agregations" => $agregations]);
            } else {
                self::afficherListe();
            }
        } else {
            self::afficherListe();
        }
    }

    /**
     * @return void affiche les détails d'un étudiant
     * @throws \Exception
     */
    public static function afficherEtudiantPage(): void
    {
        $login = ConnexionUtilisateur::getLoginUtilisateurConnecte();
        if (!ConnexionUtilisateur::estConnecte() && !ConnexionUtilisateur::estEcolePartenaire($login) && !ConnexionUtilisateur::estProfesseur()) {
            MessageFlash::ajouter("warning", "Vous n'êtes pas connectés");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (!isset($_REQUEST['idEtudiant'])) {
            MessageFlash::ajouter("warning", "L'id de l'étudiant n'a pas été transmis");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        $etudiant = (new EtudiantRepository())->recupererParClePrimaire($_REQUEST['idEtudiant']);
        if (!$etudiant) {
            self::afficherErreur("Aucunes infos sur l'étudiant");
            return;
        }
        if (!ConfigurationSite::getDebug()) {
            ConfigurationLDAP::connecterServeur();
            if (!ConnexionUtilisateur::estUtilisateur(ConfigurationLDAP::getAvecUidNumber($_REQUEST['idEtudiant'])) && !ConnexionUtilisateur::estAdministrateur() && !ConnexionUtilisateur::estEcolePartenaire(ConnexionUtilisateur::getLoginUtilisateurConnecte())) {
                MessageFlash::ajouter("danger", "Les détails d'un étudiant ne peuvent être vu que par lui même et un administrateur.");
                self::redirectionVersUrl("controleurFrontal.php");
                return;
            }
        }
        $regarder = "";
        if (isset($_REQUEST["regarder"])){
            $regarder = $_REQUEST["regarder"];
        }
        $ecolechoisi = (new EcoleRepository())->recupererEcoleFavoris($_REQUEST["idEtudiant"]);
        $notes = (new EtudiantRepository())->getNotesEtudiant($_REQUEST['idEtudiant']);
        if (ConnexionUtilisateur::estEcolePartenaire($login)){
            $notesAgregees = (new EtudiantRepository())->recupererNotesAgregees($_REQUEST['idEtudiant'], $login);
        } else if (ConnexionUtilisateur::estAdministrateur() || ConnexionUtilisateur::estProfesseur()) {
            $notesAgregees = (new EtudiantRepository())->recupererNotesAgregees($_REQUEST['idEtudiant'], "prof");
        } else {
            $notesAgregees = array();
        }
//        $avis = (new EcoleRepository())->recupererAvis($_REQUEST["idEtudiant"]);
        $avis= [];
//        $avisRenseigner = (new AvisRepository())->recupererAvis($_REQUEST['idEtudiant']);
        $moyennes = array();
        foreach ($notesAgregees as $note) {
            $moyennes[] = (new AgregationRepository())->moyenne($note['idAgregation']);
        }
        ControleurGenerique::afficherVue("vueGenerale.php", ["titre" => "page Etudiant", "cheminCorpsVue" => "etudiant/etudiantPage.php", "etudiant" => $etudiant, "notes" => $notes, "notesAgregees" => $notesAgregees, "ecolesChoisie"=>$ecolechoisi, "regarder" => $regarder, "avis" => $avis, "moyennes" => $moyennes]);
    }

    public static function ajouterEcoleFavoris()
    {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter("warning", "Vous n'êtes pas connectés");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (isset($_REQUEST['idEcoles'])) {
            (new EtudiantRepository())->ajouterEcoleFav($_REQUEST["idEcoles"], $_REQUEST["idEtudiant"]);
        }
        self::afficherEtudiantPage();
    }

    public static function ajouterAvis()
    {
        if (!ConnexionUtilisateur::estConnecte()) {
            self::afficherErreur("Vous n'êtes pas connectés");
            return;
        }
        if (isset($_REQUEST['avisFormation'])) {
            (new EtudiantRepository())->ajouterAvisEcole($_REQUEST["avisFormation"], $_REQUEST["idEtudiant"]);
        }
        self::afficherEtudiantPage();
    }

    public static function afficherPdf() {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter("warning", "Vous n'êtes pas connectés");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        $etudiant = (new EtudiantRepository())->recupererParClePrimaire($_REQUEST['idEtudiant']);
        if (!$etudiant) {
            self::afficherErreur("Aucunes infos sur l'étudiant");
            return;
        }
        if (!ConfigurationSite::getDebug()) {
            ConfigurationLDAP::connecterServeur();
            if (!ConnexionUtilisateur::estUtilisateur(ConfigurationLDAP::getAvecUidNumber($_REQUEST['idEtudiant'])) && !ConnexionUtilisateur::estAdministrateur()) {
                MessageFlash::ajouter("danger", "La fiche de d'avis de poursuite d'étude d'un étudiant ne peut être vue que par lui même et un administrateur.");
                self::redirectionVersUrl("controleurFrontal.php");
                return;
            }
        }
        require __DIR__ . "/../vue/etudiant/pdf.php";
    }


    public static function ajouterDepuisCSV(): void
    {
        if (!ConnexionUtilisateur::estAdministrateur()) {
            MessageFlash::ajouter("danger", "Importation uniquement possible pour un administrateur");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        try {
            self::importerInfosEtudiant($_FILES);
            MessageFlash::ajouter("success", "Étudiants importés avec succès !");
            self::redirectionVersUrl("controleurFrontal.php?action=afficherListe");
        } catch (\Exception $e) {
            MessageFlash::ajouter("warning", "Erreur lors de l'importation des données");
            self::redirectionVersUrl("controleurFrontal.php?action=afficherListe");
        }
    }

    public static function importerInfosEtudiant(array $tableau): void
    {
        $filename = $tableau["file"]["tmp_name"];
        if ($tableau["file"]["size"] > 0) {
            $file = fopen($filename, "r");
            $header = fgetcsv($file, 10000, ',');

            if ($header[0] != "etudid") {
                throw new \Exception();
            }

            foreach ($header as $index => $colName) {
                if ($colName === 'Nom') {
                    $header[$index] = 'Nom1';
                    break;
                }
            }

            $etudiants = [];
            $notesRessources = [];
            while (($data = fgetcsv($file, 10000, ",")) !== FALSE) {
                $ligne = array_combine($header, $data);



                $etudid = $ligne["etudid"];
                $code_nip = $ligne["code_nip"];
                $civ = $ligne["Civ."];
                $nomEtu = $ligne["Nom1"];
                $prenomEtu = $ligne["Prénom"];
                $bac = $ligne["Bac"];
                $specialite = $ligne["Spécialité"];
                $rg_admis = $ligne["Rg. Adm."];
                $login = $nomEtu . $prenomEtu[0];

                if (!ctype_digit(substr($etudid, 0, 1))) {
                    break;
                }

                $etudiant = new Etudiant((int)$etudid, $code_nip, $civ, $nomEtu, $prenomEtu, $bac, $specialite, (int)$rg_admis, "", $login);
                $etudiants[] = $etudiant;

                foreach ($header as $index => $colName) {
                    if (str_starts_with($colName, "R")) {
                        if (strlen($colName) > 1) {
                            $semestre = $colName[1];
                            if (!ctype_digit($semestre)) {
                                continue;
                            }
                            if ($ligne[$colName] === "" || $ligne[$colName] === "~") {
                                continue;
                            }
                            $note = $ligne[$colName];

                            $noteRessource = new Noter($etudid, $semestre, $colName, $note);
                            $notesRessources[] = $noteRessource;

                            (new EtudiantRepository())->enregistrerRessource($colName);
                        }
                    }
                }
            }
            fclose($file);
            (new EtudiantRepository())->ajouterPlusieurs($etudiants);
            (new NoterRepository())->ajouterPlusieurs($notesRessources);
        }
    }

    public static function afficherPE() : void {
        if (!ConnexionUtilisateur::estAdministrateur()) {
            MessageFlash::ajouter("danger", "Vous n'avez pas les droits administrateurs");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        $avis = (new AvisRepository())->recuperer();
        $formations = (new FormationRepository())->recuperer();
        ControleurGenerique::afficherVue("vueGenerale.php", ["titre" => "Poursuite d'étude", "cheminCorpsVue" => "rpe/formulairePE.php", "avisListe" => $avis, "formations" => $formations]);
    }

    public static function enregistrerAvis() : void {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter("warning", "Vous n'êtes pas connectés");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (!ConnexionUtilisateur::estAdministrateur()) {
            MessageFlash::ajouter("warning", "Vous n'avez pas les droits.");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (!isset($_REQUEST['avis'])) {
            MessageFlash::ajouter("warning", "Aucun avis entrer en paramètre.");
            self::redirectionVersUrl("controleurFrontal.php?action=afficherPE&controleur=etudiant");
            return;
        }
        if ((new AvisRepository())->existeAvis($_REQUEST['avis'])) {
            MessageFlash::ajouter("warning", "Avis déjà existant.");
            self::redirectionVersUrl("controleurFrontal.php?action=afficherPE&controleur=etudiant");
            return;
        }
        $avis = new Avis($_REQUEST['avis']);
        (new AvisRepository())->ajouter($avis);
        MessageFlash::ajouter("success", "Avis prédéfini ajouté.");
        self::afficherPE();
    }

    public static function enregistrerFormation() : void {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter("warning", "Vous n'êtes pas connectés");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (!ConnexionUtilisateur::estAdministrateur()) {
            MessageFlash::ajouter("warning", "Vous n'avez pas les droits.");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (!isset($_REQUEST['nomFormation'])) {
            MessageFlash::ajouter("warning", "Aucune formation entrée en paramètre.");
            self::redirectionVersUrl("controleurFrontal.php?action=afficherPE&controleur=etudiant");
            return;
        }
        if ((new FormationRepository())->existeFormation($_REQUEST['nomFormation'])) {
            MessageFlash::ajouter("warning", "Formation déjà existante.");
            self::redirectionVersUrl("controleurFrontal.php?action=afficherPE&controleur=etudiant");
            return;
        }
        $formation = new Formation($_REQUEST['nomFormation']);
        (new FormationRepository())->ajouter($formation);
        MessageFlash::ajouter("success", "Formation prédéfinie ajoutée.");
        self::afficherPE();
    }

    public static function supprimerAvis(): void
    {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter("warning", "Vous n'êtes pas connectés");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (!ConnexionUtilisateur::estAdministrateur()) {
            MessageFlash::ajouter("warning", "Vous n'avez pas les droits.");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (!isset($_REQUEST['avis'])) {
            MessageFlash::ajouter("warning", "Aucun avis entré en paramètre.");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (!(new AvisRepository())->supprimer($_REQUEST['avis'])) {
            MessageFlash::ajouter("fail", "Aucun avis correspondant");
        }
        else {
            MessageFlash::ajouter("success", "Avis prédéfini supprimé");
        }
        self::afficherPE();
    }

    public static function supprimerFormation(): void
    {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter("warning", "Vous n'êtes pas connectés");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (!ConnexionUtilisateur::estAdministrateur()) {
            MessageFlash::ajouter("warning", "Vous n'avez pas les droits.");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (!isset($_REQUEST['nomFormation'])) {
            MessageFlash::ajouter("warning", "Aucune formation entrée en paramètre.");
            self::redirectionVersUrl("controleurFrontal.php");
            return;
        }
        if (!(new FormationRepository())->supprimer($_REQUEST['nomFormation'])) {
            MessageFlash::ajouter("fail", "Aucune formation correspondante");
        }
        else {
            MessageFlash::ajouter("success", "Formation prédéfinie supprimée");
        }
        self::afficherPE();
    }
}