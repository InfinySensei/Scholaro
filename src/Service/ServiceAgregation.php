<?php

namespace App\Sae\Service;

use App\Sae\Exception\ArgNullException;
use App\Sae\Exception\DroitException;
use App\Sae\Lib\ConnexionUtilisateur;
use App\Sae\Lib\MessageFlash;
use App\Sae\Modele\DataObject\Agregation;
use App\Sae\Modele\Repository\AgregationRepository;
use App\Sae\Modele\Repository\EtudiantRepository;
use App\Sae\Modele\Repository\RessourceRepository;
use mysql_xdevapi\Exception;

class ServiceAgregation
{
    /**
     * @param $login
     * @return array retourne la liste des agrégations demandées sinon renvoie une Exception si il y a un problème
     * @throws DroitException
     */
    public function recupererListe($login): array
    {
        (new ServiceGenerique())->verifDroit($login);
        if (ConnexionUtilisateur::estEcolePartenaire($login)) {
            $agregations = (new AgregationRepository())->recupererParUtilisateur($login);
        } else {
            $agregations = (new AgregationRepository())->recupererParUtilisateur("prof");
        }
        foreach ($agregations as $agregation) {
            $listeRessources = (new AgregationRepository())->listeRessourcesAgregees($agregation->getIdAgregation());
            $listeAgregation = (new AgregationRepository())->listeAgregationsAgregees($agregation->getIdAgregation());
            if (empty($listeAgregation) && empty($listeRessources)) {
                (new AgregationRepository())->supprimer($agregation->getIdAgregation());
            }
        }
        return $agregations;
    }

    /**
     * @param $idAgregation
     * @return array retourne l'agregation et les listes en rapport et ainsi que sa moyenne
     * @throws ArgNullException
     * @throws DroitException
     */
    public function detail($idAgregation): array
    {
        (new ServiceGenerique())->verifDroit(ConnexionUtilisateur::getLoginUtilisateurConnecte());
        (new ServiceGenerique())->idVide($idAgregation);

        $repository = new AgregationRepository();
        $ressourceRepo = new RessourceRepository();

        // Récupération de l'agrégation principale
        $agregation = $repository->recupererParClePrimaire($idAgregation);
        if (!$agregation) {
            throw new ArgNullException("L'id n'est pas celui d'une agrégation");
        }
        if ($agregation->getSiretCreateur() != ConnexionUtilisateur::getLoginUtilisateurConnecte() && ((ConnexionUtilisateur::estAdministrateur() || ConnexionUtilisateur::estProfesseur()) && $agregation->getLoginCreateur() != "prof")) {
            throw new DroitException("Vous n'avez pas créée cette agrégation");
        }
        // Récupération des listes associées
        $listeRessources = $repository->listeRessourcesAgregees($idAgregation);
        $listeAgregations = $repository->listeAgregationsAgregees($idAgregation);

        // Calcul de la moyenne
        $moyenne = 0;
        $coefTotal = 0;

        foreach ($listeRessources as $ressource) {
            $moyenne += $ressourceRepo->moyenne($ressource[0]) * $ressource[1];
            $coefTotal += $ressource[1];
        }

        foreach ($listeAgregations as $agreg) {
            $moyenne += $repository->moyenne($agreg[0]) * $agreg[1];
            $coefTotal += $agreg[1];
        }

        if ($coefTotal > 0) {
            $moyenne /= $coefTotal;
        }
        $moyenne = round($moyenne, 2);

        return [
            'agregation' => $agregation,
            'listeRessources' => $listeRessources,
            'listeAgregations' => $listeAgregations,
            'moyenne' => $moyenne
        ];
    }

    /**
     * @param $idAgregation
     * @return void verifie les exceptions et supprime l'agregation
     * @throws ArgNullException
     * @throws DroitException
     */
    public function supprimer($idAgregation): void
    {
        (new ServiceGenerique())->idVide($idAgregation);
        $repository = new AgregationRepository();
        $login = ConnexionUtilisateur::getLoginUtilisateurConnecte();
        $agregation = $repository->recupererParClePrimaire($idAgregation);
        if (!$agregation) {
            throw new ArgNullException("L'agrégation spécifiée n'existe pas.");
        }
        if (!ConnexionUtilisateur::estAdministrateur() &&
            (($agregation->getLoginCreateur() && $agregation->getLoginCreateur() !== $login) ||
                ($agregation->getSiretCreateur() && $agregation->getSiretCreateur() !== $login))) {
            throw new DroitException("Vous n'avez pas les droits pour supprimer cette agrégation.");
        }
        $repository->supprimer($idAgregation);
    }

    /**
     * @return array renvoie toutes les informations que le formulaire à besoin
     * @throws DroitException
     */
    public function preparerFormulaire(): array
    {
        $login = ConnexionUtilisateur::getLoginUtilisateurConnecte();
        (new ServiceGenerique())->verifDroit($login);
        if (ConnexionUtilisateur::estEcolePartenaire($login)) {
            $agregations = (new AgregationRepository())->recupererParUtilisateur($login);
        } else {
            $agregations = (new AgregationRepository())->recupererParUtilisateur("prof");
        }
        $ressources = (new RessourceRepository())->recuperer();

        return [
            'agregations' => $agregations,
            'ressources' => $ressources
        ];
    }

    /**
     * @return false|string|void permet d'enregistrer une note dans la bd
     */
    public function enregistrerNote()
    {
        (new ServiceGenerique())->verifDroit(ConnexionUtilisateur::getLoginUtilisateurConnecte());

        $nomAgregation = $_REQUEST['nomAgregation'] ?? null;
        $count = $_REQUEST['count'] ?? 0;

        if (!$nomAgregation || !$count) {
            throw new ArgNullException("Vous avez oublié le nom de l'agrégation et/ou de sélectionner des ressources");
            //  self::redirectionVersUrl("controleurFrontal.php?action=afficherFormulaire&controleur=agregation");
        }

        $tabNom = [];
        $tabCoeff = [];

        for ($i = 0; $i < $count; $i++) {
            $coeff = $_REQUEST['coeff' . $i] ?? 0;
            if ($coeff > 0) {
                $tabNom[] = $_REQUEST['idNom' . $i];
                $tabCoeff[] = $coeff;
            }
        }

        if (empty($tabNom) || empty($tabCoeff)) {
            throw new ArgNullException("Aucune ressource ou agrégation n'a été enregistrée");
        }

        $agregationRepo = new AgregationRepository();
        $etudiantRepo = new EtudiantRepository();

        // Création et enregistrement de l'agrégation

        //LE LOGIN EST TEMPORAIRE, IL SERA CHANGE DES QU ON AURA LA CONNEXION PROF ET ECOLE PARTENAIRE
        $loginCreateur = null;
        $siretCreateur = null;
        if (ConnexionUtilisateur::estAdministrateur() ||ConnexionUtilisateur::estProfesseur()) {
            $loginCreateur = "prof";
        }
        if (ConnexionUtilisateur::estEcolePartenaire(ConnexionUtilisateur::getLoginUtilisateurConnecte())) {
            $siretCreateur = ConnexionUtilisateur::getLoginUtilisateurConnecte();
        }
        $agregation = new Agregation(null, $nomAgregation, $loginCreateur, $siretCreateur);
        $idAgregation = $agregationRepo->ajouter($agregation);

        // Enregistrement des ressources/agregations liées
        foreach ($tabNom as $index => $nom) {
            if ($nom[0] === 'R') {
                $etudiantRepo->enregistrerRessourceAgregee($nom, $idAgregation, $tabCoeff[$index]);
            } else {
                $etudiantRepo->enregistrerAgregationAgregee($idAgregation, $nom, $tabCoeff[$index]);
            }
        }

        // Calcul et enregistrement des moyennes des étudiants
        $etudiants = $etudiantRepo->recuperer();
        foreach ($etudiants as $etudiant) {
            $moyenne = $etudiant->calculerMoyenne($tabNom, $tabCoeff);
            if ($moyenne != -1) {
                $agregationRepo->ajouterEtudiant($idAgregation, $etudiant->getEtudid(), $moyenne);
            }
        }
        return $idAgregation;
    }
}