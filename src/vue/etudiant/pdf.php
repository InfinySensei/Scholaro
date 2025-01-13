<?php

use App\Sae\Lib\PDF;

/**
 * @var \App\Sae\Modele\DataObject\Etudiant $etudiant
 */
$pdf = new PDF();
$pdf->creerPage($etudiant);
$pdf->afficherPage();
?>