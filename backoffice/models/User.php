<?php

    include '../connect/Connect.php';

    function getUserByMdp($username, $mdp) {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $mdp);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }




?>