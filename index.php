<?php
require_once 'config.php';

// Extraction des données statistiques
$totalFilms = $pdo->query("SELECT COUNT(*) FROM film")->fetchColumn();
$totalSeances = $pdo->query("SELECT COUNT(*) FROM seance")->fetchColumn();
$totalReservations = $pdo->query("SELECT COUNT(*) FROM reservation")->fetchColumn();

// Récupération des séances à venir
$query = "SELECT s.id_seance, s.date, s.horaire, f.titre, sa.numero 
          FROM seance s 
          JOIN film f ON s.id_film = f.id_film 
          JOIN salle sa ON s.id_salle = sa.id_salle 
          ORDER BY s.date ASC, s.horaire ASC LIMIT 5";
$seances = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinéManager - Tableau de bord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Barre latérale -->
    <aside class="sidebar">
        <div>
            <div class="brand">
                <div class="brand-icon"><i class="fa-solid fa-film"></i></div>
                <span class="brand-name">CinéManager</span>
            </div>
            <nav class="nav-menu">
                <a href="index.php" class="nav-link active">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Tableau de bord</span>
                </a>
                <a href="films.php" class="nav-link">
                    <i class="fa-solid fa-clapperboard"></i>
                    <span>Films</span>
                </a>
                <a href="reservations.php" class="nav-link">
                    <i class="fa-solid fa-ticket"></i>
                    <span>Réservations</span>
                </a>
            </nav>
        </div>
        <div class="sidebar-footer">
            CinéManager v1.0 • Groupe I
        </div>
    </aside>

    <!-- Contenu principal -->
    <main class="main-wrapper">
        <header class="header">
            <div class="page-title">
                <h1>Aperçu Général</h1>
                <p>Bienvenue sur le système d'administration du cinéma.</p>
            </div>
            <a href="reservations.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nouvelle Réservation
            </a>
        </header>

        <!-- Cartes Statistiques -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <span>Films au catalogue</span>
                    <h2><?= $totalFilms ?></h2>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-film"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <span>Séances programmées</span>
                    <h2><?= $totalSeances ?></h2>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <span>Réservations totales</span>
                    <h2><?= $totalReservations ?></h2>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-ticket"></i></div>
            </div>
        </section>

        <!-- Tableau des séances -->
        <section class="content-card">
            <div class="card-header">
                <h2>Prochaines Séances</h2>
                <input type="text" id="tableSearch" placeholder="Rechercher une séance..." class="form-control" style="width: 250px;">
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Film</th>
                            <th>Salle</th>
                            <th>Date</th>
                            <th>Horaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($seances) > 0): ?>
                            <?php foreach($seances as $s): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($s['titre']) ?></strong></td>
                                <td><?= htmlspecialchars($s['numero']) ?></td>
                                <td><?= date('d/m/Y', strtotime($s['date'])) ?></td>
                                <td><span class="badge badge-indigo"><?= date('H:i', strtotime($s['horaire'])) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted);">Aucune séance planifiée.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="js/app.js"></script>
</body>
</html>
