<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfoFlash | Votre source d'actualités</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand { font-weight: bold; color: #dc3545 !important; }
        .hero-section { background: #fff; padding: 40px 0; border-bottom: 1px solid #ddd; }
        .card { border: none; transition: transform 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card:hover { transform: translateY(-5px); }
        .category-badge { font-size: 0.8rem; text-transform: uppercase; font-weight: bold; }
        .footer { background: #212529; color: white; padding: 50px 0; margin-top: 50px; }
    </style>
</head>
<body>

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fa-solid fa-newspaper me-2"></i>INFOFLASH</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Politique</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Technologie</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Sport</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Culture</a></li>
                </ul>
                <a href="../../frontend/index.php" class="btn btn-outline-light">Frontend</a>
            </div>
        </div>
    </nav>

    <!-- Section À la une (Hero) -->
    <header class="hero-section mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=800&q=80" class="img-fluid rounded mb-3 mb-lg-0" alt="News">
                </div>
                <div class="col-lg-4">
                    <span class="badge bg-danger mb-2 category-badge">Dernière minute</span>
                    <h1 class="display-5 fw-bold">Titre de l'information majeure du jour</h1>
                    <p class="lead text-muted">Voici un résumé accrocheur de l'actualité principale. Ce texte est conçu pour donner envie aux lecteurs d'en savoir plus sur cet événement important.</p>
                    <a href="#" class="btn btn-primary btn-lg">Lire la suite</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="row">
            <!-- Colonne Principale : Liste d'articles -->
            <div class="col-lg-8">
                <h3 class="mb-4 border-bottom pb-2">Dernières Actualités</h3>
                
                <div class="row g-4">
                    <?php
                    // Simulation de données provenant d'une base de données
                    $articles = [
                        [
                            "titre" => "L'IA révolutionne le monde du travail",
                            "image" => "https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&w=400&q=80",
                            "cat" => "Techno",
                            "date" => "Il y a 2 heures"
                        ],
                        [
                            "titre" => "Exploration spatiale : une nouvelle étape franchie",
                            "image" => "https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=400&q=80",
                            "cat" => "Science",
                            "date" => "Il y a 5 heures"
                        ],
                        [
                            "titre" => "Économie mondiale : les prévisions pour 2024",
                            "image" => "https://images.unsplash.com/photo-1611974717483-58285a810e0e?auto=format&fit=crop&w=400&q=80",
                            "cat" => "Économie",
                            "date" => "Hier"
                        ],
                        [
                            "titre" => "L'équipe nationale remporte la finale",
                            "image" => "https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=400&q=80",
                            "cat" => "Sport",
                            "date" => "Hier"
                        ]
                    ];

                    foreach ($articles as $article) {
                        echo '
                        <div class="col-md-6">
                            <div class="card h-100">
                                <img src="'.$article['image'].'" class="card-img-top" alt="...">
                                <div class="card-body">
                                    <span class="text-primary fw-bold category-badge">'.$article['cat'].'</span>
                                    <h5 class="card-title mt-2">'.$article['titre'].'</h5>
                                    <p class="card-text text-muted">Petit extrait du contenu de l\'article pour donner un aperçu au lecteur...</p>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> '.$article['date'].'</small>
                                </div>
                            </div>
                        </div>';
                    }
                    ?>
                </div>
            </div>

            <!-- Sidebar (Colonne de droite) -->
            <aside class="col-lg-4">
                <div class="bg-white p-4 shadow-sm rounded mb-4">
                    <h4 class="mb-3">Newsletter</h4>
                    <p class="small text-muted">Recevez l'essentiel de l'actu chaque matin dans votre boîte mail.</p>
                    <form>
                        <input type="email" class="form-control mb-2" placeholder="votre@email.com">
                        <button class="btn btn-danger w-100" type="button">S'abonner</button>
                    </form>
                </div>

                <div class="bg-white p-4 shadow-sm rounded">
                    <h4 class="mb-3">Populaire</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge bg-dark me-3">1</span>
                            <a href="#" class="text-decoration-none text-dark small fw-bold">Les 10 destinations à visiter absolument cet été</a>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge bg-dark me-3">2</span>
                            <a href="#" class="text-decoration-none text-dark small fw-bold">Comment sécuriser ses données en ligne ?</a>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge bg-dark me-3">3</span>
                            <a href="#" class="text-decoration-none text-dark small fw-bold">Nouvelle loi sur le télétravail : ce qu'il faut savoir</a>
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </main>

    <!-- Pied de page -->
    <footer class="footer">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>À propos</h5>
                    <p class="small text-secondary">InfoFlash est un site de démonstration pour un projet web d'actualités en temps réel.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Liens utiles</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-secondary text-decoration-none">Mentions légales</a></li>
                        <li><a href="#" class="text-secondary text-decoration-none">Contact</a></li>
                        <li><a href="#" class="text-secondary text-decoration-none">Publicité</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Suivez-nous</h5>
                    <a href="#" class="text-white me-3"><i class="fa-brands fa-facebook fa-xl"></i></a>
                    <a href="#" class="text-white me-3"><i class="fa-brands fa-twitter fa-xl"></i></a>
                    <a href="#" class="text-white"><i class="fa-brands fa-instagram fa-xl"></i></a>
                </div>
            </div>
            <hr class="mt-4 bg-secondary">
            <p class="mb-0 small text-secondary">&copy; <?php echo date("Y"); ?> InfoFlash - Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>