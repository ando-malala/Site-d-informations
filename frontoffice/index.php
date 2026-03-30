<?php
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) { ob_start("ob_gzhandler"); } else { ob_start(); }
include_once '../backoffice/connect/Connect.php';

function e($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function excerptText($html, $maxLength = 180) {
    $text = trim(strip_tags((string) $html));
    if (mb_strlen($text) <= $maxLength) return $text;
    return mb_substr($text, 0, $maxLength) . '...';
}

$articles = []; $error = ''; $categories = [];

try {
    $conn = getConnection();
    $categories = $conn->query("SELECT id, name FROM category_article ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
    $sql = "SELECT a.id, a.title, a.slug, a.summary, a.created_at, c.name AS category_name,
            ai.image_url AS image_url, ai.alt_text AS alt_text
            FROM article a LEFT JOIN category_article c ON c.id = a.category_id
            LEFT JOIN article_image ai ON ai.id = (
                SELECT ai2.id
                FROM article_image ai2
                WHERE ai2.article_id = a.id
                ORDER BY ai2.is_main DESC, ai2.id ASC
                LIMIT 1
            )
            WHERE a.status = 'publie' ORDER BY a.created_at DESC LIMIT 10";
    $articles = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    closeConnection($conn);
} catch (Throwable $exception) { $error = $exception->getMessage(); }

$defaultHeroImage = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1200&q=60&fm=webp';
$defaultCardImage = 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&w=400&q=60&fm=webp';

$heroArticle = $articles[0] ?? null;
$listArticles = array_slice($articles, 1, 4); 
$filInfo = array_slice($articles, 5); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfoFlash | Actualités et informations en continu</title>
    <meta name="description" content="Suivez l'actualité en continu, les opérations militaires, la diplomatie et l'économie avec InfoFlash. Décryptages et informations vérifiées.">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <?php if ($heroArticle): ?>
        <link rel="preload" as="image" href="<?php echo e($heroArticle['image_url'] ?? $defaultHeroImage); ?>" fetchpriority="high">
    <?php endif; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Open+Sans:wght@400;600&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Open+Sans:wght@400;600&display=swap" media="print" onload="this.media='all'">
    <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    
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
            <p class="text-muted small fst-italic mb-2 border-top border-bottom py-1 d-inline-block mt-2">Édition en continu &mdash; <?php echo date('l j F Y'); ?></p>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg navbar-light journal-header mb-4 sticky-top shadow-sm">
        <div class="container">
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Menu principal">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav gap-3">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Actualités</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages/Article.php">Articles</a></li>
                    <?php foreach ($categories as $cat): ?>
                        <li class="nav-item"><a class="nav-link" href="pages/Article.php?category=<?php echo e($cat['id']); ?>"><?php echo e($cat['name']); ?></a></li>
                    <?php endforeach; ?>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item ms-lg-3 d-flex gap-2 align-items-center">
                            <a href="../backoffice/logout.php" class="btn btn-sm btn-danger rounded-0" title="Se déconnecter"><i class="fa-solid fa-power-off"></i></a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3">
                            <a href="../backoffice/login.php" class="btn btn-sm btn-dark rounded-0 fw-bold px-3">Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        <?php if (!empty($error)): ?><div class="alert alert-danger rounded-0 border-0 border-start border-danger border-4"><?php echo e($error); ?></div><?php endif; ?>

        <div class="row">
            <div class="col-lg-9 article-border pe-lg-5">
                <?php if ($heroArticle): $cleanTitle = strip_tags($heroArticle['title']); ?>
                <article class="mb-5 pb-4 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6 pe-md-4">
                            <span class="category-label"><?php echo e($heroArticle['category_name'] ?? 'Événement'); ?></span>
                            <h2 class="display-5 fw-bold mt-2 mb-3 title-hover lh-sm">
                                <a href="lire.php?slug=<?php echo e($heroArticle['slug']); ?>" class="text-dark text-decoration-none"><?php echo e($cleanTitle); ?></a>
                            </h2>
                            <p class="lead text-secondary" style="font-family: 'Merriweather', serif; font-size: 1.15rem; line-height: 1.6;">
                                <?php echo e(excerptText($heroArticle['summary'] ?? '', 200)); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <a href="lire.php?slug=<?php echo e($heroArticle['slug']); ?>" class="img-zoom-wrap" aria-label="Lire l'article : <?php echo e($cleanTitle); ?>">
                                <img src="<?php echo e($heroArticle['image_url'] ?? $defaultHeroImage); ?>" width="800" height="533" fetchpriority="high" class="img-fluid w-100 shadow-sm" style="object-fit: cover;" alt="<?php echo e($heroArticle['alt_text'] ?? 'Illustration'); ?>">
                            </a>
                        </div>
                    </div>
                </article>
                <?php endif; ?>

                <div class="row g-5 mb-4">
                    <?php foreach ($listArticles as $article): $cleanTitle = strip_tags($article['title']); ?>
                        <div class="col-md-6">
                            <article>
                                <a href="lire.php?slug=<?php echo e($article['slug']); ?>" class="img-zoom-wrap mb-3" aria-label="<?php echo e($cleanTitle); ?>">
                                    <img src="<?php echo e($article['image_url'] ?? $defaultCardImage); ?>" width="400" height="266" loading="lazy" class="img-fluid w-100 shadow-sm" style="height: 220px; object-fit: cover;" alt="<?php echo e($article['alt_text'] ?? 'Illustration'); ?>">
                                </a>
                                <span class="category-label"><?php echo e($article['category_name'] ?? 'Actualité'); ?></span>
                                <h3 class="h4 mt-2 mb-2 title-hover lh-base">
                                    <a href="lire.php?slug=<?php echo e($article['slug']); ?>" class="text-dark text-decoration-none"><?php echo e($cleanTitle); ?></a>
                                </h3>
                                <p class="text-muted small mb-0 lh-lg"><?php echo e(excerptText($article['summary'] ?? '', 130)); ?></p>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="col-lg-3 ps-lg-4">
                <div class="d-flex align-items-center mb-4 pb-2 border-bottom border-dark border-2">
                    <div class="spinner-grow spinner-grow-sm text-danger me-2" role="status"><span class="visually-hidden">En direct</span></div>
                    <h2 class="h5 mb-0 fw-bold text-uppercase tracking-widest">Le fil info</h2>
                </div>
                
                <div class="fil-info-container">
                    <?php foreach ($filInfo as $info): 
                        $time = !empty($info['created_at']) ? date('H:i', strtotime($info['created_at'])) : '';
                        $cleanTitle = strip_tags($info['title']);
                    ?>
                        <div class="fil-info-item">
                            <span class="fil-info-time"><?php echo e($time); ?></span>
                            <a href="lire.php?slug=<?php echo e($info['slug']); ?>" class="text-dark text-decoration-none fw-bold" style="font-size: 0.95rem; line-height: 1.4;">
                                <?php echo e($cleanTitle); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>
        </div>
    </main>

    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container text-center">
            <h2 class="logo-text text-white mb-4 fs-3">INFOFLASH</h2>
            <p class="text-secondary small">&copy; <?php echo date("Y"); ?> InfoFlash. Tous droits réservés.</p>
        </div>
    </footer>
    <?php ob_end_flush(); ?>
</body>
</html>