<?php
require_once('bd_data.php');
require_once('headerInner.php');

$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Получение списка почтовых адресов пользователей
$query = "SELECT id, email FROM users";
$result = $connection->query($query);

$userEmails = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userEmails[$row['id']] = $row['email'];
    }
}

// Обработка запроса на изменение заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_order'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    // Обновление данных заказа в базе данных
    $query = "UPDATE orders SET status = '$status' WHERE id = $orderId";
    $updateResult = $connection->query($query);

    if ($updateResult) {
        echo "<div class=\"alert alert-success\">Изменения заказа успешно сохранены.</div>";
    } else {
        echo "<div class=\"alert alert-danger\">Ошибка при сохранении изменений заказа: " . $connection->error . "</div>";
    }
}

?>

<div class="container">
    <h1>Изменение заказа</h1>
    <form action="order_changing.php" method="POST">
        <div class="form-group">
            <label for="user_email">Выберите пользователя:</label>
            <select class="form-control" id="user_email" name="user_email" onchange="this.form.submit()">
                <option value="">Выберите пользователя</option>
                <?php
                foreach ($userEmails as $userId => $email) {
                    $selected = ($_POST['user_email'] == $email) ? 'selected' : '';
                    echo "<option value=\"$email\" $selected>$email</option>";
                }
                ?>
            </select>
        </div>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_email'])) {
            $selectedUserEmail = $_POST['user_email'];

            // Получение ID пользователя по выбранной почте
            $query = "SELECT id FROM users WHERE email = '$selectedUserEmail'";
            $result = $connection->query($query);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $userId = $row['id'];

                // Получение списка заказов выбранного пользователя
                $query = "SELECT id, order_date, status FROM orders WHERE user_id = $userId";
                $result = $connection->query($query);

                if ($result && $result->num_rows > 0) {
                    echo "
                    <div class=\"form-group\">
                        <label for=\"order_id\">Выберите заказ:</label>
                        <select class=\"form-control\" id=\"order_id\" name=\"order_id\">
                            <option value=\"\">Выберите заказ</option>
                    ";

                    while ($row = $result->fetch_assoc()) {
                        $orderId = $row['id'];
                        $orderDate = $row['order_date'];
                        $status = $row['status'];

                        $selected = ($_POST['order_id'] == $orderId) ? 'selected' : '';
                        echo "<option value=\"$orderId\" $selected>$orderId ($orderDate)</option>";
                    }

                    echo "
                        </select>
                    </div>
                    ";
                }
            }
        }
        ?>
        <div class="form-group">
            <label for="status">Статус заказа:</label>
            <select class="form-control" id="status" name="status">
                <option value="В стадии принятия" <?php echo (isset($_POST['status']) && $_POST['status'] == 'В стадии принятия') ? 'selected' : ''; ?>>В стадии принятия</option>
                <option value="В стадии готовки" <?php echo (isset($_POST['status']) && $_POST['status'] == 'В стадии готовки') ? 'selected' : ''; ?>>В стадии готовки</option>
                <option value="В стадии доставки" <?php echo (isset($_POST['status']) && $_POST['status'] == 'В стадии доставки') ? 'selected' : ''; ?>>В стадии доставки</option>
                <option value="Доставлено" <?php echo (isset($_POST['status']) && $_POST['status'] == 'Доставлено') ? 'selected' : ''; ?>>Доставлено</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" name="change_order">Сохранить изменения</button>
    </form>
</div>

<?php
require_once('footer.php');
?>
