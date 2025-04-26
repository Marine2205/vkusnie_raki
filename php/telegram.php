<?php
// Подключаем необходимые классы и файлы
include './Classes/cart.php';           // Класс для работы с корзиной
require_once './functions/dataBase.php'; // Функции для работы с базой данных
include './Classes/product.php';        // Класс для работы с продуктами

// Инициализация сессии и получение ID сессии
session_start();
$sId = session_id();

// Создаем экземпляры классов для работы с корзиной и продуктами
$cart = new Cart($db);
$products = new Product($db);

// Получаем все продукты и содержимое корзины
$allProducts = $products->getProducts();
$cartProducts = $cart->getCart($sId);

// Настройки Telegram-бота
$bot_token = '7472349813:AAF2SfCQmQ-1T8m5tyJEDbHMNI-MkCdZi3M'; // Токен бота
$chat_id = '-4771652627'; // ID чата для отправки сообщений

// Получение данных из формы заказа с проверкой наличия
$name = isset($_POST['name']) ? $_POST['name'] : 'Не указано';
$phone = isset($_POST['phone']) ? $_POST['phone'] : 'Не указано';
$comment = isset($_POST['comment']) ? $_POST['comment'] : 'Не указано';
$address = isset($_POST['address']) ? $_POST['address'] : 'Не указано';

// Формирование массива с информацией о покупателе
$arr = array(
   "Имя покупателя: " => $name,
   "Номер телефона: " => $phone,
   "Комментарий к заказу: " => $comment,
   "Адрес: " => $address
);

// Формирование текста сообщения с информацией о покупателе
$txt = '';
foreach ($arr as $key => $value) {
   $txt .= "<b>" . $key . "</b>" . $value . "%0A"; // %0A - символ переноса строки в URL
};

// Инициализация переменных для расчета стоимости варки
$totalCrayfishWeight = 0; // Общий вес раков
$boilingCost = 0;         // Стоимость варки

// Подсчет общего веса раков в заказе
foreach ($cartProducts as $cartItem) {
    $productInfo = $products->getProductById($cartItem['product']);
    
    // Проверяем, принадлежит ли продукт категории 2 (раки)
    if ($productInfo['category_id'] == 2) {
        $totalCrayfishWeight += $cartItem['quantity'];
    }
}

// Расчет стоимости варки, если есть раки в заказе
if ($totalCrayfishWeight > 0) {
    $boilingCost = 100 * $totalCrayfishWeight; // 100 рублей за килограмм
}

// Инициализация переменных для формирования деталей заказа
$order = '';
$totalSum = 0;

// Добавление информации о каждом товаре в заказ
foreach ($cartProducts as $cartItem) {
    $productInfo = $products->getProductById($cartItem['product']);
    
    // Пропускаем услуги "Варка" и "Доставка" в основном списке товаров
    if ($productInfo['name'] == 'Варка') {
        continue;
    }
    if ($productInfo['name'] == 'Доставка') {
        continue;
    }
    
    // Расчет суммы за товар и добавление к общей сумме
    $itemSum = $productInfo['price'] * $cartItem['quantity'];
    $totalSum += $itemSum;
    
    // Формирование строки с информацией о товаре
    $order .= "<b>Товар:</b> " . $productInfo['name'] .
            " | <b>Количество:</b> " . $cartItem['quantity'] .
            " | <b>Цена:</b> " . $productInfo['price'] . " руб." .
            " | <b>Сумма:</b> " . $itemSum . " руб.%0A";
}

// Добавление стоимости варки к заказу, если она есть
if ($boilingCost > 0) {
    $totalSum += $boilingCost;
    $order .= "<b>Варка раков:</b> " . $totalCrayfishWeight . " кг. | <b>Стоимость варки:</b> " . $boilingCost . " руб.%0A";
}

// Добавление общей суммы заказа
$order .= "%0A<b>Общая сумма заказа:</b> " . $totalSum . " руб.";

// Добавление деталей заказа к сообщению
$txt .= "%0A<b>Заказ:</b>%0A" . $order;

// Отправка сообщения в Telegram через API
$sendToTelegram = fopen(
    "https://api.telegram.org/bot{$bot_token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text={$txt}",
    'r');

// Обработка результата отправки
if($sendToTelegram){
    // Если сообщение отправлено успешно, очищаем корзину и перенаправляем на страницу благодарности
    $cart->clearCart($sId);
    header('Location: ../thank-you.php');
}
else{
    // Если произошла ошибка, выводим сообщение
    echo "Ошибка";
}

?>