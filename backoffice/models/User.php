<?php

    include '../connect/Connect.php';

    function getUserByCredentials($username, $password) {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT id, username, email, password, role, created_at FROM user WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        closeConnection($conn);

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        unset($user['password']);
        return $user;
    }

    function getUserByMdp($username, $mdp) {
        return getUserByCredentials($username, $mdp);
    }




?>