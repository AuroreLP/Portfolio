<?php
// Sécurisation du fichier
$filename = 'downloads/cv-aurore-le-perff.pdf';

// Vérifier si le fichier existe
if (!file_exists($filename)) {
    die('Fichier introuvable.');
}

// Définir les headers pour le téléchargement
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="cv-aurore-le-perff.pdf"');
header('Content-Length: ' . filesize($filename));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Lire et envoyer le fichier
readfile($filename);
exit;
?>
