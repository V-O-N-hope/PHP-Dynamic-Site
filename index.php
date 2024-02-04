<?php
require_once('includes/header.php');
require_once('includes/bd_data.php');

// Функция для определения операционной системы пользователя
function getOS() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    $osArray = array(
        '/windows nt 10/i'      => 'Windows 10 / 11',
        '/windows nt 6.3/i'     => 'Windows 8.1',
        '/windows nt 6.2/i'     => 'Windows 8',
        '/windows nt 6.1/i'     => 'Windows 7',
        '/windows nt 6.0/i'     => 'Windows Vista',
        '/windows nt 5.2/i'     => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     => 'Windows XP',
        '/windows xp/i'         => 'Windows XP',
        '/windows nt 5.0/i'     => 'Windows 2000',
        '/windows me/i'         => 'Windows ME',
        '/win98/i'              => 'Windows 98',
        '/win95/i'              => 'Windows 95',
        '/win16/i'              => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i'        => 'Mac OS 9',
        '/linux/i'              => 'Linux',
        '/ubuntu/i'             => 'Ubuntu',
        '/iphone/i'             => 'iPhone',
        '/ipod/i'               => 'iPod',
        '/ipad/i'               => 'iPad',
        '/android/i'            => 'Android',
        '/blackberry/i'         => 'BlackBerry',
        '/webos/i'              => 'Mobile'
    );

    foreach ($osArray as $regex => $value) {
        if (preg_match($regex, $userAgent)) {
            return $value;
        }
    }

    return 'Unknown';
}

// Проверка наличия значения 'key' в куках
if (isset($_COOKIE['key'])) {
    $uniqKey = $_COOKIE['key'];

    // Проверка соответствия уникального ключа в базе данных
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $query = "SELECT id, email FROM users WHERE uniqKey = '$uniqKey'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userId = $row['id'];
        $email = $row['email'];
    } else {
        setcookie('key', '', time() - 3600); // Удаление некорректного ключа из кук
        $email = "unregistered";
    }

    // Получение данных о дате входа, операционной системе и IP-адресе
    $loginDate = date('Y-m-d H:i:s');
    $os = getOS();
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    // Добавление значения в базу данных
    $insertQuery = "INSERT INTO user_logins (email, login_date, os, ip_address) VALUES ('$email', '$loginDate', '$os', '$ipAddress')";
    $conn->query($insertQuery);
    $conn->close();
} else {
    $email = "unregistered";
}

?>

<div class="container">
    <div class="row">
        <div class="col-lg-6">
            <img src="logo/company-image.jpg" alt="Company Image" class="img-fluid">
        </div>
        <div class="col-lg-6">
            <h2 class="text-primary">Доставка еды с CDeliv</h2>
            <p>CDeliv - это лучшая компания по доставке еды прямо к вашей двери. Мы предлагаем широкий выбор блюд разных кухонь мира, от свежих салатов до горячих пицц и аппетитных десертов.</p>
            <p>Наши курьеры гарантируют быструю и надежную доставку, а мы ставим на первое место качество и свежесть каждого блюда. Мы бережно упаковываем все заказы, чтобы они пришли к вам в идеальном состоянии.</p>
            <p>Заказывайте еду у нас и наслаждайтесь вкусом прямо у себя дома, в офисе или в любом другом месте. Мы гарантируем удобство, надежность и вкус в каждом блюде!</p>
            <a href="catalog.php" class="btn btn-primary">Смотреть меню</a>
        </div>
    </div>
</div>

<?php
require_once('includes/footer.php');
?>
