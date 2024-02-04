<?php

if (isset($_COOKIE['key'])) {
    echo '<script>window.location.href = "me.php";</script>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    require_once('includes/bd_data.php');
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Проверка входных данных на соответствие с данными в базе данных
    $query = "SELECT id, uniqKey FROM users WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userId = $row['id'];
        $uniqKey = $row['uniqKey'];

        // Запись уникального ключа в куки
        setcookie('key', $uniqKey, time() + 360000); // Пример установки на 1 час

        $conn->close();
        echo '<script>window.location.href = "me.php";</script>';
        exit();
    } else {
        $errorMessage = 'Неправильный email или пароль';
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CDeliv - Доставка еды</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Дополнительные стили, если необходимо */
        .navbar-brand {
            display: flex;
            align-items: center;
            color: #333;
            font-weight: bold;
            text-decoration: none;
        }

        .navbar-brand img {
            margin-right: 10px;
        }

        .navbar-nav .nav-link {
            color: #333;
            font-weight: bold;
            margin-right: 15px;
        }

        .navbar-nav .nav-link:hover {
            color: #555;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-5">
            <a class="navbar-brand" href="index.php">
                <img src="logo/logo.jpg" alt="CDeliv Logo" height="30">
                CDeliv
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="catalog.php">Каталог</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="me.php">Я</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    
<div class="container">
    <h1>Вход</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control <?php if (isset($errorMessage)) echo 'is-invalid'; ?>" id="email" name="email" placeholder="Введите email" required>
            <?php if (isset($errorMessage)) echo '<div class="invalid-feedback">' . $errorMessage . '</div>'; ?>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control <?php if (isset($errorMessage)) echo 'is-invalid'; ?>" id="password" name="password" placeholder="Введите пароль" required>
            <?php if (isset($errorMessage)) echo '<div class="invalid-feedback">' . $errorMessage . '</div>'; ?>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-primary">Войти</button>
            <p class="mb-0">Еще не зарегистрированы? <a href="register.php">Регистрация</a></p>
        </div>
    </form>
</div>

<footer>
    <div class="container mt-5">
        <div class="text-center text-muted">
            hotspotIlya@gmail.com
        </div>
    </div>
</footer>
</body>
</html>

