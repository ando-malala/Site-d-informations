<?php
session_start();
include_once 'connect/Connect.php';

$error = '';

if (isset($_SESSION['user_id'])) {
    header('Location: pages/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        try {
            $conn = getConnection();
            
            $sql = "SELECT id, username, password, role FROM user WHERE username = ? OR email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                if (password_verify($password, $user['password'])) {
         
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    header('Location: pages/index.php');
                    exit();
                } else {
                    $error = "Identifiants incorrects.";
                }
            } else {
                $error = "Identifiants incorrects.";
            }
            closeConnection($conn);
        } catch (Throwable $e) {
            $error = "Erreur de connexion à la base de données.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | Administration InfoFlash</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Open Sans', sans-serif; background-color: #f4f6f9; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { max-width: 400px; width: 100%; border: none; border-top: 4px solid #111; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .logo-text { font-family: 'Merriweather', serif; font-weight: 900; letter-spacing: -1px; color: #111; }
    </style>
</head>
<body>

    <div class="card login-card p-4">
        <div class="text-center mb-4">
            <h1 class="logo-text h2 mb-1">INFOFLASH</h1>
            <p class="text-muted small text-uppercase tracking-widest">Espace Rédaction</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small rounded-0 text-center">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="username" class="form-label small fw-bold">Nom d'utilisateur ou Email</label>
                <input type="text" class="form-control rounded-0" id="username" name="username" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label small fw-bold">Mot de passe</label>
                <input type="password" class="form-control rounded-0" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-dark w-100 rounded-0 fw-bold text-uppercase" style="letter-spacing: 1px;">Se connecter</button>
        </form>
        
        <div class="text-center mt-4 pt-3 border-top">
            <a href="inscription.php" class="text-danger small text-decoration-none fw-bold d-block mb-2">Créer un nouveau compte</a>
            <a href="../frontoffice/index.php" class="text-muted small text-decoration-none">&larr; Retour au site public</a>
        </div>
    </div>
        
        <div class="text-center mt-4">
            <a href="../frontoffice/index.php" class="text-muted small text-decoration-none">&larr; Retour au site public</a>
        </div>
    </div>

</body>
</html>