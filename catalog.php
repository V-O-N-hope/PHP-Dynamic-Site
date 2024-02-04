<?php

require_once('includes/header.php');
require_once('includes/bd_data.php');

// Установка соединения с базой данных
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

// Запрос на получение карточек товаров
$sql = "SELECT * FROM product_cards";
$result = $conn->query($sql);

// Проверка наличия карточек товаров
if ($result->num_rows > 0) {
    echo '<div class="container">';
    echo '<div class="row justify-content-center">'; // Добавлено выравнивание по центру

    // Перебор и отображение карточек товаров
    while ($row = $result->fetch_assoc()) {
        echo '<div class="col-lg-4 col-md-4 col-sm-6 mb-4">';
        echo '<div class="card">';
        echo '<img src="' . $row['image_path'] . '" class="card-img-top" alt="Product Image">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">' . $row['name'] . '</h5>'; // Добавлено отображение имени продукта
        echo '<h6 class="card-subtitle mb-2 text-muted">' . $row['price'] . ' руб.</h6>'; // Перенесено отображение цены в подзаголовок
        echo '<p class="card-text">' . $row['description'] . '</p>';
        echo '<a href="order.php?id=' . $row['id'] . '" class="btn btn-primary">Заказать</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';
} else {
    echo '<div class="container">';
    echo '<div class="row">';
    echo '<div class="col text-center">';
    echo '<p class="text-muted">Нет доступных товаров.</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

// Закрытие соединения с базой данных
$conn->close();

require_once('includes/footer.php');
