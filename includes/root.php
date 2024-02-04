<div class="container">
    <a href="logout.php" class="btn btn-danger d-flex justify-content-center">Выход</a>

    <h1>Создание карточки товара</h1>
    <a href="includes/create_product_card.php">Создать карточку</a>

    <h1>Редактирование карточки товара</h1>
    <a href="includes/change_product_card.php">Редактировать карточку</a>

    <h1>Редактирование заказа пользователя</h1>
    <a href="includes/order_changing.php">Редактировать заказ</a>

    <h1>Рассылка пользователям</h1>
    <a href="includes/sending.php">Отправить рассылку</a>

    <h1>Статистика посещений</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Количество</th>
                <th>ОС</th>
                <th>IP адрес</th>
                <th>Имя</th>
                <th>Дата</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
            if ($connection->connect_error) {
                die("Ошибка подключения к базе данных: " . $connection->connect_error);
            }

            $query = "SELECT os, ip_address, email, login_date FROM user_logins ORDER BY os DESC";
            $result = $connection->query($query);

            if ($result && $result->num_rows > 0) {
                $osCount = array();

                while ($row = $result->fetch_assoc()) {
                    $os = $row['os'];
                    $ipAddress = $row['ip_address'];
                    $name = $row['email'];
                    $loginDate = $row['login_date'];

                    if (isset($osCount[$os])) {
                        $osCount[$os]++;
                    } else {
                        $osCount[$os] = 1;
                    }

                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td>$os</td>";
                    echo "<td>$ipAddress</td>";
                    echo "<td>$name</td>";
                    echo "<td>$loginDate</td>";
                    echo "</tr>";
                }

                arsort($osCount); // Сортировка массива по убыванию

                foreach ($osCount as $os => $count) {
                    echo "<tr>";
                    echo "<td>$count</td>";
                    echo "<td>$os</td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Нет данных для отображения</td></tr>";
            }

            $connection->close();
            ?>
        </tbody>
    </table>
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
}
?>


