<?php
require_once 'config.php';

$message = '';

// Traitement : Ajout de film
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $titre = trim($_POST['titre']);
    $categorie = trim($_POST['categorie']);
    $duree = trim($_POST['duree']);
    $description = trim($_POST['description']);

    if (!empty($titre) && !empty($categorie)) {
        $stmt = $pdo->prepare("INSERT INTO film (titre, categorie, duree, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titre, $categorie, $duree, $description]);
        $message = "Film ajouté avec succès !";
    }
}

// Traitement : Suppression de film
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM film WHERE id_film = ?");
    $stmt->execute([$id]);
    header('Location: films.php');
    exit;
}

// Extraction de la liste des films
$films = $pdo->query("SELECT * FROM film ORDER BY id_film DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinéManager - Catalogue des Films</title>
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
                <a href="films.php" class="nav-link active">
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

    <main class="main-wrapper">
        <header class="header">
            <div class="page-title">
                <h1>Gestion des Films</h1>
                <p>Consultez, ajoutez ou supprimez des films du catalogue.</p>
            </div>
        </header>

        <div class="grid-layout">
            <!-- Formulaire d'ajout -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Nouveau Film</h2>
                </div>
                <form method="POST" action="films.php">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Titre du film</label>
                        <input type="text" name="titre" required class="form-control" placeholder="Ex: Inception">
                    </div>
                    <div class="form-group">
                        <label>Genre / Catégorie</label>
                        <input type="text" name="categorie" required class="form-control" placeholder="Ex: Science-Fiction">
                    </div>
                    <div class="form-group">
                        <label>Durée</label>
                        <input type="text" name="duree" class="form-control" placeholder="Ex: 148 min">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Résumé du film..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Enregistrer le Film</button>
                </form>
            </div>

            <!-- Liste des films -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Films à l'affiche</h2>
                    <input type="text" id="tableSearch" placeholder="Rechercher..." class="form-control" style="width: 200px;">
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Durée</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($films as $f): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($f['titre']) ?></strong>
                                    <p style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars(substr($f['description'], 0, 50)) ?>...</p>
                                </td>
                                <td><span class="badge badge-indigo"><?= htmlspecialchars($f['categorie']) ?></span></td>
                                <td><?= htmlspecialchars($f['duree']) ?></td>
                                <td>
                                    <a href="films.php?delete=<?= $f['id_film'] ?>" class="btn btn-danger btn-delete-confirm" style="padding: 0.4rem 0.6rem;">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="js/app.js"></script>
</body>
</html>
