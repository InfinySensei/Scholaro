<?php

namespace App\Sae\Modele\DataObject;

use App\Sae\Modele\Repository\AvisRepository;

class Avis extends AbstractDataObject
{

    private string $avis;


    /**
     * @param string $avis
     */
    public function __construct(string $avis) {
        $this->avis = $avis;
    }

    public function getAvis(): string {
        return $this->avis;
    }

    public function setAvis(string $avis): void {
        $this->avis = $avis;
    }
}