<?php
include_once '../backoffice/connect/Connect.php';

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$slug = $_GET['slug'] ?? '';
$article = null;
$error = '';
$recentArticles = [];

if (empty($slug)) {
    $error = "Aucun article spécifié.";
} else {
    try {
        $conn = getConnection();
        
        // 1. Récupérer l'article spécifique, son auteur, sa catégorie et sa source
        $sql = "SELECT 
                    a.id, a.title, a.slug, a.summary, a.content, a.created_at,
                    c.name AS category_name,
                    u.username AS author_name,
                    s.name AS source_name, s.url AS source_url,
                    COALESCE(ai_main.image_url, ai_any.image_url) AS image_url,
                    COALESCE(ai_main.alt_text, ai_any.alt_text) AS alt_text
                FROM article a
                LEFT JOIN category_article c ON a.category_id = c.id
                LEFT JOIN user u ON a.user_id = u.id
                LEFT JOIN source s ON a.source_id = s.id
                LEFT JOIN article_image ai_main ON ai_main.article_id = a.id AND ai_main.is_main = 1
                LEFT JOIN article_image ai_any ON ai_any.article_id = a.id
                WHERE a.slug = ? AND a.status = 'publie'
                GROUP BY a.id";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $article = $result->fetch_assoc();
            
            // 2. Récupérer 3 articles récents pour la sidebar (différents de l'article actuel)
            $sqlRecent = "SELECT title, slug, created_at FROM article 
                          WHERE status = 'publie' AND id != ? 
                          ORDER BY created_at DESC LIMIT 3";
            $stmtRecent = $conn->prepare($sqlRecent);
            $stmtRecent->bind_param("i", $article['id']);
            $stmtRecent->execute();
            $recentArticles = $stmtRecent->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $error = "Cet article n'existe pas ou n'est plus disponible.";
        }
        
        closeConnection($conn);
    } catch (Throwable $exception) {
        $error = "Une erreur est survenue lors du chargement de l'article.";
    }
}

$defaultImage = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1200&q=80';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article ? e($article['title']) . ' | InfoFlash' : 'Article introuvable'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Open Sans', sans-serif; background-color: #fcfcfc; color: #111; }
        h1, h2, h3, h4, h5, .logo-text { font-family: 'Merriweather', serif; font-weight: 700; color: #000; }
        .journal-header { border-top: 1px solid #ccc; border-bottom: 2px solid #000; background: #fff; }
        .category-label { color: #b71c1c; font-size: 0.85rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; }
        
        /* Styles spécifiques à l'article */
        .article-title { font-size: 3rem; line-height: 1.2; letter-spacing: -1px; }
        .article-summary { font-family: 'Merriweather', serif; font-size: 1.25rem; font-weight: 400; color: #444; line-height: 1.6; border-left: 4px solid #b71c1c; padding-left: 15px; }
        .article-meta { font-size: 0.9rem; color: #666; border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 10px 0; }
        
        /* Contenu de l'article (généré par TinyMCE) */
        .article-content { font-family: 'Merriweather', serif; font-size: 1.1rem; line-height: 1.8; color: #222; }
        .article-content p { margin-bottom: 1.5rem; }
        .article-content h2 { font-size: 1.8rem; margin-top: 2rem; margin-bottom: 1rem; }
        .article-content img { max-width: 100%; height: auto; display: block; margin: 2rem auto; }
        
        .sidebar-title { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; font-size: 1.2rem; text-transform: uppercase; }
        .recent-link { color: #111; text-decoration: none; font-weight: 600; font-family: 'Merriweather', serif; }
        .recent-link:hover { color: #b71c1c; }
    </style>
</head>
<body>

    <header class="bg-white py-3">
        <div class="container text-center">
            <a href="index.php" class="text-decoration-none text-dark"><div class="logo-text fs-1">INFOFLASH</div></a>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg navbar-light journal-header mb-5 sticky-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link px-3 fw-bold" href="index.php">ACTUALITÉS</a></li>
                    <li class="nav-item"><a class="nav-link px-3 fw-bold" href="article.php">TOUS LES ARTICLES</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mb-5">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center py-5">
                <i class="fa-solid fa-triangle-exclamation fa-3x mb-3"></i>
                <h2>Oups !</h2>
                <p class="lead mb-0"><?php echo e($error); ?></p>
                <a href="index.php" class="btn btn-dark mt-4">Retour à l'accueil</a>
            </div>
        <?php elseif ($article): 
            $imageUrl = $article['image_url'] ?? $defaultImage;
            $altText = $article['alt_text'] ?? $article['title'];
            $date = date('d/m/Y à H\hi', strtotime($article['created_at']));
            $author = $article['author_name'] ?? 'La Rédaction';
            $category = $article['category_name'] ?? 'Édition';
        ?>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <header class="mb-4">
                        <a href="article.php?category=<?php echo $article['category_id'] ?? ''; ?>" class="text-decoration-none">
                            <span class="category-label"><?php echo e($category); ?></span>
                        </a>
                        <h1 class="article-title mt-2 mb-4"><?php echo e($article['title']); ?></h1>
                        
                        <?php if (!empty($article['summary'])): ?>
                            <div class="article-summary mb-4">
                                <?php echo nl2br(e($article['summary'])); ?>
                            </div>
                        <?php endif; ?>

                        <div class="article-meta d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Par <?php echo e($author); ?></strong><br>
                                <span class="small">Publié le <?php echo e($date); ?></span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-dark rounded-circle me-1"><i class="fa-brands fa-x-twitter"></i></button>
                                <button class="btn btn-sm btn-outline-dark rounded-circle me-1"><i class="fa-brands fa-facebook-f"></i></button>
                                <button class="btn btn-sm btn-outline-dark rounded-circle"><i class="fa-solid fa-link"></i></button>
                            </div>
                        </div>
                    </header>

                    <figure class="mb-5">
                        <img src="<?php echo e($imageUrl); ?>" class="img-fluid w-100" alt="<?php echo e($altText); ?>">
                        <figcaption class="text-muted small mt-2 fst-italic border-bottom pb-2">Illustration : <?php echo e($altText); ?></figcaption>
                    </figure>

                    <div class="article-content">
                        <?php echo $article['content']; ?>
                    </div>

                    <?php if (!empty($article['source_name'])): ?>
                        <div class="mt-5 p-3 bg-light border-start border-4 border-secondary">
                            <span class="fw-bold">Source :</span> 
                            <?php if (!empty($article['source_url'])): ?>
                                <a href="<?php echo e($article['source_url']); ?>" target="_blank" rel="noopener noreferrer" class="text-dark"><?php echo e($article['source_name']); ?></a>
                            <?php else: ?>
                                <?php echo e($article['source_name']); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                </div>

                <aside class="col-lg-3 offset-lg-1 mt-5 mt-lg-0">
                    <div class="position-sticky" style="top: 100px;">
                        <h4 class="sidebar-title">À lire aussi</h4>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($recentArticles as $recent): ?>
                                <article>
                                    <span class="text-danger small fw-bold"><?php echo date('d/m/Y', strtotime($recent['created_at'])); ?></span><br>
                                    <a href="lire.php?slug=<?php echo e($recent['slug']); ?>" class="recent-link lh-sm d-inline-block mt-1">
                                        <?php echo e($recent['title']); ?>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        </div>

            
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0 small">&copy; <?php echo date("Y"); ?> InfoFlash - Site de démonstration éditoriale.</p>
        </div>
    </footer>

</body>
</html>