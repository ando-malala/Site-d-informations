<?php
include_once '../controllers/CategoryArticle.php';

function e($value) {
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$isEdit = $editItem !== null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="CRUD des catégories d'articles du backoffice InfoFlash.">
	<meta name="keywords" content="CRUD, catégorie, article, backoffice">
	<title>Backoffice Catégories | InfoFlash</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Open+Sans:wght@400;600&display=swap">
	<style>
		body { font-family: 'Open Sans', sans-serif; background-color: #fcfcfc; color: #111; }
		h1, h2, h3, h4, h5, .logo-text { font-family: 'Merriweather', serif; font-weight: 700; color: #000; }
		.logo-text { font-size: 3.5rem; letter-spacing: -2px; }
		.journal-header { border-top: 1px solid #ccc; border-bottom: 3px solid #000; background: #fff; }
		.nav-link { color: #333; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
		.nav-link:hover, .nav-link.active { color: #b71c1c; }
		.section-title { border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px; }
		.panel { border: 0; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
		.panel .card-header { background: #fff; border-bottom: 2px solid #111; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.8px; }
		.table thead th { text-transform: uppercase; font-size: 0.78rem; letter-spacing: 0.6px; }
		.form-control, .form-select { border-radius: 0; }
		.btn { border-radius: 0; }
	</style>
</head>
<body>
<header class="bg-white pt-4 pb-3">
	<div class="container text-center"><a href="index" class="text-decoration-none"><h1 class="logo-text mb-0">INFOFLASH</h1></a></div>
</header>

<nav class="navbar navbar-expand-lg navbar-light journal-header mb-4 sticky-top shadow-sm">
	<div class="container">
		<button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Menu backoffice">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse justify-content-center" id="navbarNav">
			<ul class="navbar-nav gap-3">
				<li class="nav-item"><a class="nav-link" href="index">Actualités</a></li>
				<li class="nav-item"><a class="nav-link" href="article">Articles</a></li>
				<li class="nav-item"><a class="nav-link" href="article-image">Images</a></li>
				<li class="nav-item"><a class="nav-link active" href="category-article">Catégories</a></li>
				<li class="nav-item"><a class="nav-link" href="source">Sources</a></li>
				<li class="nav-item"><a class="nav-link" href="type-source">Types</a></li>
				<li class="nav-item ms-lg-3"><a href="../../frontoffice/" class="btn btn-sm btn-dark fw-bold px-3">Frontoffice</a></li>
			</ul>
		</div>
	</div>
</nav>

<main class="container pb-5">
	<h2 class="section-title h4">Gestion des catégories</h2>
	<?php if (!empty($message)): ?><div class="alert alert-success rounded-0"><?php echo e($message); ?></div><?php endif; ?>
	<?php if (!empty($error)): ?><div class="alert alert-danger rounded-0"><?php echo e($error); ?></div><?php endif; ?>

	<div class="card panel mb-4">
		<div class="card-header"><?php echo $isEdit ? 'Modifier' : 'Créer'; ?> une catégorie</div>
		<div class="card-body">
			<form method="post">
				<input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'create'; ?>">
				<?php if ($isEdit): ?><input type="hidden" name="id" value="<?php echo e($editItem['id']); ?>"><?php endif; ?>
				<div class="row g-3">
					<div class="col-md-6"><input class="form-control" name="name" placeholder="Name" required value="<?php echo e($editItem['name'] ?? ''); ?>"></div>
					<div class="col-md-6"><input class="form-control" name="slug" placeholder="Slug" value="<?php echo e($editItem['slug'] ?? ''); ?>"></div>
				</div>
				<div class="mt-3 d-flex gap-2">
					<button class="btn btn-primary" type="submit"><?php echo $isEdit ? 'Mettre à jour' : 'Créer'; ?></button>
					<?php if ($isEdit): ?><a class="btn btn-outline-secondary" href="category-article">Annuler</a><?php endif; ?>
				</div>
			</form>
		</div>
	</div>

	<div class="card panel">
		<div class="card-header">Liste des catégories</div>
		<div class="table-responsive">
			<table class="table table-hover align-middle mb-0">
				<thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Actions</th></tr></thead>
				<tbody>
				<?php foreach ($items as $item): ?>
					<tr>
						<td><?php echo e($item['id']); ?></td>
						<td><?php echo e($item['name']); ?></td>
						<td><?php echo e($item['slug']); ?></td>
						<td class="d-flex gap-2">
							<a class="btn btn-sm btn-outline-dark" href="?edit=<?php echo e($item['id']); ?>">Modifier</a>
							<form method="post" onsubmit="return confirm('Supprimer cette catégorie ?');">
								<input type="hidden" name="action" value="delete">
								<input type="hidden" name="id" value="<?php echo e($item['id']); ?>">
								<button class="btn btn-sm btn-danger" type="submit">Supprimer</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</main>

<footer class="bg-dark text-white py-4 mt-5"><div class="container text-center"><p class="mb-0 small text-secondary">&copy; <?php echo date("Y"); ?> Backoffice InfoFlash.</p></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
