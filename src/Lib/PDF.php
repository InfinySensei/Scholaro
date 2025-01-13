<?php

namespace App\Sae\Lib;

use App\Sae\Modele\DataObject\Etudiant;


require_once __DIR__ . '/fpdf/fpdf.php';

// Classe personnalisée pour le PDF
class PDF extends \FPDF
{
    public function creerPage(Etudiant $etudiant)
    {
        $this->AddPage();
        $this->SetFont('Arial', 'b', 10);

        $this->Cell(0,4.5, mb_convert_encoding("Fiche Avis Poursuite d'Études - Promotion 2024-2025", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->Cell(0,4.5, mb_convert_encoding("Département Informatique", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->Cell(0,4.5, 'IUT Montpellier-Sete', 0, 1, 'C');
        $this->Cell(0, 10, 'FICHE D\'INFORMATION ETUDIANT(E)', 0, 1, 'L');
        $x = $this->GetX(); // Position X actuelle
        $y = $this->GetY(); // Position Y actuelle
        $this->Line($x - 1, $y - 2, $x + 191, $y - 2); // Ligne sous le texte
        $this->Ln(3);

        $this->SetFont('Arial', '', 10);
        $this->Cell(70, 10, 'NOM', 1, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(105, 10, mb_convert_encoding($etudiant->getNomEtu(), 'ISO-8859-1', 'UTF-8'), 1, 1);

        $this->Cell(70, 10, 'Prenom', 1, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(105, 10, mb_convert_encoding($etudiant->getPrenomEtu(), 'ISO-8859-1', 'UTF-8'), 1, 1);

        $this->Cell(70, 10, 'Apprentissage en BUT 3 : (oui/non)', 1, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(105, 10, mb_convert_encoding("«Alternance»", 'ISO-8859-1', 'UTF-8'), 1, 1);

        $this->Cell(70, 10, 'Parcours BUT', 1, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(105, 10, mb_convert_encoding("«Parcours»", 'ISO-8859-1', 'UTF-8'), 1, 1);

        $this->Ln(10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, mb_convert_encoding('Avis de l\'équipe pédagogique pour la poursuite d\'études après le BUT 3', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $x = $this->GetX(); // Position X actuelle
        $y = $this->GetY(); // Position Y actuelle
        $this->Line($x - 1, $y - 2, $x + 191, $y - 2); // Ligne sous le texte
        $this->Ln(3);

        $this->SetFont('Arial', '', 10);

        $y = $this->GetY();
        $this->MultiCell(34, 24, mb_convert_encoding('Pour l\'étudiant', 'ISO-8859-1', 'UTF-8'), 1, 'C');
        $this->SetXY($this->GetX() + 34, $y);

        $this->MultiCell(50, 7, mb_convert_encoding('En école d\'ingénieur et  master en informatique', 'ISO-8859-1', 'UTF-8'), 1, 'L');
        $y2 = $this->GetY();
        $this->SetXY($this->GetX() + 84, $y);
        $this->SetFont('Arial', 'b', 10);
        $this->MultiCell(60, ($y2 - $y)/2, mb_convert_encoding('«Avis_Ecole_dingénieur_et_master_en_info»', 'ISO-8859-1', 'UTF-8'), 1, 'C');

        $this->SetFont('Arial', '', 10);
        $y = $this->GetY();
        $this->SetXY($this->GetX() + 34, $y);
        $this->MultiCell(50, 10, 'En master en management', 1);
        $y2 = $this->GetY();
        $this->SetXY($this->GetX() + 84, $y);
        $this->SetFont('Arial', 'b', 10);
        $this->MultiCell(60, ($y2 - $y), mb_convert_encoding('«Avis_Master_en_management»', 'ISO-8859-1', 'UTF-8'), 1, 'C');

        $this->Ln(10);
        $this->SetFont('Arial', '', 10);
        $y = $this->GetY();
        $this->MultiCell(34, 5, 'Nombre d\'avis pour la promotion', 1);
        $this->SetXY($this->GetX() + 34, $y);
        $this->MultiCell(39, 10, '', 1, 0);
        $this->SetXY($this->GetX() + 73, $y);
        $this->MultiCell(39, 10, mb_convert_encoding('Très Favorable', 'ISO-8859-1', 'UTF-8'), 1, 'C');
        $this->SetXY($this->GetX() + 112, $y);
        $this->MultiCell(39, 10, 'Favorable', 1, 'C');
        $this->SetXY($this->GetX() + 151, $y);
        $this->MultiCell(39, 10, mb_convert_encoding('Réservé', 'ISO-8859-1', 'UTF-8'), 1, 'C');

        $y = $this->GetY();
        $this->MultiCell(34, 12, '', 1);
        $this->SetXY($this->GetX() + 34, $y);
        $this->MultiCell(39, 6, mb_convert_encoding('En école d\'ingénieur et master en informatique', 'ISO-8859-1', 'UTF-8'), 1, 'L');
        $this->SetXY($this->GetX() + 73, $y);
        $this->MultiCell(39, 12, 37, 1, 'C');
        $this->SetXY($this->GetX() + 112, $y);
        $this->MultiCell(39, 12, 20, 1, 'C');
        $this->SetXY($this->GetX() + 151, $y);
        $this->MultiCell(39, 12, 33, 1, 'C');

        $y = $this->GetY();
        $this->MultiCell(34, 12, '', 1);
        $this->SetXY($this->GetX() + 34, $y);
        $this->MultiCell(39, 6, mb_convert_encoding('Master en management ', 'ISO-8859-1', 'UTF-8'), 1, 'L');
        $this->SetXY($this->GetX() + 73, $y);
        $this->MultiCell(39, 12, 44, 1, 'C');
        $this->SetXY($this->GetX() + 112, $y);
        $this->MultiCell(39, 12, 40, 1, 'C');
        $this->SetXY($this->GetX() + 151, $y);
        $this->MultiCell(39, 12, 6, 1, 'C');

        $this->Ln(10);
        $this->SetXY($this->GetX() + 105, $this->GetY());
        $this->MultiCell(85, 7, mb_convert_encoding('Signature du Responsable des Poursuites d\'études par délégation du chef de département', 'ISO-8859-1', 'UTF-8'), 0, 1);
    }

    public function afficherPage() {
        $this->Output('', 'Avis PE_2024.pdf');
    }

    // En-tête de page
    function Header()
    {
        $this->Image('../ressources/images/dep_info.png', 3, 5, 44);
        $this->Image('../ressources/images/um_noir.png', 152, 1, 18);
        $this->Image('../ressources/images/Logo_IUT_RVB.jpg', 175, 4, 25);
        $this->Ln(11);
    }

    // Pied de page
    function Footer()
    {
        $this->SetY(-15);
    }
}