<?php

namespace App\Sae\Modele\DataObject;

use App\Sae\Modele\Repository\EtudiantRepository;

class Etudiant extends AbstractDataObject
{
    private int $etudid;
    private string $codenip;
    private string $civ;
    private string $nomEtu;
    private string $prenomEtu;
    private string $bac;
    private string $specialite;
    private int $rg_admis;
    private string $avis;

    private string $login;

    /**
     * @param int $etudid
     * @param string $codenip
     * @param string $civ
     * @param string $nom
     * @param string $prenom
     * @param string $bac
     * @param string $specialite
     * @param int $rgadmis
     * @param string $avis
     */
    public function __construct(int $etudid, string $codenip, string $civ, string $nom, string $prenom, string $bac, string $specialite, int $rgadmis, string $avis, string $login)
    {
        $this->etudid = $etudid;
        $this->codenip = $codenip;
        $this->civ = $civ;
        $this->nomEtu = $nom;
        $this->prenomEtu = $prenom;
        $this->bac = $bac;
        $this->specialite = $specialite;
        $this->rg_admis = $rgadmis;
        $this->avis = $avis;
        $this->login = $login;
    }

    public function getEtudid(): int
    {
        return $this->etudid;
    }

    public function setEtudid(int $etudid): void
    {
        $this->etudid = $etudid;
    }

    public function getCodenip(): string
    {
        return $this->codenip;
    }

    public function setCodenip(string $codenip): void
    {
        $this->codenip = $codenip;
    }

    public function getCiv(): string
    {
        return $this->civ;
    }

    public function setCiv(string $civ): void
    {
        $this->civ = $civ;
    }

    public function getNomEtu(): string
    {
        return $this->nomEtu;
    }

    public function setNomEtu(string $nomEtu): void
    {
        $this->nomEtu = $nomEtu;
    }

    public function getPrenomEtu(): string
    {
        return $this->prenomEtu;
    }

    public function setPrenomEtu(string $prenomEtu): void
    {
        $this->prenomEtu = $prenomEtu;
    }

    public function getBac(): string
    {
        return $this->bac;
    }

    public function setBac(string $bac): void
    {
        $this->bac = $bac;
    }

    public function getSpecialite(): string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): void
    {
        $this->specialite = $specialite;
    }

    public function getRgadmis(): int
    {
        return $this->rg_admis;
    }

    public function setRgadmis(int $rg_admis): void
    {
        $this->rg_admis = $rg_admis;
    }

    public function getAvis(): string
    {
        return $this->avis;
    }

    public function setAvis(string $avis): void
    {
        $this->avis = $avis;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }


    /**
     * @return float
     * permet de calculer la moyenne d'un étudiant
    */
    public function calculerMoyenne(array $tabNom, array $tabCoeff): float
    {
        $etudiants = (new EtudiantRepository())->recuperer();
        $diviseur = 0;
        $numerateur = 0;
        for ($i = 0; $i < count($tabNom); $i++) {
            if ($tabNom[$i][0] === 'R') {
                $note = (new EtudiantRepository())->existeDansNoter($this->getEtudid() , $tabNom[$i]);
                if ($note) {
                    $diviseur += (float) $tabCoeff[$i];
                    $numerateur += (float) $note[0] *(float) $tabCoeff[$i];
                }
            } else {
                $note = (new EtudiantRepository())->existeDansAgregationRessource($this->getEtudid(), $tabNom[$i]);
                if ($note) {
                    $diviseur += $tabCoeff[$i];
                    $numerateur += $note[0] * $tabCoeff[$i];
                }
            }
        }
        if($diviseur != 0) {
            return ($numerateur / $diviseur);
        }else{
            return -1;
        }
    }


}