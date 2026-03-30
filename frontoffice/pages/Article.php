<?php
include_once '../../backoffice/connect/Connect.php';

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function excerptText($html, $maxLength = 150) {
    $text = trim(strip_tags((string) $html));
    if (mb_strlen($text) <= $maxLength) return $text;
    return mb_substr($text, 0, $maxLength) . '...';
}

$conn = getConnection();
$error = '';
$articles = [];
$categories = [];

// Paramètres de filtrage
$search = $_GET['q'] ?? '';
$categoryId = $_GET['category'] ?? '';

try {
    // 1. Récupérer toutes les catégories pour le menu déroulant du filtre
    $catResult = $conn->query("SELECT id, name FROM category_article ORDER BY name ASC");
    if ($catResult) {
        $categories = $catResult->fetch_all(MYSQLI_ASSOC);
    }

    // 2. Construire la requête principale avec des filtres dynamiques
    $sql = "SELECT 
                a.id, a.title, a.slug, a.summary, a.created_at, 
                c.name AS category_name,
                COALESCE(ai_main.image_url, ai_any.image_url) AS image_url
            FROM article a
            LEFT JOIN category_article c ON c.id = a.category_id
            LEFT JOIN article_image ai_main ON ai_main.article_id = a.id AND ai_main.is_main = 1
            LEFT JOIN article_image ai_any ON ai_any.article_id = a.id
            WHERE a.status = 'publie' "; // On ne montre que les articles publiés en front

    $params = [];
    $types = "";

    // Application du filtre de recherche (titre ou contenu)
    if ($search !== '') {
        $sql .= " AND (a.title LIKE ? OR a.content LIKE ?) ";
        $searchParam = "%" . $search . "%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= "ss";
    }

    // Application du filtre de catégorie
    if ($categoryId !== '' && is_numeric($categoryId)) {
        $sql .= " AND a.category_id = ? ";
        $params[] = $categoryId;
        $types .= "i";
    }

    $sql .= " GROUP BY a.id ORDER BY a.created_at DESC";

    $stmt = $conn->prepare($sql);
    
    // Bind dynamique des paramètres s'il y en a
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $articles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Throwable $exception) {
    $error = $exception->getMessage();
} finally {
    if (isset($conn)) closeConnection($conn);
}

$defaultCardImage = 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&w=400&q=80';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les articles | InfoFlash</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Open Sans', sans-serif; background-color: #fcfcfc; color: #333; }
        h1, h2, h3, h4, h5, .navbar-brand { font-family: 'Merriweather', serif; font-weight: 700; color: #111; }
        .journal-header { border-top: 2px solid #111; border-bottom: 2px solid #111; background: #fff; }
        .card { border: 1px solid #ebebeb; border-radius: 0; box-shadow: none; transition: background 0.2s; }
        .card:hover { background: #f8f9fa; }
        .category-badge { font-family: 'Open Sans', sans-serif; font-size: 0.75rem; letter-spacing: 1px; color: #b71c1c; font-weight: 600; text-transform: uppercase; }
        .filter-sidebar { background: #fff; border: 1px solid #ebebeb; padding: 20px; position: sticky; top: 80px; }
    </style>
</head>
<body>

    <header class="bg-white py-3">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-0">INFOFLASH</h1>
            <p class="text-muted small text-uppercase tracking-widest mb-0"><?php echo date('l j F Y'); ?></p>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg navbar-light journal-header sticky-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link fw-bold px-4" href="../index.php">À LA UNE</a></li>
                    <li class="nav-item"><a class="nav-link active fw-bold px-4" href="article.php">TOUS LES ARTICLES</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row">
            <aside class="col-lg-3 mb-4">
                <div class="filter-sidebar">
                    <h4 class="h5 mb-3 border-bottom pb-2">Filtrer les résultats</h4>
                    <form method="GET" action="article.php">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Recherche par mot-clé</label>
                            <div class="input-group">
                                <input type="text" name="q" class="form-control rounded-0" placeholder="Ex: politique, climat..." value="<?php echo e($search); ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Catégorie</label>
                            <select name="category" class="form-select rounded-0">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($categoryId == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo e($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 rounded-0 fw-bold">Appliquer les filtres</button>
                        <?php if ($search || $categoryId): ?>
                            <a href="article.php" class="btn btn-outline-secondary w-100 rounded-0 mt-2 small">Réinitialiser</a>
                        <?php endif; ?>
                    </form>
                </div>
            </aside>

            <div class="col-lg-9">
                <h2 class="border-bottom border-dark pb-2 mb-4">Archives & Actualités</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger rounded-0"><?php echo e($error); ?></div>
                <?php endif; ?>

                <?php if (empty($articles)): ?>
                    <div class="alert alert-light border rounded-0 text-center py-5">
                        <i class="fa-regular fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="lead mb-0">Aucun article ne correspond à votre recherche.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($articles as $article): 
                            $cardImage = !empty($article['image_url']) ? $article['image_url'] : $defaultCardImage;
                            $category = !empty($article['category_name']) ? $article['category_name'] : 'Édition';
                            $date = !empty($article['created_at']) ? date('d/m/Y', strtotime($article['created_at'])) : '';
                        ?>
                            <div class="col-md-4">
                                <article class="card h-100">
                                    <img src="<?php echo e($cardImage); ?>" loading="lazy" class="card-img-top rounded-0" style="height:180px; object-fit:cover;" alt="Image d'illustration">
                                    <div class="card-body">
                                        <div class="category-badge mb-2"><?php echo e($category); ?></div>
                                        <h3 class="h5 card-title"><a href="../lire.php?slug=<?php echo e($article['slug']); ?>" class="text-dark text-decoration-none stretched-link"><?php echo e($article['title']); ?></a></h3>
                                        <p class="card-text text-muted small mt-2"><?php echo e(excerptText($article['summary'] ?? '', 100)); ?></p>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pt-0">
                                        <small class="text-muted fst-italic">Publié le <?php echo e($date); ?></small>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0 small">&copy; <?php echo date("Y"); ?> InfoFlash - Site de démonstration éditoriale.</p>
        </div>
    </footer>

</body>
</html>