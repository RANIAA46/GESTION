<?php
$pdo = new PDO("mysql:host=localhost;dbname=gestionabsence", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// جلب الأساتذة
$enseignants = $pdo->query("SELECT Id_Ens, Nom_Ens, Prenom_Ens FROM enseignant")->fetchAll();

// هل تم اختيار أستاذ؟
$enseignantSelectionne = null;
if (isset($_GET['id_ens'])) {
    $id_ens = (int)$_GET['id_ens'];
    $stmt = $pdo->prepare("SELECT * FROM enseignant WHERE Id_Ens = ?");
    $stmt->execute([$id_ens]);
    $enseignantSelectionne = $stmt->fetch();
}

// جلب البيانات للنموذج
$groupes = $pdo->query("SELECT Id_Grp FROM groupe")->fetchAll();
$salles = $pdo->query("SELECT nom_salle FROM salle")->fetchAll();
$seances = $pdo->query("SELECT Num_Rep, Heure_Deb, Heure_Fin FROM repartition")->fetchAll();

// جلب modules المرتبطة بالأستاذ المحدد
$modules = [];
if ($enseignantSelectionne) {
    $stmt_mod = $pdo->prepare("SELECT m.Id_Mdl, m.Disigne FROM module m JOIN enseigne es ON m.Id_Mdl = es.Id_Mdl WHERE es.Id_Ens = ?");
    $stmt_mod->execute([$enseignantSelectionne['Id_Ens']]);
    $modules = $stmt_mod->fetchAll();
}

// المعالجة إذا تم إرسال النموذج
$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jours = $_POST["jours"];
    $id_ens = $_POST["id_ens"];
    $num_rep = $_POST["num_rep"];
    $id_grp = $_POST["id_grp"];
    $id_mdl = $_POST["id_mdl"];
    $nom_salle = $_POST["nom_salle"];

    $stmt = $pdo->prepare("INSERT INTO emploi (Jours, Id_Ens, Num_Rep, Id_Grp, Id_Mdl, nom_salle) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$jours, $id_ens, $num_rep, $id_grp, $id_mdl, $nom_salle])) {
        // ✅ تم الحفظ، نعيد التوجيه إلى صفحة emploi الخاصة بالأستاذ
        header("Location: emploiprof.php?id_ens=" . $id_ens);
        exit;
    } else {
        echo "<div class='error'>❌ فشل في الإضافة.</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Enseignants</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #eef1f5;
            margin: 0;
            padding: 30px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-bottom: 40px;
        }
        .card {
            background: white;
            width: 220px;
            height: 130px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 1.1rem;
            color: #444;
            text-decoration: none;
            text-align: center;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
            color: #007BFF;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        form label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
        }
        form select, input[type=submit] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        input[type=submit] {
            background: #007BFF;
            color: white;
            margin-top: 20px;
            font-weight: bold;
            cursor: pointer;
        }
        .success {
            text-align: center;
            background: #d4edda;
            padding: 10px;
            border-radius: 8px;
            color: #155724;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h1>Choisissez un Enseignant</h1>

<div class="cards">
    <?php foreach ($enseignants as $ens): ?>
        <a href="?id_ens=<?= $ens['Id_Ens'] ?>" class="card">
            <?= htmlspecialchars($ens['Nom_Ens'] . ' ' . $ens['Prenom_Ens']) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if ($enseignantSelectionne): ?>
    <div class="form-container">
        <h2>Ajouter Emploi pour <?= htmlspecialchars($enseignantSelectionne['Nom_Ens'] . ' ' . $enseignantSelectionne['Prenom_Ens']) ?></h2>
        <?php if ($success): ?>
            <div class="success">✅ Emploi ajouté avec succès !</div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="id_ens" value="<?= $enseignantSelectionne['Id_Ens'] ?>">

            <label for="jours">Jour :</label>
            <select name="jours" required>
                <option value="">-- Choisir --</option>
                <?php foreach (['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi'] as $j): ?>
                    <option value="<?= $j ?>"><?= $j ?></option>
                <?php endforeach; ?>
            </select>

            <label for="num_rep">Séance :</label>
            <select name="num_rep" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($seances as $s): ?>
                    <option value="<?= $s['Num_Rep'] ?>">
                        <?= "Séance {$s['Num_Rep']} - {$s['Heure_Deb']} à {$s['Heure_Fin']}" ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="id_grp">Groupe :</label>
            <select name="id_grp" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($groupes as $g): ?>
                    <option value="<?= $g['Id_Grp'] ?>">Groupe <?= $g['Id_Grp'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="id_mdl">Module :</label>
            <select name="id_mdl" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($modules as $m): ?>
                    <option value="<?= $m['Id_Mdl'] ?>"><?= htmlspecialchars($m['Disigne']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="nom_salle">Salle :</label>
            <select name="nom_salle" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($salles as $s): ?>
                    <option value="<?= htmlspecialchars($s['nom_salle']) ?>"><?= htmlspecialchars($s['nom_salle']) ?></option>
                <?php endforeach; ?>
            </select>

            <input type="submit" value="Ajouter">
        </form>
    </div>
<?php endif; ?>

</body>
</html>
