<?php
session_start();
include_once 'connect/Connect.php';

$error = '';
$success = '';

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'editeur';

    // 1. Vérifications de base
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Veuillez remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Le format de l'email est invalide.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif ($role !== 'admin' && $role !== 'editeur') {
        $error = "Le rôle sélectionné est invalide.";
    } else {
        try {
            $conn = getConnection();
            
            // 2. Vérifier si l'utilisateur ou l'email existe déjà
            $checkSql = "SELECT id FROM user WHERE username = ? OR email = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("ss", $username, $email);
            $checkStmt->execute();
            
            if ($checkStmt->get_result()->num_rows > 0) {
                $error = "Ce nom d'utilisateur ou cet email est déjà utilisé.";
            } else {
                // 3. Hachage du mot de passe (TRÈS IMPORTANT POUR LA SÉCURITÉ)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // 4. Insertion dans la base de données
                $insertSql = "INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->bind_param("ssss", $username, $email, $hashed_password, $role);
                
                if ($insertStmt->execute()) {
                    $success = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
                } else {
                    $error = "Erreur lors de la création du compte.";
                }
            }
            closeConnection($conn);
        } catch (Throwable $e) {
            $error = "Erreur de connexion à la base de données : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte | Administration InfoFlash</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Open Sans', sans-serif; background-color: #f4f6f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px 0; }
        .login-card { max-width: 450px; width: 100%; border: none; border-top: 4px solid #b71c1c; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .logo-text { font-family: 'Merriweather', serif; font-weight: 900; letter-spacing: -1px; color: #111; }
    </style>
</head>
<body>

    <div class="card login-card p-4">
        <div class="text-center mb-4">
            <h1 class="logo-text h2 mb-1">INFOFLASH</h1>
            <p class="text-muted small text-uppercase tracking-widest">Nouveau collaborateur</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small rounded-0 text-center">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success py-3 small rounded-0 text-center fw-bold">
                <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?><br>
                <a href="login.php" class="btn btn-sm btn-success mt-2 rounded-0">Aller à la connexion</a>
            </div>
        <?php else: ?>

            <form method="POST" action="inscription.php">
                <div class="mb-3">
                    <label for="username" class="form-label small fw-bold">Nom d'utilisateur</label>
                    <input type="text" class="form-control rounded-0" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label small fw-bold">Adresse Email</label>
                    <input type="email" class="form-control rounded-0" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label small fw-bold">Mot de passe</label>
                        <input type="password" class="form-control rounded-0" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <label for="confirm_password" class="form-label small fw-bold">Confirmer</label>
                        <input type="password" class="form-control rounded-0" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="role" class="form-label small fw-bold">Rôle dans la rédaction</label>
                    <select class="form-select rounded-0" id="role" name="role" required>
                        <option value="editeur">Éditeur</option>
                        <option value="admin">Administrateur </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-danger w-100 rounded-0 fw-bold text-uppercase" style="letter-spacing: 1px;">Créer le compte</button>
            </form>

        <?php endif; ?>
        
        <div class="text-center mt-4 border-top pt-3">
            <span class="small text-muted">Déjà un compte ?</span>
            <a href="login.php" class="small text-dark fw-bold text-decoration-none ms-1">Se connecter</a>
        </div>
    </div>

</body>
</html>