<?php

session_start();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) { ob_start("ob_gzhandler"); } else { ob_start(); }
include_once '../../backoffice/connect/Connect.php';

function e($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function excerptText($html, $maxLength = 150) {
    $text = trim(strip_tags((string) $html));
    if (mb_strlen($text) <= $maxLength) return $text;
    return mb_substr($text, 0, $maxLength) . '...';
}

$conn = getConnection(); $error = ''; $articles = []; $categories = [];
$search = $_GET['q'] ?? ''; $categoryId = $_GET['category'] ?? '';

try {
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
            WHERE a.status = 'publie' "; 
    $params = []; $types = "";

    if ($search !== '') { $sql .= " AND (a.title LIKE ? OR a.content LIKE ?) "; $params[] = "%$search%"; $params[] = "%$search%"; $types .= "ss"; }
    if ($categoryId !== '' && is_numeric($categoryId)) { $sql .= " AND a.category_id = ? "; $params[] = $categoryId; $types .= "i"; }
    $sql .= " ORDER BY a.created_at DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $articles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    closeConnection($conn);
} catch (Throwable $exception) { $error = $exception->getMessage(); }

$defaultCardImage = 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&w=400&q=60&fm=webp';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives et Articles | InfoFlash</title>
    <meta name="description" content="Parcourez les archives et les dernières actualités sur InfoFlash. Filtrez par catégories et sujets spécifiques.">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Open+Sans:wght@400;600&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Open+Sans:wght@400;600&display=swap" media="print" onload="this.media='all'">
    
    <style>
        body { font-family: 'Open Sans', sans-serif; background-color: #fcfcfc; color: #333; }
        h1, h2, h3, h4, h5 { font-family: 'Merriweather', serif; font-weight: 700; color: #111; }
        .logo-text { font-family: 'Merriweather', serif; font-size: 3.5rem; letter-spacing: -2px; font-weight: 900; color: #000; }
        .journal-header { border-top: 1px solid #ccc; border-bottom: 3px solid #000; background: #fff; }
        .nav-link { color: #333; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
        
        .filter-sidebar { background: #f8f9fa; padding: 25px; border-top: 4px solid #111; position: sticky; top: 100px; }
        .category-badge { color: #b71c1c; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; }
        
        /* Design flat des cartes sans bordures moches */
        .article-card { transition: transform 0.2s; border-bottom: 1px solid #eee; padding-bottom: 20px; height: 100%; display: flex; flex-direction: column; }
        .img-zoom-wrap { overflow: hidden; display: block; }
        .img-zoom-wrap img { transition: transform 0.5s ease; }
        .img-zoom-wrap:hover img { transform: scale(1.04); }
        .title-hover a { background-image: linear-gradient(transparent calc(100% - 2px), #b71c1c 2px); background-repeat: no-repeat; background-size: 0 100%; transition: background-size 0.3s ease; display: inline; }
        .title-hover a:hover { background-size: 100% 100%; color: #000 !important; }
    </style>
</head>
<body>

    <header class="bg-white pt-4 pb-3">
        <div class="container text-center"><a href="../index.php" class="text-decoration-none"><h1 class="logo-text mb-0">INFOFLASH</h1></a></div>
    </header>

    <nav class="navbar navbar-expand-lg navbar-light journal-header sticky-top shadow-sm mb-5">
        <div class="container">
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav gap-3">
                    <li class="nav-item"><a class="nav-link" href="../index.php">ACTUALITÉS</a></li>
                    <li class="nav-item"><a class="nav-link active fw-bold text-danger" href="Article.php">ARCHIVES</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item ms-lg-3 d-flex gap-2 align-items-center">
                            <a href="../../backoffice/logout.php" class="btn btn-sm btn-danger rounded-0" title="Se déconnecter"><i class="fa-solid fa-power-off"></i></a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3">
                            <a href="../../backoffice/login.php" class="btn btn-sm btn-dark rounded-0 fw-bold px-3">Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="row">
            <aside class="col-lg-3 mb-4 pe-lg-4">
                <div class="filter-sidebar shadow-sm">
                    <h2 class="h6 text-uppercase fw-bold mb-4 tracking-widest">Recherche</h2>
                    <form method="GET" action="Article.php">
                        <div class="mb-3">
                            <input type="text" aria-label="Mots-clés" name="q" class="form-control rounded-0 bg-white" placeholder="Mots-clés..." value="<?php echo e($search); ?>">
                        </div>
                        <div class="mb-4">
                            <select aria-label="Catégorie" name="category" class="form-select rounded-0 bg-white">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($categoryId == $cat['id']) ? 'selected' : ''; ?>><?php echo e($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 rounded-0 fw-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Appliquer</button>
                        <?php if ($search || $categoryId): ?><a href="Article.php" class="btn btn-link text-muted text-decoration-none w-100 mt-2 small">Réinitialiser</a><?php endif; ?>
                    </form>
                </div>
            </aside>

            <div class="col-lg-9 border-start ps-lg-4">
                <div class="d-flex align-items-center mb-4 pb-2 border-bottom border-dark border-2">
                    <h2 class="h4 mb-0 fw-bold text-uppercase">Tous nos articles</h2>
                </div>

                <?php if (!empty($error)): ?><div class="alert alert-danger rounded-0"><?php echo e($error); ?></div><?php endif; ?>

                <?php if (empty($articles)): ?>
                    <div class="alert bg-light border-0 rounded-0 text-center py-5"><p class="lead text-muted mb-0">Aucun résultat trouvé.</p></div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($articles as $article): 
                            $cardImage = !empty($article['image_url']) ? $article['image_url'] : $defaultCardImage;
                            $altText = !empty($article['alt_text']) ? $article['alt_text'] : strip_tags($article['title']);
                        ?>
                            <div class="col-md-6 mb-3">
                                <div class="article-card">
                                    <a href="../lire.php?slug=<?php echo e($article['slug']); ?>" class="img-zoom-wrap mb-3" aria-label="<?php echo e(strip_tags($article['title'])); ?>">
                                        <img src="<?php echo e($cardImage); ?>" loading="lazy" width="400" height="225" class="w-100 shadow-sm" style="height:225px; object-fit:cover;" alt="<?php echo e($altText); ?>">
                                    </a>
                                    <div class="category-badge mb-2"><?php echo e($article['category_name'] ?? 'Édition'); ?></div>
                                    <h3 class="h4 title-hover lh-base mb-2">
                                        <a href="../lire.php?slug=<?php echo e($article['slug']); ?>" class="text-dark text-decoration-none"><?php echo e(strip_tags($article['title'])); ?></a>
                                    </h3>
                                    <p class="text-muted small lh-lg flex-grow-1"><?php echo e(excerptText($article['summary'] ?? '', 120)); ?></p>
                                    <small class="text-muted fst-italic mt-2">Le <?php echo date('d/m/Y', strtotime($article['created_at'])); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-4 mt-5"><div class="container text-center"><p class="mb-0 small text-secondary">&copy; <?php echo date("Y"); ?> InfoFlash.</p></div></footer>
    <?php ob_end_flush(); ?>
</body>
</html>