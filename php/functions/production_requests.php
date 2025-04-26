
<?php
/* Файл production_requests.php - обработчик AJAX-запросов для страницы продукции
Отвечает за обработку различных действий с продуктами (добавление в корзину, изменение количества и т.д.) */

// Инициализация сессии и получение ID сессии
session_start();
$sId = session_id();

// Подключаем необходимые классы и файлы
include '../Classes/product.php';        // Класс для работы с продуктами
include '../Classes/cart.php';           // Класс для работы с корзиной
require_once './dataBase.php';           // Функции для работы с базой данных

// Создаем экземпляры классов для работы с продуктами и корзиной
$product = new Product($db);
$cart = new Cart($db);

// Получаем все продукты и содержимое корзины
$products = $product->getProducts();
$productsInCart = $cart->getCart($sId);

// Получаем все продукты для функции productMapById
$allProducts = $product->getProducts();

/**
 * Функция для создания массива продуктов, индексированного по ID
 * @param string $value - ключ для значения, которое нужно получить из продукта
 * @return array - ассоциативный массив продуктов
 */
function productMapById($value)
{
   global $allProducts;

   $productsMap = array();
   foreach ($allProducts as $prod) {
      $productsMap[$prod['id']] = $prod[$value];
   }
   return $productsMap;
}

// Обработка добавления продукта в корзину (первое добавление)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['to_cart'])) {
   $productID = $_POST['to_cart'];

   // Добавляем продукт в корзину
   $result = $cart->toCart($sId, $productID);

   // Отправляем JSON-ответ об успешном добавлении
   header('Content-Type: application/json');
   echo json_encode([
      'status' => 'success',
      'message' => 'Продукт успешно добавлен в корзину',
      'productID' => $productID
   ]);
   exit;
}

// Обработка увеличения количества продукта в корзине
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cart'])) {
   $productID = $_POST['add_cart'];

   // Увеличиваем количество продукта в корзине
   $result = $cart->addProductToCart($sId, $productID);

   // Отправляем JSON-ответ об успешном добавлении
   header('Content-Type: application/json');
   echo json_encode([
      'status' => 'success',
      'message' => 'Продукт успешно добавлен в корзину',
      'productID' => $productID
   ]);
   exit;
}

// Обработка уменьшения количества продукта в корзине
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['minus_cart'])) {
   $productID = $_POST['minus_cart'];
   
   // Уменьшаем количество продукта в корзине
   $result = $cart->minusProductAtCart($sId, $productID);

   // Отправляем JSON-ответ об успешном удалении
   header('Content-Type: application/json');
   echo json_encode([
      'status' => 'success',
      'message' => 'Продукт успешно удален из корзины',
      'productID' => $productID
   ]);
   exit;
}

// Обработка установки конкретного количества продукта в корзине
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_cart'])) {
   $productID = $_POST['set_cart'];
   $quantity = $_POST['quantity'];
   
   // Устанавливаем новое количество продукта в корзине
   $result = $cart->setProductQuantity($sId, $productID, $quantity);

   // Отправляем JSON-ответ об успешном изменении
   header('Content-Type: application/json');
   echo json_encode([
      'status' => 'success',
      'message' => 'Продукт успешно удален из корзины',
      'productID' => $productID
   ]);
   exit;
}

// Обработка удаления продукта из корзины
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['del_product'])) {
   $productID = $_POST['del_product'];
   $productName = productMapById('name')[$productID];
   
   // Находим все продукты с таким же именем в корзине и удаляем их
   foreach ($productsInCart as $item) {
      $productInfo = $product->getProductById($item['product']);
      if ($productInfo['name'] == $productName) {
         // Устанавливаем количество в 0, что фактически удаляет продукт из корзины
         $cart->setProductQuantity($sId, $item['product'], 0);
         break;
      }
   }
   
   // Отправляем JSON-ответ об успешном удалении
   header('Content-Type: application/json');
   echo json_encode([
      'status' => 'success',
      'message' => 'товар успешно отменен',
      'productID' => $productID,
   ]);
   exit;
}

?>