<?php

namespace App\Sae\Modele\DataObject;

class Noter extends AbstractDataObject
{
    private int $etudid;
    private int $semestre_id;
    private string $nomRessource;
    private float $note;

    public function __construct(int $etudid, int $semestre_id, string $nomRessource, float $note) {
        $this->etudid = $etudid;
        $this->semestre_id = $semestre_id;
        $this->nomRessource = $nomRessource;
        $this->note = $note;
    }

    public function getEtudid(): int
    {
        return $this->etudid;
    }

    public function setEtudid(int $etudid): void
    {
        $this->etudid = $etudid;
    }

    public function getSemestre_id(): int
    {
        return $this->semestre_id;
    }

    public function setSemestre_id(int $semestre_id): void
    {
        $this->semestre_id = $semestre_id;
    }

    public function getNomRessource(): string
    {
        return $this->nomRessource;
    }

    public function setNomRessource(string $nomRessource): void
    {
        $this->nomRessource = $nomRessource;
    }

    public function getNote(): float
    {
        return $this->note;
    }

    public function setNote(float $note): void
    {
        $this->note = $note;
    }


}