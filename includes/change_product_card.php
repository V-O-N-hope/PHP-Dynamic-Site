<?php
require_once('bd_data.php');
require_once('headerInner.php');

$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Получение списка имен карточек продуктов
$query = "SELECT id, name, image_path FROM product_cards";
$result = $connection->query($query);

$productCards = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productCards[$row['id']] = array('name' => $row['name'], 'image_path' => $row['image_path']);
    }
}

// Обработка запроса на сохранение изменений
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_changes'])) {
    $cardId = $_POST['card_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $rating = $_POST['rating'];

    // Обновление данных карточки товара в базе данных
    $query = "UPDATE product_cards SET name = '$name', price = '$price', description = '$description', rating = '$rating' WHERE id = $cardId";
    $updateResult = $connection->query($query);

    if ($updateResult) {
        echo "<div class=\"alert alert-success\">Изменения успешно сохранены.</div>";
    } else {
        echo "<div class=\"alert alert-danger\">Ошибка при сохранении изменений: " . $connection->error . "</div>";
    }
}

?>

<div class="container">
    <h1>Изменение карточки товара</h1>
    <form action="change_product_card.php" method="GET">
        <div class="form-group">
            <label for="card_id">Выберите карточку товара:</label>
            <select class="form-control" id="card_id" name="card_id" onchange="this.form.submit()">
                <option value="">Выберите карточку</option>
                <?php
                foreach ($productCards as $cardId => $cardData) {
                    $name = $cardData['name'];
                    $imagePath = $cardData['image_path'];
                    $selected = ($cardId == $_GET['card_id']) ? 'selected' : '';
                    echo "<option value=\"$cardId\" $selected>$name</option>";
                }
                ?>
            </select>
        </div>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['card_id'])) {
        $cardId = $_GET['card_id'];

        // Получение данных карточки товара из базы данных
        $query = "SELECT * FROM product_cards WHERE id = $cardId";
        $result = $connection->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $name = $row['name'];
            $imagePath = $row['image_path'];
            $price = $row['price'];
            $description = $row['description'];
            $rating = $row['rating'];

            // Вывод формы редактирования карточки товара
            echo "<h2>Редактирование карточки товара</h2>";
            echo "<form action=\"change_product_card.php\" method=\"POST\">";
            echo "<input type=\"hidden\" name=\"card_id\" value=\"$cardId\">";

            // Превью изображения
            echo "<div class=\"preview\">";
            echo "<img src=\"../$imagePath\" alt=\"Превью\" class=\"preview-image\" height=\"200px\">";
            echo "</div>";

            echo "<div class=\"form-group\">";
            echo "<label for=\"name\">Название:</label>";
            echo "<input type=\"text\" class=\"form-control\" id=\"name\" name=\"name\" value=\"$name\">";
            echo "</div>";

            echo "<div class=\"form-group\">";
            echo "<label for=\"price\">Цена:</label>";
            echo "<input type=\"text\" class=\"form-control\" id=\"price\" name=\"price\" value=\"$price\">";
            echo "</div>";

            echo "<div class=\"form-group\">";
            echo "<label for=\"description\">Описание:</label>";
            echo "<textarea class=\"form-control\" id=\"description\" name=\"description\">$description</textarea>";
            echo "</div>";

            echo "<div class=\"form-group\">";
            echo "<label for=\"rating\">Рейтинг:</label>";
            echo "<input type=\"text\" class=\"form-control\" id=\"rating\" name=\"rating\" value=\"$rating\">";
            echo "</div>";

            echo "<button type=\"submit\" class=\"btn btn-primary\" name=\"save_changes\">Сохранить</button>";
            echo "</form>";
        } else {
            echo "Карточка товара не найдена.";
        }
    }
    ?>



<?php
require_once('footer.php');
?>
