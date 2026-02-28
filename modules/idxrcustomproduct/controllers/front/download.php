<?php
if (!isset($_GET['file'])) {
    echo "Aucun fichier spécifié.";
    exit;
}

$file_url = urldecode($_GET['file']);
$file_name = basename($file_url);

// Validate it’s an SVG
if (pathinfo($file_name, PATHINFO_EXTENSION) !== 'svg') {
    echo "Fichier non supporté.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Voir et Télécharger le Fichier SVG</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .svg-container {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            display: inline-block;
        }
        .download-btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        .download-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h2>Prévisualisation du fichier SVG</h2>
    <div class="svg-container">
        <object type="image/svg+xml" data="<?= htmlspecialchars($file_url) ?>" width="400" height="400">
            Votre navigateur ne prend pas en charge les SVG.
        </object>
    </div>

    <br>

    <a href="<?= htmlspecialchars($file_url) ?>" download="<?= htmlspecialchars($file_name) ?>" class="download-btn">
        📥 Télécharger le fichier
    </a>

</body>
</html>
