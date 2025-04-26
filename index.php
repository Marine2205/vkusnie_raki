<?php
require_once "./php/functions/dataBase.php";

$connection = $db->prepare("SELECT * FROM reviews");
$connection->execute();

$reviews = $connection->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="scss/Index/index.css">
    <link rel="stylesheet" href="scss/index.css">
    <title>vkusnie raki</title>
</head>
<body>
    <?php include "./php/header.php" ?>
    <div class="lending">
        <div class="lending__left">
            <div class="lending__up-content">
                <div class="lending__text lending__text--up">
                    <h1 class="lending__text__h1">
                        Самый свежий улов сочных раков ждет Вас!
                    </h1>
                </div>
            </div>
            <div class='lending__left lending__left--img-wrapper'>
                <div class="lending__left lending__left--img_up"></div>
                <img src="../img/lending_img.jpg" alt="crayfish" class="lending__img"></img>
                <div class="lending__left lending__left--img_down"></div>
            </div>
            <div class="lending__text lending__text--down">
                <h3 class="lending__text__h3">
                    Создай лучшие
                    воспомининания!
                    Холодные дни преврати в
                    душевные посиделки с 
                    друзьями!
                </h3>
                <a href="./production.php">
                    <button class="lending__text lending__text--button" id="button" >
                        Купить
                    </button>
                </a>
            </div>
        </div>
        <div class="lending__reviews-wrapper">
            <div class="lending__reviews-wrapper lending__reviews-wrapper--main">
                <h2 class="lending__reviews-wrapper--h2">Отзывы</h2>
                <div class="lending__reviews-wrapper--reviews">
                <?php foreach ($reviews as $review): ?>
                    <div class="lending__reviews-wrapper--review">
                        <div class="lending__reviews-wrapper--review--header">
                            <img src="../img/user.png" alt="user">
                            <span class="lending__reviews-wrapper--review--header--name">
                                Anon
                            </span>
                            <div class="stars">
                                <?php for ($i = 0; $i < $review['stars']; $i++):?>
                                    <img src="../img/star.png" alt="star">
                                <?php endfor;?>
                            </div>
                        </div>
                        <p class="lending__reviews-wrapper lending__reviews-wrapper--p">
                            <?php echo $review['text']?>
                        </p>
                        <div class="lending__reviews-wrapper--review--footer">
                            <span class="lending__reviews-wrapper--review--footer--date">
                                <?php echo $review['create_at']?>
                            </span>
                        </div>
                    </div>
                <?php endforeach;?>
                </div>
            </div>
        </div>
    </div>
    <?php include "./php/footer.php" ?>
    <script src="./js/app.js"></script>
</body>

</html>