<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'projet_crud';

try {
$db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET['id'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $mail = $_POST['mail'];
    $codePostal = $_POST['codePostal'];


    if (isset($_POST['supprUser'])) {
        deleteUser($db, $_POST['supprUser']);
        header('Location: index.php');
    }

    if (empty($nom) || !preg_match("/^[a-zA-Z-']*$/", $nom)) {
        $_SESSION['error']['nom'] = "Merci de remplir le champs 'Nom' avec des lettres et des tirets uniquement.";
    }
    if (empty($prenom) || !preg_match("/^[a-zA-Z-']*$/", $prenom)) {
        $_SESSION['error']['prenom'] = "Merci de remplir le champs 'Prénom' avec des lettres et des tirets uniquement.";
    }
    if (empty($mail) || !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $mail)) {
        $_SESSION['error']['mail'] = "Merci de remplir le champs 'Mail' avec une adresse email valide";
    }
    if (empty($codePostal) || !preg_match("/^[0-9]{5}$/", $codePostal)) {
        $_SESSION['error']['codePostal'] = "Merci de remplir le champs 'Code Postal' avec un code postal à 5 chiffres";
    }

    if (empty($_SESSION['error'])) {
        $sql = "INSERT INTO utilisateurs (Nom, Prenom, Mail, CodePostal) VALUES (?, ?, ?, ?)";
        $statement = $db->prepare($sql);
        $statement->execute([$nom, $prenom, $mail, $codePostal]);

        header("Location: index.php");
        exit;
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM utilisateurs WHERE ID = ?";
    $statement = $db->prepare($sql);
    $statement->execute([$id]);

    $utilisateur = $statement->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $mail = $_POST['mail'];
        $codePostal = $_POST['codePostal'];

        $sql = "UPDATE utilisateurs SET Nom = ?, Prenom = ?, Mail = ?, CodePostal = ? WHERE ID = ?";
        $statement = $db->prepare($sql);
        $statement->execute([$nom, $prenom, $mail, $codePostal, $id]);

        header("Location: index.php");
        exit;
    }
}

$sql = "SELECT * FROM utilisateurs";
$statement = $db->prepare($sql);
$statement->execute();

$utilisateurs = $statement->fetchAll(PDO::FETCH_ASSOC);}
 catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet CRUD</title>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Projet CRUD</title>
        <link rel="stylesheet" href="./css/style.css">
    </head>
</head>

<body>
    <h1>Page de gestion des utilisateurs</h1>
    <form id="adduser" method="POST" action="index.php">
        <label for="nom">Nom:
            <?php if (isset($_SESSION['error']['nom']))
                echo $_SESSION['error']['nom']; ?>
        </label><br>
        <input type="text" id="nom" name="nom" placeholder="Entrez le nom de l'utilisateur"><br>

        <label for="prenom">Prénom:
            <?php if (isset($_SESSION['error']['prenom']))
                echo $_SESSION['error']['prenom']; ?>
        </label><br>
        <input type="text" id="prenom" name="prenom" placeholder="Entrez le prénom de l'utilisateur"><br>

        <label for="mail">Mail:
            <?php if (isset($_SESSION['error']['mail']))
                echo $_SESSION['error']['mail']; ?>
        </label><br>
        <input type="text" id="mail" name="mail" placeholder="Entrez l'adresse mail de l'utilisateur"><br>

        <label for="codePostal">Code Postal:
            <?php if (isset($_SESSION['error']['codePostal']))
                echo $_SESSION['error']['codePostal']; ?>
        </label><br>
        <input type="text" id="codePostal" name="codePostal" placeholder="Entrez le code postal de l'utilisateur"><br>

        <input type="submit" id="adduserButton" value="Ajouter utilisateur">
    </form>

    <?php echo "<table>";
    foreach ($utilisateurs as $utilisateur) {
        if (isset($_GET['id']) && $_GET['id'] == $utilisateur['ID']) {
            ?>
            <tr>
                <form id="edituser" method="POST">
                    <td><input type="text" name="nom" value="<?= $utilisateur['Nom'] ?>"></td>
                    <td><input type="text" name="prenom" value="<?= $utilisateur['Prenom'] ?>"></td>
                    <td><input type="text" name="mail" value="<?= $utilisateur['Mail'] ?>"></td>
                    <td><input type="text" name="codePostal" value="<?= $utilisateur['CodePostal'] ?>"></td>
                    <td id="savebuttonContainer"><input type="submit" id="saveButton" value="Enregistrer"></td>
                </form>
            </tr>
            <?php
        } else {
            ?>
            <tr>
                <td>
                    <?= $utilisateur['Nom'] ?>
                </td>
                <td>
                    <?= $utilisateur['Prenom'] ?>
                </td>
                <td>
                    <?= $utilisateur['Mail'] ?>
                </td>
                <td>
                    <?= $utilisateur['CodePostal'] ?>
                </td>
                <td>
                <div class="button-container">
                    <a href="?id=<?= $utilisateur['ID'] ?>" class="modifierButton">Modifier</a>
                    <form method="POST" action="index.php" class="deluserform">
                        <input type="hidden" name="supprUser" value="<?= $utilisateur['ID'] ?>">
                        <input type="submit" class="supprButton" value="Supprimer">
                    </form>
        </div>
                </td>
            </tr>
            <?php
        }
    }
    echo "</table>";
    ?>
</body>

</html>

<?php function deleteUser($db, $id) {
        $sqlQuery = "DELETE FROM utilisateurs WHERE ID = :id";
        $statement = $db->prepare($sqlQuery);
        $statement->bindParam(':id', $id);
        $statement->execute();
    } ?>