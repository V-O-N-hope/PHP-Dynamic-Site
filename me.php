<?php
require_once('includes/bd_data.php');

// Проверка наличия значения 'key' в куках
if (!isset($_COOKIE['key'])) {
    header('Location: logout.php');
    exit();
}

$uniqKey = $_COOKIE['key'];

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соответствия уникального ключа в базе данных и получение ID пользователя
$query = "SELECT id FROM users WHERE uniqKey = '$uniqKey'";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    $conn->close();
    setcookie('key', '', time() - 3600);
    header('Location: logout.php');
    exit();
}

$row = $result->fetch_assoc();
$userId = $row['id'];

// Получение заказов пользователя с информацией о продукте
$orderQuery = "SELECT orders.id, orders.order_date, product_cards.name, product_cards.image_path, product_cards.price, orders.status
               FROM orders
               JOIN product_cards ON orders.product_card_id = product_cards.id
               WHERE orders.user_id = '$userId'
               ORDER BY orders.order_date DESC";
$orderResult = $conn->query($orderQuery);

$conn->close();

require_once('includes/header.php');

$stage = '';

if ($_COOKIE['key'] === 'root'){
    require_once('includes/root.php');
    $stage = 'd-none';
}
?>

<div class="container <?php echo $stage; ?>">
<a href="logout.php" class="btn btn-danger d-flex justify-content-center">Выход</a>
    <div class="row">
        <div class="col-lg-12">
            <h2>Мои заказы</h2>
            <?php if ($orderResult && $orderResult->num_rows > 0) : ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Номер заказа</th>
                            <th>Дата заказа</th>
                            <th>Превью</th>
                            <th>Имя продукта</th>
                            <th>Цена</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orderResult->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo $order['order_date']; ?></td>
                                <td><img src="<?php echo $order['image_path']; ?>" alt="Превью" class="img-thumbnail" width="40px"></td>
                                <td><?php echo $order['name']; ?></td>
                                <td><?php echo $order['price']; ?></td>
                                <td><?php echo $order['status']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>У вас пока нет заказов.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
