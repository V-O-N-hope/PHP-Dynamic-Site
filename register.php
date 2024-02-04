<?php
require_once('includes/header.php');
require_once('includes/bd_data.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Проверяем подключение к базе данных
    if ($conn->connect_error) {
        die("Ошибка подключения к базе данных: " . $conn->connect_error);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $shouldSend = isset($_POST['should_send']) ? 1 : 0;

    // Проверяем корректность email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Некорректный формат email.';
    } else {
        // Проверяем, существует ли уже пользователь с таким email
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $errorMessage = 'Пользователь с таким email уже зарегистрирован.';
        } elseif ($password !== $confirmPassword) {
            $errorMessage = 'Пароли не совпадают.';
        } else {
            // Генерируем уникальный ключ (uniqKey) на основе хеша из почты, пароля и текущего времени
            $uniqKey = md5($email . $password . time());

            // Добавляем пользователя в БД с уникальным ключом
            $query = "INSERT INTO users (email, password, shouldSend, uniqKey) VALUES ('$email', '$password', '$shouldSend', '$uniqKey')";
            if (mysqli_query($conn, $query)) {
                $conn->close();
                // Перенаправляем на страницу входа
                echo '<script>window.location.href = "me.php";</script>';
                exit();
            } else {
                $errorMessage = 'Ошибка при добавлении пользователя: ' . mysqli_error($conn);
            }
        }
    }
}

?>

<div class="container">
    <h1>Регистрация</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control <?php if (isset($errorMessage)) echo 'is-invalid'; ?>" id="email" name="email" placeholder="Введите email" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
            <?php if (isset($errorMessage)) echo '<div class="invalid-feedback">' . $errorMessage . '</div>'; ?>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <div class="input-group">
                <input type="password" class="form-control <?php if (isset($errorMessage)) echo 'is-invalid'; ?>" id="password" name="password" placeholder="Введите пароль" required>
                <button class="btn btn-outline-secondary" type="button" id="showPassword" onclick="togglePasswordVisibility('password')">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Подтверждение пароля</label>
            <div class="input-group">
                <input type="password" class="form-control <?php if (isset($errorMessage)) echo 'is-invalid'; ?>" id="confirm_password" name="confirm_password" placeholder="Подтвердите пароль" required>
                <button class="btn btn-outline-secondary" type="button" id="showConfirmPassword" onclick="togglePasswordVisibility('confirm_password')">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <?php if (isset($errorMessage)) echo '<div class="invalid-feedback">' . $errorMessage . '</div>'; ?>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="should_send" name="should_send">
            <label class="form-check-label" for="should_send">Отправлять рассылку</label>
        </div>
        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
    </form>
</div>

<script>
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(`show${inputId.charAt(0).toUpperCase() + inputId.slice(1)}`);
        if (input.type === 'password') {
            input.type = 'text';
            button.innerHTML = '<i class="bi bi-eye-slash"></i>';
        } else {
            input.type = 'password';
            button.innerHTML = '<i class="bi bi-eye"></i>';
        }
    }
</script>

<?php

require_once('includes/footer.php');

?>
