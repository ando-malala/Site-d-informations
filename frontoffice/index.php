<?php
include_once '../backoffice/connect/Connect.php';

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function excerptText($html, $maxLength = 180) {
    $text = trim(strip_tags((string) $html));
    if (mb_strlen($text) <= $maxLength) return $text;
    return mb_substr($text, 0, $maxLength) . '...';
}

$articles = [];
$error = '';
// Initialise un tableau vide pour les catégories
$categories = [];

try {
    $conn = getConnection();
    
    // ... [Tes autres requêtes existantes pour les articles] ...

    // Nouvelle requête : Récupérer les catégories pour la navbar
    $catSql = "SELECT id, name FROM category_article ORDER BY name ASC";
    $catStmt = $conn->prepare($catSql);
    $catStmt->execute();
    $categories = $catStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    closeConnection($conn);
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

try {
    $conn = getConnection();
    // On ne sélectionne que les articles 'publie'
    $sql = "SELECT
                a.id, a.title, a.slug, a.summary, a.created_at,
                c.name AS category_name,
                COALESCE(ai_main.image_url, ai_any.image_url) AS image_url
            FROM article a
            LEFT JOIN category_article c ON c.id = a.category_id
            LEFT JOIN article_image ai_main ON ai_main.article_id = a.id AND ai_main.is_main = 1
            LEFT JOIN article_image ai_any ON ai_any.article_id = a.id
            WHERE a.status = 'publie'
            GROUP BY a.id
            ORDER BY a.created_at DESC LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $articles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    closeConnection($conn);
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

$defaultHeroImage = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1200&q=80';
$defaultCardImage = 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&w=400&q=80';

$heroArticle = $articles[0] ?? null;
$listArticles = array_slice($articles, 1, 4); // Les 4 suivants pour les cartes
$filInfo = array_slice($articles, 5); // Le reste pour le fil info
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfoFlash | L'actualité en continu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Open Sans', sans-serif; background-color: #fcfcfc; color: #111; }
        h1, h2, h3, h4, h5, .logo-text { font-family: 'Merriweather', serif; font-weight: 700; color: #000; }
        
        .logo-text { font-size: 3rem; letter-spacing: -1px; }
        .journal-header { border-top: 1px solid #ccc; border-bottom: 2px solid #000; background: #fff; }
        .nav-link { color: #333; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; }
        
        /* Grille éditoriale */
        .article-border { border-right: 1px solid #e0e0e0; }
        .hero-title { font-size: 2.5rem; line-height: 1.1; }
        .category-label { color: #b71c1c; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; font-family: 'Open Sans', sans-serif; }
        
        /* Fil info */
        .fil-info-item { border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px; }
        .fil-info-time { color: #b71c1c; font-weight: 600; font-size: 0.8rem; }
        
        a.text-dark:hover { color: #b71c1c !important; text-decoration: underline !important; }
        @media (max-width: 991px) { .article-border { border-right: none; } }
    </style>
</head>
<body>

    <header class="bg-white pt-4 pb-2">
        <div class="container text-center">
            <div class="logo-text">INFOFLASH</div>
            <p class="text-muted small fst-italic mb-2">Édition en continu &mdash; <?php echo date('l j F Y'); ?></p>
        </div>
    </header>

<nav class="navbar navbar-expand-lg navbar-light journal-header mb-4 sticky-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link px-3 active" href="index.php">Actualités</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link px-3 active" href="pages/article.php">Articles</a>
                    </li>
                    
                    <?php foreach ($categories as $cat): ?>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="pages/article.php?category=<?php echo e($cat['id']); ?>">
                                <?php echo e($cat['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>

                    <li class="nav-item">
                        <a class="nav-link px-3 text-danger" href="pages/article.php">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>Rechercher
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger rounded-0 border-0 border-start border-danger border-4">Erreur : <?php echo e($error); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-9 article-border pe-lg-4">
                
                <?php if ($heroArticle): ?>
                <article class="mb-5">
                    <div class="row">
                        <div class="col-md-7">
                            <span class="category-label"><?php echo e($heroArticle['category_name'] ?? 'Événement'); ?></span>
                            <h1 class="hero-title mt-2 mb-3">
                                <a href="lire.php?slug=<?php echo e($heroArticle['slug']); ?>" class="text-dark text-decoration-none">
                                    <?php echo e($heroArticle['title']); ?>
                                </a>
                            </h1>
                            <p class="lead fs-5 text-secondary" style="font-family: 'Merriweather', serif;">
                                <?php echo e(excerptText($heroArticle['summary'] ?? '', 250)); ?>
                            </p>
                        </div>
                        <div class="col-md-5">
                            <a href="lire.php?slug=<?php echo e($heroArticle['slug']); ?>">
                                <img src="<?php echo e($heroArticle['image_url'] ?? $defaultHeroImage); ?>" class="img-fluid w-100 mb-3" alt="Image à la une">
                            </a>
                        </div>
                    </div>
                </article>
                <?php endif; ?>

                <hr class="mb-4">

                <div class="row g-4 mb-4">
                    <?php foreach ($listArticles as $article): ?>
                        <div class="col-md-6">
                            <article>
                                <span class="category-label"><?php echo e($article['category_name'] ?? 'Actualité'); ?></span>
                                <a href="lire.php?slug=<?php echo e($article['slug']); ?>" class="text-dark text-decoration-none">
                                    <h3 class="h4 mt-1 mb-2"><?php echo e($article['title']); ?></h3>
                                </a>
                                <p class="text-muted small mb-2"><?php echo e(excerptText($article['summary'] ?? '', 120)); ?></p>
                                    <img src="<?php echo e($article['image_url'] ?? $defaultHeroImage); ?>" class="img-fluid w-100 mb-3" alt="Image à la une">
                            
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="col-lg-3 ps-lg-4">
                <h4 class="border-bottom border-dark pb-2 mb-4">Le fil info</h4>
                
                <div class="fil-info-container">
                    <?php 
                    if (empty($filInfo)) {
                        echo '<p class="small text-muted fst-italic">Le fil est calme pour le moment.</p>';
                    }
                    foreach ($filInfo as $info): 
                        $time = !empty($info['created_at']) ? date('H:i', strtotime($info['created_at'])) : '';
                    ?>
                        <div class="fil-info-item">
                            <span class="fil-info-time me-2"><?php echo e($time); ?></span>
                            <a href="lire.php?slug=<?php echo e($info['slug']); ?>" class="text-dark text-decoration-none small fw-bold">
                                <?php echo e($info['title']); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="bg-light p-3 mt-4 text-center border">
                    <h5 class="h6 fw-bold">Soutenez notre rédaction</h5>
                    <p class="small mb-3">Abonnez-vous pour un accès illimité à nos enquêtes exclusives.</p>
                    <button class="btn btn-dark btn-sm rounded-0 w-100">S'abonner</button>
                </div>
            </aside>
        </div>
    </main>

    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container text-center">
            <h2 class="logo-text text-white mb-4 fs-3">INFOFLASH</h2>
            <ul class="list-inline small mb-0">
                <li class="list-inline-item"><a href="#" class="text-white text-decoration-none">Mentions légales</a></li>
                <li class="list-inline-item mx-3 text-secondary">|</li>
                <li class="list-inline-item"><a href="#" class="text-white text-decoration-none">Politique de confidentialité</a></li>
                <li class="list-inline-item mx-3 text-secondary">|</li>
                <li class="list-inline-item"><a href="#" class="text-white text-decoration-none">Nous contacter</a></li>
            </ul>
        </div>
    </footer>

</body>
</html>