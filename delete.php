<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'projet_crud';

try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $sqlQuery = "DELETE FROM utilisateurs WHERE ID = :id";
        $statement = $db->prepare($sqlQuery);
        $statement->bindParam(':id', $id);
        $statement->execute();

        header('Location: index.php');
    }
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}