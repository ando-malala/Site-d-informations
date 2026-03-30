<?php
include_once '../connect/Connect.php';

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function excerptText($html, $maxLength = 180) {
    $text = trim(strip_tags((string) $html));
    if (mb_strlen($text) <= $maxLength) {
        return $text;
    }
    return mb_substr($text, 0, $maxLength) . '...';
}

function renderHtml($html) {
    return html_entity_decode((string) $html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function plainText($value) {
    return trim(strip_tags((string) $value));
}

$articles = [];
$error = '';

try {
    $conn = getConnection();
    $sql = "SELECT
                a.id,
                a.title,
                a.slug,
                a.summary,
                a.content,
                a.status,
                a.created_at,
                c.name AS category_name,
                COALESCE(ai_main.image_url, ai_any.image_url) AS image_url
            FROM article a
            LEFT JOIN category_article c ON c.id = a.category_id
            LEFT JOIN article_image ai_main ON ai_main.article_id = a.id AND ai_main.is_main = 1
            LEFT JOIN article_image ai_any ON ai_any.article_id = a.id
            GROUP BY a.id
            ORDER BY a.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $articles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    closeConnection($conn);
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

$defaultHeroImage = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=800&q=80';
$defaultCardImage = 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&w=400&q=80';

$heroArticle = $articles[0] ?? null;
$listArticles = array_slice($articles, 1, 4);
$filInfo = array_slice($articles, 5);

if (empty($listArticles) && $heroArticle !== null) {
    $listArticles = [$heroArticle];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Backoffice InfoFlash : gestion et aperçu des derniers articles publiés.">
    <meta name="keywords" content="backoffice, articles, actualités, CMS">
    <title>Backoffice | InfoFlash</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Open+Sans:wght@400;600&display=swap">
    <style>
        body { font-family: 'Open Sans', sans-serif; background-color: #fcfcfc; color: #111; }
        h1, h2, h3, h4, h5, .logo-text { font-family: 'Merriweather', serif; font-weight: 700; color: #000; }
        .logo-text { font-size: 3.5rem; letter-spacing: -2px; }
        .journal-header { border-top: 1px solid #ccc; border-bottom: 3px solid #000; background: #fff; }
        .nav-link { color: #333; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; transition: color 0.2s; }
        .nav-link:hover, .nav-link.active { color: #b71c1c; }
        .article-border { border-right: 1px solid #e0e0e0; }
        .category-label { color: #b71c1c; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; }
        .img-zoom-wrap { overflow: hidden; display: block; }
        .img-zoom-wrap img { transition: transform 0.5s ease; }
        .img-zoom-wrap:hover img { transform: scale(1.04); }
        .title-hover a { background-image: linear-gradient(transparent calc(100% - 2px), #b71c1c 2px); background-repeat: no-repeat; background-size: 0 100%; transition: background-size 0.3s ease; display: inline; }
        .title-hover a:hover { background-size: 100% 100%; color: #000 !important; }
        .fil-info-item { position: relative; padding-left: 18px; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px; }
        .fil-info-item::before { content: ''; position: absolute; left: 0; top: 6px; width: 8px; height: 8px; background-color: #b71c1c; border-radius: 50%; }
        .fil-info-time { color: #b71c1c; font-weight: 600; font-size: 0.8rem; display: block; margin-bottom: 3px; }
        @media (max-width: 991px) { .article-border { border-right: none; } }
    </style>
</head>
<body>

    <header class="bg-white pt-4 pb-3">
        <div class="container text-center">
            <h1 class="logo-text mb-0">INFOFLASH</h1>
            <p class="text-muted small fst-italic mb-2 border-top border-bottom py-1 d-inline-block mt-2">Backoffice &mdash; <?php echo date('l j F Y'); ?></p>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg navbar-light journal-header mb-4 sticky-top shadow-sm">
        <div class="container">
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Menu backoffice">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav gap-3">
                    <li class="nav-item"><a class="nav-link active" href="index">Actualités</a></li>
                    <li class="nav-item"><a class="nav-link" href="article">Articles</a></li>
                    <li class="nav-item"><a class="nav-link" href="article-image">Images</a></li>
                    <li class="nav-item"><a class="nav-link" href="category-article">Catégories</a></li>
                    <li class="nav-item"><a class="nav-link" href="source">Sources</a></li>
                    <li class="nav-item"><a class="nav-link" href="type-source">Types</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="../../frontoffice/" class="btn btn-sm btn-dark rounded-0 fw-bold px-3">Frontoffice</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        <?php if (!empty($error)): ?><div class="alert alert-danger rounded-0 border-0 border-start border-danger border-4"><?php echo e($error); ?></div><?php endif; ?>

        <div class="row">
            <div class="col-lg-9 article-border pe-lg-5">
                <?php if ($heroArticle): $cleanTitle = plainText($heroArticle['title'] ?? 'Article'); ?>
                <article class="mb-5 pb-4 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6 pe-md-4">
                            <span class="category-label"><?php echo e($heroArticle['category_name'] ?? 'Événement'); ?></span>
                            <h2 class="display-5 fw-bold mt-2 mb-3 title-hover lh-sm">
                                <a href="article?edit=<?php echo e($heroArticle['id']); ?>" class="text-dark text-decoration-none"><?php echo e($cleanTitle); ?></a>
                            </h2>
                            <p class="lead text-secondary" style="font-family: 'Merriweather', serif; font-size: 1.15rem; line-height: 1.6;">
                                <?php echo e(excerptText($heroArticle['summary'] ?? $heroArticle['content'] ?? '', 200)); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <a href="article?edit=<?php echo e($heroArticle['id']); ?>" class="img-zoom-wrap" aria-label="Modifier l'article : <?php echo e($cleanTitle); ?>">
                                <img src="<?php echo e($heroArticle['image_url'] ?? $defaultHeroImage); ?>" class="img-fluid w-100 shadow-sm" style="object-fit: cover;" alt="<?php echo e($cleanTitle); ?>">
                            </a>
                        </div>
                    </div>
                </article>
                <?php endif; ?>

                <div class="row g-5 mb-4">
                    <?php foreach ($listArticles as $article): $cleanTitle = plainText($article['title'] ?? 'Article'); ?>
                        <div class="col-md-6">
                            <article>
                                <a href="article?edit=<?php echo e($article['id']); ?>" class="img-zoom-wrap mb-3" aria-label="Modifier : <?php echo e($cleanTitle); ?>">
                                    <img src="<?php echo e($article['image_url'] ?? $defaultCardImage); ?>" loading="lazy" class="img-fluid w-100 shadow-sm" style="height: 220px; object-fit: cover;" alt="<?php echo e($cleanTitle); ?>">
                                </a>
                                <span class="category-label"><?php echo e($article['category_name'] ?? 'Actualité'); ?></span>
                                <h3 class="h4 mt-2 mb-2 title-hover lh-base">
                                    <a href="article?edit=<?php echo e($article['id']); ?>" class="text-dark text-decoration-none"><?php echo e($cleanTitle); ?></a>
                                </h3>
                                <p class="text-muted small mb-0 lh-lg"><?php echo e(excerptText($article['summary'] ?? $article['content'] ?? '', 130)); ?></p>
                            </article>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($listArticles)): ?>
                        <div class="col-12"><div class="alert alert-info mb-0">Aucun article à afficher pour le moment.</div></div>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="col-lg-3 ps-lg-4">
                <div class="d-flex align-items-center mb-4 pb-2 border-bottom border-dark border-2">
                    <div class="spinner-grow spinner-grow-sm text-danger me-2" role="status"><span class="visually-hidden">En direct</span></div>
                    <h2 class="h5 mb-0 fw-bold text-uppercase">Le fil backoffice</h2>
                </div>

                <?php foreach ($filInfo as $info): $time = !empty($info['created_at']) ? date('H:i', strtotime($info['created_at'])) : ''; ?>
                    <div class="fil-info-item">
                        <span class="fil-info-time"><?php echo e($time); ?></span>
                        <a href="article?edit=<?php echo e($info['id']); ?>" class="text-dark text-decoration-none fw-bold" style="font-size: 0.95rem; line-height: 1.4;">
                            <?php echo e(plainText($info['title'] ?? 'Article')); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </aside>
        </div>
    </main>

    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container text-center">
            <h2 class="logo-text text-white mb-4 fs-3">INFOFLASH</h2>
            <p class="text-secondary small mb-0">&copy; <?php echo date("Y"); ?> Backoffice InfoFlash. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>