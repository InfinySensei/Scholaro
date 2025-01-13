<?php

namespace App\Sae\Modele\DataObject;

use App\Sae\Modele\Repository\FormationRepository;

class Formation extends AbstractDataObject
{

    private string $formation;

    /**
     * @param string $formation
     */
    public function __construct(string $formation)
    {
        $this->formation = $formation;
    }

    public function getFormation(): string {
        return $this->formation;
    }

    public function setFormation(string $formation): void {
        $this->formation = $formation;
    }
}