
<?php

$servername = "root";
$username = "root";
$password = "";
$dbname = "todolist"; 

try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}


function lireTaches($db) {
    $requete = "SELECT * FROM todo ORDER BY created_at DESC";
    $resultat = $db->query($requete);

    if ($resultat) {
        return $resultat->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}


function ajouterTache($db, $title) {
    $requete = "INSERT INTO todo (title) VALUES (:title)";
    $statement = $db->prepare($requete);
    $statement->bindParam(':title', $title);
    return $statement->execute();
}


function supprimerTache($db, $id) {
    $requete = "DELETE FROM todo WHERE id = :id";
    $statement = $db->prepare($requete);
    $statement->bindParam(':id', $id);
    return $statement->execute();
}


function basculerEtatTache($db, $id) {
    $requete = "UPDATE todo SET done = 1 - done WHERE id = :id";
    $statement = $db->prepare($requete);
    $statement->bindParam(':id', $id);
    return $statement->execute();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'new':
                if (isset($_POST['title'])) {
                    ajouterTache($db, $_POST['title']);
                }
                break;

            case 'delete':
                if (isset($_POST['id'])) {
                    supprimerTache($db, $_POST['id']);
                }
                break;

            case 'toggle':
                if (isset($_POST['id'])) {
                    basculerEtatTache($db, $_POST['id']);
                }
                break;
        }
    }
}


$taches = lireTaches($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo List</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rGBLHhzxE0VoKX5/3C75Ck4whCfI60lA6ZHfUsqAd2N9gElTUI6E4lThqGDr8xj" crossorigin="anonymous">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Ma ToDo List</a>
        </div>
    </nav>

    <div class="container mt-3">

        <!--ajouter  -->
        <form action="" method="post" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="title" placeholder="Nouvelle tâche" required>
                <button type="submit" class="btn btn-primary" name="action" value="new">Ajouter</button>
            </div>
        </form>

        <!-- Liste des tâches -->
        <ul class="list-group">
            <?php foreach ($taches as $tache): ?>
                <li class="list-group-item <?php echo ($tache['done'] ? 'list-group-item-success' : 'list-group-item-warning'); ?>">
                    <?php echo $tache['title']; ?>

                    <!--  supprimer -->
                    <form action="" method="post" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $tache['id']; ?>">
                        <button type="submit" class="btn btn-success" name="action" value="toggle">Basculer</button>
                        <button type="submit" class="btn btn-danger" name="action" value="delete">Supprimer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-bmM4evuBpk1mUft0RW7B4Vl5lKi5R+knyjy6A6M9N0nLyE1U6b1QYoqEe/zM8D88" crossorigin="anonymous"></script>

</body>
</html>
