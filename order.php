<?php
require_once('includes/bd_data.php');

// Проверка наличия GET-запроса
if (!isset($_GET['id'])) {
    header('Location: catalog.php');
    exit();
}

// Проверка наличия значения 'key' в куках
if (!isset($_COOKIE['key'])) {
    header('Location: logout.php');
    exit();
}

$productId = $_GET['id'];
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

// Получение информации о карточке товара
$productQuery = "SELECT * FROM product_cards WHERE id = '$productId'";
$productResult = $conn->query($productQuery);

if (!$productResult || $productResult->num_rows === 0) {
    $conn->close();
    header('Location: catalog.php');
    exit();
}

$product = $productResult->fetch_assoc();

// Обработка отправки формы заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Выполняем запрос для внесения данных в базу данных
    $insertQuery = "INSERT INTO orders (product_card_id, user_id, status)
                    VALUES ('$productId', '$userId', 'В стадии принятия')";
    $insertResult = $conn->query($insertQuery);

    if ($insertResult) {
        // Заказ успешно добавлен в базу данных
        $orderId = $conn->insert_id;

        $conn->close();
        header('Location: me.php');
        exit();
    } else {
        // Ошибка при добавлении заказа
        $conn->close();
        header('Location: catalog.php');
        exit();
    }
}

$conn->close();

require_once('includes/header.php');
?>

<div class="container">
    <div class="row">
        <div class="col-lg-6">
            <h2><?php echo $product['name']; ?></h2>
            <img src="<?php echo $product['image_path']; ?>" alt="Product Image" class="img-thumbnail">
            <p><?php echo $product['description']; ?></p>
            <p>Рейтинг: <?php echo $product['rating']; ?></p>
        </div>
        <div class="col-lg-6">
            <h2>Форма заказа</h2>
            <form method="POST">
                <button type="submit" class="btn btn-primary">Подтвердить заказ</button>
            </form>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
