<?php

namespace App\Sae\Modele\DataObject;

use App\Sae\Modele\DataObject\AbstractDataObject;
use App\Sae\Modele\Repository\RessourceRepository;

class Ressource extends AbstractDataObject
{
    private string $nomRessource;

    /**
     * @param string $nomRessource
     */
    public function __construct(string $nomRessource)
    {
        $this->nomRessource = $nomRessource;
    }


    public function getNomRessource(): string
    {
        return $this->nomRessource;
    }

    public function setNomRessource(string $nomRessource): void
    {
        $this->nomRessource = $nomRessource;
    }


}