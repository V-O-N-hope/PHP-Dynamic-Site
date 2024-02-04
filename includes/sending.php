<?php
    require_once('bd_data.php');
    require_once('headerInner.php');
?>

<div class="container">
    <h1>Рассылка</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <div class="form-group">
        <label for="subject">Заголовок:</label>
        <input type="text" class="form-control" id="subject" name="subject" required>
    </div>
    <div class="form-group">
        <label for="message">Текст сообщения:</label>
        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Отправить</button>
</form>

</div>

<?php
require 'vendor/autoload.php'; // Подключение автозагрузчика Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Подключение к базе данных и получение списка пользователей для рассылки
    $connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($connection->connect_error) {
        die("Ошибка подключения к базе данных: " . $connection->connect_error);
    }

    $query = "SELECT email FROM users WHERE shouldSend = true";
    $result = $connection->query($query);

    if ($result && $result->num_rows > 0) {
        // Настройка PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Конфигурация SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Укажите SMTP-сервер
            $mail->SMTPAuth = true;
            $mail->Username = 'ksisdrive@gmail.com'; // Укажите вашу почту
            $mail->Password = 'wubddgtpnvcznvwd'; // Укажите пароль от почты
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Отправка сообщения каждому пользователю
            while ($row = $result->fetch_assoc()) {
                $email = $row['email'];
                $mail->setFrom('ksisdrive@gmail.com', 'Your CDeliv'); // Укажите вашу почту и имя отправителя
                $mail->addAddress($email); // Добавьте адрес получателя
                $mail->Subject = $subject;
                $mail->Body = $message;

                $mail->send();
            }

            echo 'Сообщение успешно отправлено всем пользователям.';
        } catch (Exception $e) {
            echo 'Ошибка при отправке сообщения: ' . $mail->ErrorInfo;
        }
    } else {
        echo 'Нет пользователей для отправки сообщения.';
    }

    $connection->close();

    require_once('footer.php');
}
?>