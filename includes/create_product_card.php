<?php
require_once("headerInner.php");
require_once('bd_data.php');

// Проверяем, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, загружено ли изображение
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Получаем информацию о загруженном файле
        $image = $_FILES['image'];
        $imagePath = '../items/' . basename($image['name']); // Путь для сохранения изображения
        $imageName = pathinfo($imagePath, PATHINFO_FILENAME); // Имя картинки без расширения

        // Перемещаем загруженное изображение в папку uploads
        move_uploaded_file($image['tmp_name'], $imagePath);

        // Получаем остальные данные из формы
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $rating = $_POST['rating'];

        $imagePath = 'items/' . basename($image['name']); // Путь для сохранения изображения
        // Подключаемся к базе данных
        $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if (!$connection) {
            die('Ошибка подключения к базе данных: ' . mysqli_connect_error());
        }

        // Экранируем данные перед внесением в базу данных
        $name = mysqli_real_escape_string($connection, $name);
        $imagePath = mysqli_real_escape_string($connection, $imagePath);
        $price = mysqli_real_escape_string($connection, $price);
        $description = mysqli_real_escape_string($connection, $description);
        $rating = mysqli_real_escape_string($connection, $rating);

        // Выполняем запрос для внесения данных в базу данных
        $query = "INSERT INTO product_cards (name, image_path, price, description, rating) 
                  VALUES ('$name', '$imagePath', '$price', '$description', '$rating')";
        $result = mysqli_query($connection, $query);

        // Проверяем успешность выполнения запроса
        if ($result) {
            // Выводим сообщение об успешном создании карточки товара
            echo '<div class="alert alert-success" role="alert">Карточка товара успешно создана!</div>';
        } else {
            // Выводим сообщение об ошибке при выполнении запроса
            echo '<div class="alert alert-danger" role="alert">Ошибка при создании карточки товара!</div>';
        }

        // Закрываем соединение с базой данных
        mysqli_close($connection);
    } else {
        // Выводим сообщение об ошибке загрузки изображения
        echo '<div class="alert alert-danger" role="alert">Ошибка загрузки изображения!</div>';
    }
}
?>

<!-- Ваш HTML-код для отображения страницы -->

<div class="container">
    <h2 class="mt-4">Создание карточки товара</h2>
    <form action="create_product_card.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="image">Изображение товара:</label>
            <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
        </div>
        <div class="form-group">
            <label for="name">Имя товара:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="price">Цена:</label>
            <input type="text" class="form-control" id="price" name="price" required>
        </div>
        <div class="form-group">
            <label for="description">Описание:</label>
            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="rating">Рейтинг:</label>
            <input type="text" class="form-control" id="rating" name="rating" required>
        </div>
        <button type="submit" class="btn btn-primary">Создать</button>
    </form>
</div>


<?php
    require_once("footer.php");
?>
