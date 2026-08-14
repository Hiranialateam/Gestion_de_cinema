<?php
require_once 'config.php';

$billetGenere = null;

// Traitement de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $contact = trim($_POST['contact']);
    $id_seance = (int)$_POST['id_seance'];
    $numero_place = trim($_POST['numero_place']);
    $tarif = (float)$_POST['tarif'];

    if (!empty($nom) && !empty($prenom) && $id_seance > 0) {
        // 1. Inscription ou enregistrement du Client
        $stmtClient = $pdo->prepare("INSERT INTO client (nom, prenom, contact) VALUES (?, ?, ?)");
        $stmtClient->execute([$nom, $prenom, $contact]);
        $id_client = $pdo->lastInsertId();

        // 2. Création de la Réservation
        $stmtRes = $pdo->prepare("INSERT INTO reservation (id_client, id_seance) VALUES (?, ?)");
        $stmtRes->execute([$id_client, $id_seance]);
        $id_reservation = $pdo->lastInsertId();

        // 3. Génération du Billet
        $stmtBillet = $pdo->prepare("INSERT INTO billet (numero_place, tarif, id_reservation) VALUES (?, ?, ?)");
        $stmtBillet->execute([$numero_place, $tarif, $id_reservation]);

        // Informations pour le billet d'entrée
        $stmtInfo = $pdo->prepare("SELECT f.titre, s.date, s.horaire, sa.numero as salle 
                                   FROM seance s 
                                   JOIN film f ON s.id_film = f.id_film 
                                   JOIN salle sa ON s.id_salle = sa.id_salle 
                                   WHERE s.id_seance = ?");
        $stmtInfo->execute([$id_seance]);
        $seanceInfo = $stmtInfo->fetch();

        $billetGenere = [
            'id' => $pdo->lastInsertId(),
            'client' => $nom . ' ' . $prenom,
            'film' => $seanceInfo['titre'],
            'salle' => $seanceInfo['salle'],
            'date' => $seanceInfo['date'],
            'horaire' => $seanceInfo['horaire'],
            'place' => $numero_place,
            'tarif' => $tarif
        ];
    }
}

// Charger la liste des séances
$seances = $pdo->query("SELECT s.id_seance, s.date, s.horaire, f.titre, sa.numero 
                        FROM seance s 
                        JOIN film f ON s.id_film = f.id_film 
                        JOIN salle sa ON s.id_salle = sa.id_salle 
                        ORDER BY s.date ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinéManager - Guichet & Réservations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <aside class="sidebar">
        <div>
            <div class="brand">
                <div class="brand-icon"><i class="fa-solid fa-film"></i></div>
                <span class="brand-name">CinéManager</span>
            </div>
            <nav class="nav-menu">
                <a href="index.php" class="nav-link">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Tableau de bord</span>
                </a>
                <a href="films.php" class="nav-link">
                    <i class="fa-solid fa-clapperboard"></i>
                    <span>Films</span>
                </a>
                <a href="reservations.php" class="nav-link active">
                    <i class="fa-solid fa-ticket"></i>
                    <span>Réservations</span>
                </a>
            </nav>
        </div>
        <div class="sidebar-footer">
            CinéManager v1.0 • Groupe I
        </div>
    </aside>

    <main class="main-wrapper">
        <header class="header">
            <div class="page-title">
                <h1>Guichet & Billetterie</h1>
                <p>Enregistrez les réservations et émettez les billets d'accès.</p>
            </div>
        </header>

        <div class="grid-layout">
            <!-- Formulaire de réservation -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Nouvelle Vente</h2>
                </div>
                <form method="POST" action="reservations.php">
                    <div class="form-group">
                        <label>Nom du Client</label>
                        <input type="text" name="nom" required class="form-control" placeholder="RANAIVO">
                    </div>
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" required class="form-control" placeholder="Jean">
                    </div>
                    <div class="form-group">
                        <label>Contact / Téléphone</label>
                        <input type="text" name="contact" required class="form-control" placeholder="+261 34 00 000 00">
                    </div>
                    <div class="form-group">
                        <label>Sélectionner la Séance</label>
                        <select name="id_seance" required class="form-control">
                            <?php foreach($seances as $s): ?>
                                <option value="<?= $s['id_seance'] ?>">
                                    <?= htmlspecialchars($s['titre']) ?> - <?= $s['numero'] ?> (<?= date('d/m', strtotime($s['date'])) ?> à <?= date('H:i', strtotime($s['horaire'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group" style="flex: 1;">
                            <label>Siège / Place</label>
                            <input type="text" name="numero_place" required class="form-control" placeholder="Ex: A-12">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Tarif (€ / Ar)</label>
                            <input type="number" step="0.01" name="tarif" value="10.00" required class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Émettre le Billet</button>
                </form>
            </div>

            <!-- Aperçu du Billet -->
            <div>
                <?php if ($billetGenere): ?>
                    <div class="ticket-wrapper">
                        <div class="ticket-header">
                            <div>
                                <span class="badge badge-emerald">Billet Confirmé</span>
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem;">Ticket #<?= $billetGenere['id'] ?></p>
                            </div>
                            <i class="fa-solid fa-qrcode" style="font-size: 2rem; color: var(--accent-color);"></i>
                        </div>
                        <div class="ticket-body">
                            <div class="ticket-detail" style="grid-column: span 2;">
                                <label>Film</label>
                                <p><?= htmlspecialchars($billetGenere['film']) ?></p>
                            </div>
                            <div class="ticket-detail">
                                <label>Client</label>
                                <p style="font-size: 0.95rem;"><?= htmlspecialchars($billetGenere['client']) ?></p>
                            </div>
                            <div class="ticket-detail">
                                <label>Salle</label>
                                <p style="font-size: 0.95rem;"><?= htmlspecialchars($billetGenere['salle']) ?></p>
                            </div>
                            <div class="ticket-detail">
                                <label>Date & Heure</label>
                                <p style="font-size: 0.95rem;"><?= date('d/m/Y', strtotime($billetGenere['date'])) ?> à <?= date('H:i', strtotime($billetGenere['horaire'])) ?></p>
                            </div>
                            <div class="ticket-detail">
                                <label>Place</label>
                                <p style="color: var(--accent-color);"><?= htmlspecialchars($billetGenere['place']) ?></p>
                            </div>
                        </div>
                        <button onclick="window.print()" class="btn btn-primary" style="margin-top: 1.5rem; width: 100%; justify-content: center;">
                            <i class="fa-solid fa-print"></i> Imprimer le Billet
                        </button>
                    </div>
                <?php else: ?>
                    <div class="content-card" style="text-align: center; padding: 4rem 2rem; color: var(--text-muted);">
                        <i class="fa-solid fa-ticket" style="font-size: 3rem; margin-bottom: 1rem; color: var(--border-color);"></i>
                        <p>Remplissez le formulaire ci-contre pour générer et imprimer un billet d'entrée.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="js/app.js"></script>
</body>
</html>
