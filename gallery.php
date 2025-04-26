<?php
require_once './php/functions/dataBase.php';

$images = array();
$stmt = $db->prepare("SELECT * FROM gallery");
$stmt->execute();
$images = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="../scss/index.css">
   <link rel="stylesheet" href="../scss/Gallery/gallery.css">
   <title>Галерея</title>
</head>
<body>
   <?php include './php/header.php'?>
   <main class="gallery-main">
      <?php foreach ($images as $img): ?>
         <div class="gallery-main__image-wrapper">
            <img src="../img/gallery/<?= $img["img"] ?>" alt="<?= $img["img"] ?>" class="gallery-main__image-wrapper--img">
            <div class="gallery-main__image-wrapper--info"">
               <div class="gallery-main__image-wrapper--description">
                  <?= $img["description"] ?>
               </div>
            </div>
         </div>
      <?php endforeach; ?>
   </main>
   <?php include './php/footer.php'?>
   </body>
</html>