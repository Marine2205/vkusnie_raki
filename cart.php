<?php
/* Файл cart.php - основной файл корзины интернет-магазина
Отвечает за отображение и управление корзиной покупок */
// Подключаем необходимые классы и файлы
include './php/Classes/cart.php';        // Класс для работы с корзиной
require_once './php/functions/dataBase.php';  // Функции для работы с базой данных
include './php/Classes/product.php';     // Класс для работы с продуктами

// Инициализация сессии и получение ID сессии
session_start();
$sId = session_id();

// Создаем экземпляр класса корзины и получаем все товары в корзине
$cart = new Cart($db);
$productsInCart = $cart->getCart($sId);

// Создаем экземпляр класса продукта и получаем все доступные продукты
$product = new Product($db);
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

// Проверяем наличие услуги доставки в корзине
$hasDelivery = false;
foreach ($productsInCart as $item) {
   if (productMapById('name')[$item['product']] === 'Доставка') {
      $hasDelivery = true;
      break;
   }
}

// Подсчет общего количества продуктов категории 2 (вероятно, раков) в корзине
$totalCategory2Products = 0;
foreach ($productsInCart as $item) {
   $productInfo = $product->getProductById($item['product']);
   if ($productInfo['category_id'] == 2) {
      $totalCategory2Products += $item['quantity'];
   }
}

// Обработка добавления товара в корзину
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cart'])) {
   $productID = $_POST['add_cart'];

   // Добавляем продукт в корзину
   $result = $cart->addProductToCart($sId, $productID);

   // Обновляем количество продуктов категории 2
   $productsInCart = $cart->getCart($sId);
   $totalCategory2Products = 0;
   foreach ($productsInCart as $item) {
      $productInfo = $product->getProductById($item['product']);
      if ($productInfo['category_id'] == 2) {
         $totalCategory2Products += $item['quantity'];
      }
   }

   // Отправляем JSON-ответ
   header('Content-Type: application/json');
   echo json_encode([
      'status' => 'success',
      'message' => 'Продукт успешно добавлен в корзину',
      'productID' => $productID,
      'totalCategory2Products' => $totalCategory2Products
   ]);
   exit;
}

// Обработка удаления товара из корзины
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['minus_cart'])) {
   $productID = $_POST['minus_cart'];
   
   // Уменьшаем количество продукта в корзине
   $result = $cart->minusProductAtCart($sId, $productID);

   // Обновляем количество продуктов категории 2
   $productsInCart = $cart->getCart($sId);
   $totalCategory2Products = 0;
   foreach ($productsInCart as $item) {
      $productInfo = $product->getProductById($item['product']);
      if ($productInfo['category_id'] == 2) {
         $totalCategory2Products += $item['quantity'];
      }
   }

   // Отправляем JSON-ответ
   header('Content-Type: application/json');
   echo json_encode([
      'status' => 'success',
      'message' => 'Продукт успешно удален из корзины',
      'productID' => $productID,
      'totalCategory2Products' => $totalCategory2Products
   ]);
   exit;
}

// Обработка установки количества товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_cart'])) {
   $productID = $_POST['set_cart'];
   $quantity = $_POST['quantity'];
   
   // Устанавливаем новое количество продукта
   $result = $cart->setProductQuantity($sId, $productID, $quantity);

   // Обновляем количество продуктов категории 2
   $productsInCart = $cart->getCart($sId);
   $totalCategory2Products = 0;
   foreach ($productsInCart as $item) {
      $productInfo = $product->getProductById($item['product']);
      if ($productInfo['category_id'] == 2) {
         $totalCategory2Products += $item['quantity'];
      }
   }

   // Отправляем JSON-ответ
   header('Content-Type: application/json');
   echo json_encode([
      'status' => 'success',
      'message' => 'Продукт успешно удален из корзины',
      'productID' => $productID,
      'totalCategory2Products' => $totalCategory2Products
   ]);
   exit;
}

// Обработка отправки формы заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
   $name = $_POST['name'];
   $phone = $_POST['phone'];
   $comment = $_POST['comment'];
   $address = isset($_POST['address']) ? $_POST['address'] : '';
   exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="scss/index.css">
   <link rel="stylesheet" href="scss/Cart/cart.css">
   <title>Ваш садок</title>
</head>
<body>
   <?php include './php/header.php' ?>
   <main class="cart-main">
      <?php if(count($productsInCart) !== 0):?>
      <div class="cart-main__cart-products">
         <h1>Садок</h1>
         <?php foreach ($productsInCart as $product): ?>
            <div class="cart-main__product">
               <div class="cart-main__product--body">
                  <!-- Отображение изображения продукта -->
                  <img src="./img/<?= productMapById('img')[$product['product']]?>" class="cart-main__product--img">
                  <!-- Отображение названия продукта -->
                  <h2 class="cart-main__product--name">
                     <?= productMapById('name')[$product['product']]?>
                  </h2>
                  <div class="cart-main__product--footer">
                     <div class="cart-main__product--footer-sum">
                        <?php 
                           // Специальная обработка для услуги "Варка"
                           if(productMapById('name')[$product['product']] == 'Варка'){
                              echo "<p id='total-category2-products'>" . $totalCategory2Products * 100 . " ₽". "</p>";
                           } else {
                              echo "<p id='footer-sum-" . $product['product'] . "' 
                                    data-price='" . productMapById('price')[$product['product']] . "'></p>";
                           }
                        ?>
                     </div>
                     <?php 
                        // Различные варианты отображения кнопок управления в зависимости от типа продукта
                        if(productMapById('name')[$product['product']] == 'Варка'){
                           echo "<h3>Раки будут сварены</h3>";
                           echo "<button 
                                    class='cart-main__product--footer-actions-btn cancel'
                                    id='btnMinus'
                                    onclick='delFromCart(" . $product['product']. ")'>";
                           echo "Отменить варку";
                           echo "</button>";
                        }
                        elseif(productMapById('name')[$product['product']] == 'Доставка'){
                           echo "<h3>Раки будут доставлены</h3>";
                           echo "<button 
                                    class='cart-main__product--footer-actions-btn cancel'
                                    id='btnMinus'
                                    onclick='delFromCart(" . $product['product']. ")'>";
                           echo "Отменить доставку";
                           echo "</button>";
                        }
                        else{   
                           // Стандартные кнопки управления количеством
                           echo "<div class='cart-main__product--footer-actions'>";
                              echo "<button 
                                       type='submit'
                                       class='cart-main__product--footer-actions-btn'
                                       id='btnPlus'
                                       onclick='addToCart(". $product['product']. ")'>";
                              echo     "+";
                              echo "</button>";
                              echo "<input 
                                       type='number' 
                                       class='cart-main__product--footer-actions-input' 
                                       min='0'
                                       value='" . $product['quantity'] . "'
                                       id='countInput_" . $product['product'] . "'
                                       onchange='setTheQuantityInCart(" . $product['product'] . ", this.value)'
                                       >";
                              echo "<button 
                                       class='cart-main__product--footer-actions-btn' 
                                       id='btnMinus' 
                                       onclick='minusToCart(". $product['product'] .")'>";
                              echo "-";
                              echo "</button>";
                              echo "</div>";
                        }
                     ?>
                  </div>
               </div>
            </div>
         <?php endforeach; ?>
      </div>
      <!-- Секция с итоговой суммой и формой оформления заказа -->
      <div class="cart-main__section">
         <div class="cart-main__section--result">
            <h2>Итого</h2>
            <div class="cart-main__section--result-sum">
               <p>Сумма заказа: <span id="total-sum">0</span> ₽</p>
            </div>
         </div>
         <div class="cart-main__section--form-wrapper">
            <h2>Оформление заказа</h2>
            <form class="cart-main__section--form" method="POST" action="php/telegram.php">
               <div class="form-group">
                  <label for="name">Имя</label>
                  <input type="text" id="name" name="name" required>
               </div>
               <div class="form-group">
                  <label for="phone">Номер телефона</label>
                  <input type="tel" id="phone" name="phone" required>
               </div>
               <?php if ($hasDelivery): ?>
               <div class="form-group">
                  <label for="address">Адрес доставки</label>
                  <textarea id="address" name="address" rows="3" required></textarea>
               </div>
               <?php endif; ?>
               <div class="form-group">
                  <label for="comment">Комментарий к заказу</label>
                  <textarea id="comment" name="comment" rows="3"></textarea>
               </div>
               <button type="submit" name="submit_order" class="submit-btn">Оформить заказ</button>
            </form>
         </div>
      </div>
      <?php else:?>
         <h1 class="h1-empty">Садок пуст</h1>
      <?php endif;?>
   </main>
   <?php include './php/footer.php' ?>
   <script src="./js/cart.js"></script>
</body>
</html>