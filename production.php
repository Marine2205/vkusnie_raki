<?php
// Файл production.php - страница каталога продукции интернет-магазина
// Отвечает за отображение всех доступных продуктов и их категорий

// Инициализация сессии и получение ID сессии
session_start();
$sId = session_id();

// Подключаем необходимые классы и файлы
include './php/Classes/product.php';        // Класс для работы с продуктами
include './php/Classes/cart.php';           // Класс для работы с корзиной
require_once './php/functions/dataBase.php'; // Функции для работы с базой данных

// Создаем экземпляры классов для работы с продуктами и корзиной
$product = new Product($db);
$cart = new Cart($db);

// Получаем все продукты и информацию о корзине
$products = $product->getProducts();         // Все доступные продукты
$productsInCart = $cart->getCart($sId);     // Продукты в корзине текущего пользователя
$cartProductIDs = array_column($productsInCart, 'product'); // ID продуктов в корзине

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

// Обработка фильтрации продуктов по категории
if (isset($_GET['category'])) {
   $cat = $_GET['category'];
   if ($cat === 'all') {
      // Если выбрана категория "Все", получаем все продукты
      $products = $product->getProducts();
   } else {
      // Иначе получаем продукты только выбранной категории
      $products = $product->getProductByCategory($cat);
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="../scss/Production/production.css">
   <link rel="stylesheet" href="../scss/index.css">
   <title>Продукция</title>
</head>
<body>

   <?php include "./php/header.php" ?>
   <main class="main">
      <div class="main__products-wrapper">
      <!-- Фильтр по категориям -->
      <div class="main__product" id="filter">
         <div class="main__product--body">
            <form>
               <!-- Кнопка "Все" для отображения всех категорий -->
               <div class="category">
                  <input type="radio" name="category" id="category-radio" value="all">
                  <label for="cat">Всё</label>
               </div>
               <!-- Динамическое создание радио-кнопок для каждой категории -->
               <?php foreach ($product->getCategories() as $category):?> 
                  <div class="category">
                     <input type="radio" name="category" id="category-radio" value="<?=$category['id']?>">
                     <label for="<?= $category['name']?>"><?= $category['name']?></label>
                  </div>
               <?php endforeach;?>
               <button type="submit">Показать</button>
            </form>
         </div>
      </div>
      <!-- Отображение всех продуктов -->
      <?php foreach ($products as $prod):?>
         <div class="main__product" id="product-<?= $prod['id'] ?>">
            <div class="main__product--body">
               <!-- Изображение продукта -->
               <img src="../img/<?php echo $prod['img']?>" alt="<?php echo $prod['name']?>" class="main__product--img">
               <!-- Название продукта -->
               <h2 class="main__product--name"><?php echo $prod['name']?></h2>
                  <div class="main__product--footer">
                     <?php 
                        // Проверяем, находится ли продукт в корзине
                        if(in_array($prod['id'], $cartProductIDs)){
                           // Специальная обработка для услуги "Варка"
                           if(productMapById('name')[$prod['id']] == 'Варка'){
                              echo "<h3>Раки будут сварены</h3>";
                              echo "<button 
                                       class='main__product--cancel'
                                       id='btnMinus'
                                       onclick='delFromCart(" . $prod['id']. ")'>";
                              echo "Отменить варку";
                              echo "</button>";
                           }
                           // Специальная обработка для услуги "Доставка"
                           elseif(productMapById('name')[$prod['id']] == 'Доставка'){
                              echo "<h3>Раки будут доставлены</h3>";
                              echo "<button 
                                       class='main__product--cancel'
                                       id='btnMinus'
                                       onclick='delFromCart(" . $prod['id']. ")'>";
                              echo "Отменить доставку";
                              echo "</button>";
                           }
                           // Стандартные кнопки управления количеством для обычных продуктов
                           else{
                              echo "<div class='main__product--footer-actions'>";
                                 echo "<button 
                                          type='submit'
                                          class='main__product--footer-actions-btn'
                                          id='btnPlus'
                                          onclick='addToCart(". $prod['id']. ")'>";
                                 echo     "+";
                                 echo "</button>";
                                 // Отображение текущего количества продукта в корзине
                                 for($i = 0; $i < count($productsInCart); $i++){
                                    if ($productsInCart[$i]['product'] == $prod['id']) {
                                       $count = $productsInCart[$i]['quantity'];
                                       echo "<input 
                                                type='number' 
                                                name='prod_quantity' 
                                                class='main__product--footer-actions-input' 
                                                id=\"countInput_" . $prod['id'] . "\"
                                                value='$count'
                                                min='0'
                                                onchange='setTheQuantityInCart(" . $prod['id'] . ", this.value)'";
                                       echo ">";
                                    }
                                 }
                                 echo "<button 
                                          class='main__product--footer-actions-btn' 
                                          id='btnMinus' 
                                          onclick='minusToCart(". $prod['id'] .")'>";
                                 echo "-";
                                 echo "</button>";
                                 echo "</div>";
                           }
                        }
                        // Если продукт не в корзине, показываем кнопку "В корзину"
                        else{
                           echo "<form>";
                           echo "<button 
                                    type='submit' 
                                    class='main__product--btn' 
                                    id='button' 
                                    onclick='saveToCart(". $prod['id']. ")'>";
                           echo "В корзину";
                           echo"</button>";
                           echo "</form>";
                           // Отображение цены в зависимости от типа продукта
                           if (productMapById('name')[$prod['id']] == 'Доставка'){
                              echo "";
                           }
                           elseif(productMapById('name')[$prod['id']] == 'Варка'){
                              echo "<p class='main__product--price'> " . $prod['price'] . " руб. за 1кг</p>";
                           } else {
                           echo "<p class='main__product--price'> " . $prod['price'] . " руб.</p>";
                           }
                        }
                     ?>
                  </div>
            </div>
         </div>
         <?php endforeach;?>
      </div>
   </main>
   <?php include "./php/footer.php" ?>
   <script src="../js/product.js"></script>
</body>
</html>