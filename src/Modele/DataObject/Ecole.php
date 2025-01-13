<?php

namespace App\Sae\Modele\DataObject;

use App\Sae\Modele\DataObject\AbstractDataObject;

class Ecole extends AbstractDataObject
{
    private string $siret;
    private string $nomEcole;
    private string $villeEcole;
    private string $tel;
    private string $mailEcole;
    private bool $estValide;

    private bool $mailValider;
    private string $nonce;
    private string $mdpHache;

    /**
     * @param string $siret
     * @param string $nomEcole
     * @param string $villeEcole
     * @param string $tel
     * @param string $mailEcole
     * @param bool $estValide
     * @param bool $mailValider
     * @param string $nonce
     * @param string $mdpHache
     */
    public function __construct(string $siret, string $nomEcole, string $villeEcole, string $tel, string $mailEcole, bool $estValide, bool $mailValider, string $nonce, string $mdpHache)
    {
        $this->siret = $siret;
        $this->nomEcole = $nomEcole;
        $this->villeEcole = $villeEcole;
        $this->tel = $tel;
        $this->mailEcole = $mailEcole;
        $this->estValide = $estValide;
        $this->mailValider = $mailValider;
        $this->nonce = $nonce;
        $this->mdpHache = $mdpHache;
    }

    public function getTel(): string
    {
        return $this->tel;
    }

    public function setTel(string $tel): void
    {
        $this->tel = $tel;
    }

    public function getMail(): string
    {
        return $this->mailEcole;
    }

    public function setMail(string $mailEcole): void
    {
        $this->mailEcole = $mailEcole;
    }

    public function isEstValide(): bool
    {
        return $this->estValide;
    }

    public function setEstValide(bool $estValide): void
    {
        $this->estValide = $estValide;
    }

    public function getMdpHache(): string
    {
        return $this->mdpHache;
    }

    public function setMdpHache(string $mdpHache): void
    {
        $this->mdpHache = $mdpHache;
    }

    public function getSiret(): string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): void
    {
        $this->siret = $siret;
    }

    public function getNomEcole(): string
    {
        return $this->nomEcole;
    }

    public function setNomEcole(string $nomEcole): void
    {
        $this->nomEcole = $nomEcole;
    }

    public function getVilleEcole(): string
    {
        return $this->villeEcole;
    }

    public function setVilleEcole(string $villeEcole): void
    {
        $this->villeEcole = $villeEcole;
    }

    public function getNonce(): string
    {
        return $this->nonce;
    }

    public function setNonce(string $nonce): void
    {
        $this->nonce = $nonce;
    }

    public function getMailEcole(): string
    {
        return $this->mailEcole;
    }

    public function setMailEcole(string $mailEcole): void
    {
        $this->mailEcole = $mailEcole;
    }

    public function isMailValider(): bool
    {
        return $this->mailValider;
    }

    public function setMailValider(bool $mailValider): void
    {
        $this->mailValider = $mailValider;
    }



}