<?php
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) { ob_start("ob_gzhandler"); } else { ob_start(); }
include_once '../backoffice/connect/Connect.php';

function e($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function excerptText($html, $maxLength = 150) {
    $text = trim(strip_tags((string) $html)); if (mb_strlen($text) <= $maxLength) return $text; return mb_substr($text, 0, $maxLength) . '...';
}

$slug = $_GET['slug'] ?? ''; $article = null; $error = ''; $recentArticles = [];

if (!empty($slug)) {
    try {
        $conn = getConnection();
        $sql = "SELECT a.id, a.title, a.slug, a.summary, a.content, a.created_at, c.name AS category_name, u.username AS author_name,
                s.name AS source_name, s.url AS source_url, ai.image_url AS image_url, ai.alt_text AS alt_text
                FROM article a LEFT JOIN category_article c ON a.category_id = c.id LEFT JOIN user u ON a.user_id = u.id
                LEFT JOIN source s ON a.source_id = s.id
                LEFT JOIN article_image ai ON ai.id = (
                    SELECT ai2.id
                    FROM article_image ai2
                    WHERE ai2.article_id = a.id
                    ORDER BY ai2.is_main DESC, ai2.id ASC
                    LIMIT 1
                )
                WHERE a.slug = ? AND a.status = 'publie'";
        $stmt = $conn->prepare($sql); $stmt->bind_param("s", $slug); $stmt->execute(); $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $article = $result->fetch_assoc();
            $stmtRecent = $conn->prepare("SELECT title, slug, created_at FROM article WHERE status = 'publie' AND id != ? ORDER BY created_at DESC LIMIT 3");
            $stmtRecent->bind_param("i", $article['id']); $stmtRecent->execute();
            $recentArticles = $stmtRecent->get_result()->fetch_all(MYSQLI_ASSOC);
        } else { $error = "Cet article n'est plus disponible."; }
        closeConnection($conn);
    } catch (Throwable $exception) { $error = "Erreur de chargement."; }
}

$defaultImage = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1200&q=60&fm=webp';
$cleanPageTitle = $article ? strip_tags($article['title']) : 'Article';
$metaDescription = $article ? excerptText($article['summary'] ?? $article['content'], 155) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($cleanPageTitle); ?> | InfoFlash</title>
    <meta name="description" content="<?php echo e($metaDescription); ?>">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php if ($article): ?><link rel="preload" as="image" href="<?php echo e($article['image_url'] ?? $defaultImage); ?>" fetchpriority="high"><?php endif; ?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Open+Sans:wght@400;600&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Open+Sans:wght@400;600&display=swap" media="print" onload="this.media='all'">

    <style>
        body { font-family: 'Open Sans', sans-serif; background-color: #fcfcfc; color: #111; }
        h1, h2, h3, h4, .logo-text { font-family: 'Merriweather', serif; font-weight: 700; color: #000; }
        .logo-text { font-size: 3.5rem; letter-spacing: -2px; }
        .journal-header { border-top: 1px solid #ccc; border-bottom: 3px solid #000; background: #fff; }
        .nav-link { color: #333; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
        
        .category-label { color: #b71c1c; font-size: 0.85rem; text-transform: uppercase; font-weight: 700; letter-spacing: 2px; }
        .article-title { font-size: 3.5rem; line-height: 1.1; letter-spacing: -1px; margin-top: 15px; }
        .article-summary { font-family: 'Merriweather', serif; font-size: 1.3rem; font-weight: 300; color: #333; line-height: 1.6; border-left: 5px solid #111; padding-left: 20px; font-style: italic; }
        .article-meta { font-size: 0.85rem; color: #555; border-top: 2px solid #111; border-bottom: 1px solid #ddd; padding: 12px 0; text-transform: uppercase; letter-spacing: 1px; }
        
        .article-content { font-family: 'Merriweather', serif; font-size: 1.15rem; line-height: 1.9; color: #222; }
        .article-content h2 { font-size: 2rem; margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .article-content img { max-width: 100%; height: auto; display: block; margin: 3rem auto; border: 1px solid #eee; padding: 5px; }

        .article-content > p:first-of-type::first-letter {
            float: left; font-size: 5rem; line-height: 0.8; padding-top: 4px; padding-right: 8px; padding-left: 3px;
            font-family: 'Merriweather', serif; font-weight: 900; color: #111;
        }

        .sidebar-title { border-bottom: 3px solid #000; padding-bottom: 5px; margin-bottom: 25px; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px; font-family: 'Open Sans', sans-serif;}
        .recent-link { color: #111; text-decoration: none; font-weight: 700; font-family: 'Merriweather', serif; font-size: 1.1rem; transition: color 0.2s; }
        .recent-link:hover { color: #b71c1c; }
        
        @media (max-width: 768px) { .article-title { font-size: 2.5rem; } }
    </style>
</head>
<body>

    <header class="bg-white pt-4 pb-3">
        <div class="container text-center"><a href="index.php" class="text-decoration-none"><h1 class="logo-text mb-0">INFOFLASH</h1></a></div>
    </header>

    <nav class="navbar navbar-expand-lg navbar-light journal-header mb-5 sticky-top shadow-sm">
        <div class="container">
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav gap-3">
                    <li class="nav-item"><a class="nav-link" href="index.php">ACTUALITÉS</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages/Article.php">ARCHIVES</a></li>
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

    <main class="container mb-5">
        <?php if ($article): 
            $imageUrl = $article['image_url'] ?? $defaultImage;
            $altText = $article['alt_text'] ?? strip_tags($article['title']);
        ?>
            <div class="row justify-content-center">
                <div class="col-lg-8 pe-lg-5">
                    
                    <header class="mb-5 text-center">
                        <span class="category-label"><?php echo e($article['category_name'] ?? 'Édition'); ?></span>
                        <h1 class="article-title fw-bold mb-4 mx-auto" style="max-width: 90%;"><?php echo e(strip_tags($article['title'])); ?></h1>
                        
                        <?php if (!empty($article['summary'])): ?>
                            <div class="article-summary mb-4 text-start mx-auto" style="max-width: 95%;">
                                <?php echo nl2br(e(strip_tags($article['summary']))); ?>
                            </div>
                        <?php endif; ?>

                        <div class="article-meta d-flex justify-content-center gap-4">
                            <span>Par <strong><?php echo e($article['author_name'] ?? 'La Rédaction'); ?></strong></span>
                            <span>Publié le <strong><?php echo date('d/m/Y', strtotime($article['created_at'])); ?></strong></span>
                        </div>
                    </header>

                    <figure class="mb-5">
                        <img src="<?php echo e($imageUrl); ?>" width="1200" height="800" fetchpriority="high" class="img-fluid w-100 shadow-sm" style="object-fit:cover; max-height: 550px;" alt="<?php echo e($altText); ?>">
                        <figcaption class="text-end text-muted small mt-2 fst-italic">Illustration : <?php echo e($altText); ?></figcaption>
                    </figure>

                    <div class="article-content">
                        <?php echo $article['content']; ?>
                    </div>
                </div>

                <aside class="col-lg-3 mt-5 mt-lg-0 border-start ps-lg-4">
                    <div class="position-sticky" style="top: 100px;">
                        <h2 class="sidebar-title">À lire aussi</h2>
                        <div class="d-flex flex-column gap-4">
                            <?php foreach ($recentArticles as $recent): ?>
                                <article>
                                    <span class="text-danger small fw-bold text-uppercase" style="letter-spacing:1px;"><?php echo date('d/m/Y', strtotime($recent['created_at'])); ?></span><br>
                                    <a href="lire.php?slug=<?php echo e($recent['slug']); ?>" class="recent-link lh-sm d-inline-block mt-1">
                                        <?php echo e(strip_tags($recent['title'])); ?>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white py-4 mt-5"><div class="container text-center"><p class="mb-0 small text-secondary">&copy; <?php echo date("Y"); ?> InfoFlash.</p></div></footer>
    <?php ob_end_flush(); ?>
</body>
</html>