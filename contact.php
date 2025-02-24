<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json'); // Définit le type de contenu de la réponse

if (isset($_POST['email'], $_POST['lastname'], $_POST['name'], $_POST['tel'], $_POST['msg'], $_POST['subject']) &&
    !empty($_POST['email']) && !empty($_POST['lastname']) && !empty($_POST['name']) &&
    !empty($_POST['tel']) && !empty($_POST['msg']) && !empty($_POST['subject'])) {
    
    $from_email = trim($_POST['email']);
    $from_lastname = trim($_POST['lastname']);
    $from_name = trim($_POST['name']);
    $tel = trim($_POST['tel']);
    $subject = "Nouveau message de $from_name $from_lastname";
    $message = nl2br(htmlspecialchars(trim($_POST['msg']), ENT_QUOTES, 'UTF-8'));

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
            echo json_encode(['status' => 'success', 'message' => 'Message envoyé avec succès.']);
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
