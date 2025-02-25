<?php
session_start(); // 🔹 Doit être tout en haut

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json'); // Définit le type de contenu de la réponse

// Empêcher l'accès direct sans AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    die('Accès interdit.');
}

// Vérifier que la requête est bien en POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
    exit;
}

// Protection anti-spam (30s entre chaque envoi)
if (isset($_SESSION['last_submit_time']) && time() - $_SESSION['last_submit_time'] < 30) {
    echo json_encode(['status' => 'error', 'message' => 'Veuillez attendre 30 secondes avant un nouvel envoi.']);
    exit;
}
$_SESSION['last_submit_time'] = time();

// Fonction de nettoyage des entrées utilisateur
function clean_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Vérification et récupération des entrées utilisateur
if (isset($_POST['email'], $_POST['lastname'], $_POST['name'], $_POST['tel'], $_POST['msg'], $_POST['subject']) &&
    !empty($_POST['email']) && !empty($_POST['lastname']) && !empty($_POST['name']) &&
    !empty($_POST['tel']) && !empty($_POST['msg']) && !empty($_POST['subject'])
) {
    $from_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : null;
    $from_lastname = clean_input($_POST['lastname']);
    $from_name = clean_input($_POST['name']);
    $tel = ctype_digit($_POST['tel']) ? $_POST['tel'] : null; // Vérifie que le téléphone est numérique
    $subject = clean_input($_POST['subject']);
    $message = nl2br(clean_input($_POST['msg']));

    if (!$from_email || !$tel) {
        echo json_encode(['status' => 'error', 'message' => 'Email ou téléphone invalide.']);
        exit;
    }

    $mail = new PHPMailer();
    try {
        $mail->isSMTP();
        $mail->SMTPDebug  = 0;
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username = $_ENV['EMAIL_USERNAME'];
        $mail->Password = $_ENV['EMAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom($from_email, "$from_name $from_lastname");
        $mail->addAddress('aleperff@gmail.com', 'Aurore');

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = "<strong>Nom :</strong> $from_lastname <br>
                          <strong>Prénom :</strong> $from_name <br>
                          <strong>Email :</strong> $from_email <br>
                          <strong>Téléphone :</strong> $tel <br><br>
                          <strong>Message :</strong> <br> $message";

        if ($mail->send()) {
            echo json_encode(['status' => 'success', 'message' => 'Merci pour votre message.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'envoi : ' . $mail->ErrorInfo]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Tous les champs sont obligatoires.']);
}
?>
