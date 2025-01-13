<div class="detail content">
    <h1>
        Généralités
    </h1>
    <?php
    /**
     * @var \App\Sae\Modele\DataObject\Etudiant $etudiant
     * @var \App\Sae\Modele\DataObject\Ecole[] $ecolesChoisie
     * @var string $regarder
     * @var string[][] $avis
     */

    $nomEtudiant = htmlspecialchars($etudiant->getNomEtu());
    $prenomEtudiant = htmlspecialchars($etudiant->getPrenomEtu());
    $civ = htmlspecialchars($etudiant->getCiv());
    $bac = htmlspecialchars($etudiant->getBac());
    $spe = htmlspecialchars($etudiant->getSpecialite());
    $codeNip = htmlspecialchars($etudiant->getCodenip());


    ?>

    <p> Code Nip : <?= $codeNip ?></p>
    <p> Nom : <?= $nomEtudiant ?> </p>
    <p> Prénom : <?= $prenomEtudiant ?> </p>
    <p> Civilité : <?= $civ ?> </p>
    <p> Baccalauréat : <?= $bac ?> </p>
    <p> Spécialité : <?= $spe ?> </p>

    <?php
    $loginURL = $etudiant->getEtudid();
    echo "<p><a href=\"controleurFrontal.php?controleur=etudiant&action=afficherPdf&idEtudiant=$loginURL\">Afficher la fiche Avis Poursuite d'Études</a></p>"
    ?>


    <?php
    $methode = "";
    $action = "";
    if ($regarder == "admin") $action = "ajouterAvis"; else $action = "ajouterEcoleFavoris";
    if (\App\Sae\Configuration\ConfigurationSite::getDebug()) $methode = "get"; else $methode = "post";

    if ($regarder == "admin") {
        $avisTot = (new \App\Sae\Modele\Repository\AvisRepository())->recuperer();
        $formations = (new \App\Sae\Modele\Repository\FormationRepository())->recuperer();
        if (!empty($avisTot)) {
            echo '<h1>Avis Formation</h1>
                <form method="' . $methode . '" action="?">
               ';
            foreach ($formations as $formation) {
                $formationNom = $formation->getFormation();

                echo '<div>';
                echo '<label for="' . urldecode($formationNom) . '">' . htmlspecialchars($formationNom) . '</label>';
                echo '<select name="avisFormation[' . urldecode($formationNom) . ']" id="' . urldecode($formationNom) . '">';

                foreach ($avisTot as $avis) {
                    echo '<option value="' . urldecode($avis->getAvis()) . '">' . htmlspecialchars($avis->getAvis()) . '</option>';
                }

                echo '</select>';
                echo '</div>';
            }

            echo '<input type="hidden" name="idEtudiant" value="' . $etudiant->getEtudid() . '">
                <input type="hidden" name="action" value="' . $action . '">
                <input type="hidden" name="controleur" value="etudiant">
                <input type="hidden" name="regarder" value="admin">
                <input type="submit" name="valider" value="Valider">
                </form>';
        }
    } else if ($regarder == "") {
        echo '<h1>Ecole Favorite</h1>
            <form method="' . $methode . '" action="?">
           ';
        foreach ((new \App\Sae\Modele\Repository\EcoleRepository())->recuperer() as $ecole) {
            if ($ecole->isEstValide()) {


                $check = "";
                if (!empty($ecolesChoisie)) {
                    if (in_array($ecole, $ecolesChoisie)) {
                        $check = "checked";
                    }
                }
                echo '<input type="hidden" name="idEcoles[]" value="' . $ecole->getSiret() . 'False">
                <input type="checkbox" name="idEcoles[]" value="' . $ecole->getSiret() . '" id="' . $ecole->getSiret() . '" ' . $check . '>
        <label for="' . $ecole->getSiret() . '">' . htmlspecialchars($ecole->getNomEcole()) . '</label>
        ';
            }
        }
        echo '<input type="hidden" name="idEtudiant" value="' . $etudiant->getEtudid() . '">
                <input type="hidden" name="action" value="' . $action . '">
                <input type="hidden" name="controleur" value="etudiant">
                <input type="submit" name="valider" value="Valider">
                </form>';
    }
    ?>

</div>
<div class="content">
    <h1>
        Notes
    </h1>
    <?php

    /**
     * @var $notesAgregees \App\Sae\Modele\DataObject\Agregation[]
     */

    $idEtu = $etudiant->getEtudid();
    if (!empty($notes)) {
        $id = 0;
        foreach ($notes as $note) {
            echo "<p>$note[2] : $note[3] </p>";
            $id += 1;
        }
    } else {
        echo "<p> L'étudiant n'a pas de notes</p>";
    }

    if ($etudiant->getAvis() != null) {
        echo '<p> Avis </p>';
        echo '<p>' . htmlspecialchars($etudiant->getAvis()) . ' </p>';
    }
    ?>
</div>
<div class="content">
    <h1> Graph </h1>
    <?php
    if ($notesAgregees != null) {
        $labels = [];
        $notes = [];
        foreach ($notesAgregees as $noteAgregee) {
            $labels[] = $noteAgregee['nomAgregation'];
            $notes[] = $noteAgregee['note'];
        }
        $labelsJSON = json_encode($labels);
        $notesJSON = json_encode($notes);
        /**
         * @var $moyennes array
         */
        $moyennesJSON = json_encode($moyennes);
        ?>
        <form id="filterForm">
            <h3>Sélectionner les agrégations à afficher :</h3>
            <?php foreach ($notesAgregees as $index => $noteAgregee): ?>
                <label>
                    <input type="checkbox" name="filters[]" value="<?= $index ?>" checked>
                    <?= $noteAgregee['nomAgregation'] ?>
                </label><br>
            <?php endforeach; ?>
            <button type="button" onclick="updateChart()">Mettre à jour le graphique</button>
        </form>

        <div style="width: 750px; height: 750px; margin: 0 auto;">
            <canvas id="radarChart"></canvas>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const labels = <?php echo $labelsJSON; ?>;
            const notes = <?php echo $notesJSON; ?>;
            const moyennes = <?php echo $moyennesJSON; ?>;
            const ctx = document.getElementById('radarChart').getContext('2d');

            function updateChart() {
                const selectedIndexes = Array.from(document.querySelectorAll('input[name="filters[]"]:checked')).map(input => parseInt(input.value));
                const filteredLabels = selectedIndexes.map(index => labels[index]);
                const filteredNotes = selectedIndexes.map(index => notes[index]);
                const filteredMoyennes = selectedIndexes.map(index => moyennes[index]);

                const config = {
                    type: 'radar',
                    data: {
                        labels: filteredLabels,
                        datasets: [
                            {
                                label: 'Notes Agrégées',
                                data: filteredNotes,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                                pointBorderColor: '#fff',
                                pointHoverRadius: 6,
                            },
                            {
                                label: 'Moyennes',
                                data: filteredMoyennes,
                                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                                borderColor: 'rgba(255, 206, 86, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                                pointBorderColor: '#fff',
                                pointHoverRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                min: 0,
                                max: 20,
                                ticks: {
                                    stepSize: 2,
                                    showLabelBackdrop: false,
                                    color: '#666',
                                    backdropPadding: 3,
                                    z: 1
                                },
                                grid: {
                                    color: 'rgba(200, 200, 200, 0.3)',
                                },
                                angleLines: {
                                    color: 'rgba(200, 200, 200, 0.5)',
                                    display: true,
                                },
                                pointLabels: {
                                    font: {
                                        size: 16
                                    },
                                    color: '#333',
                                    padding: 10,
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        layout: {
                            padding: {
                                top: 30,
                                bottom: 30,
                            }
                        }
                    }
                };
                if (window.chartInstance) {
                    window.chartInstance.destroy();
                }
                window.chartInstance = new Chart(ctx, config);
            }

            updateChart();
        </script>
        <?php
    } else {
        echo '<p>Aucune note agrégée à modéliser pour cet étudiant</p>';
    }
    ?>

</div>

