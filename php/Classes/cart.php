<?php 

class Cart{
   public function __construct($dataBase) {
      $this->db = $dataBase;
   }

   private $db;

   function getCart($sId)
   {
      $stmt = $this->db->prepare("SELECT * FROM cart where sessionID = :sId");
      $stmt->execute([':sId' => $sId]);
      return $stmt->fetchAll();
   }

   
   function toCart($sId, $productID)
   {
      $stmt = $this->db->prepare("INSERT INTO cart (`sessionID`, `product`, `quantity`) VALUES ( :sId, :productID, 1);");
      $stmt->execute(["sId" => $sId, "productID" => $productID]);
      return $stmt->fetchAll();
   }

   function addProductToCart($sId, $productID){
      $stmt = $this->db->prepare("UPDATE cart SET quantity = quantity + 1 WHERE sessionID = :sessionID and product = :productID");
      $stmt->execute(["productID" => $productID, "sessionID" => $sId]);
   }

   function minusProductAtCart($sId, $productID){
      // Сначала уменьшаем количество на 1
      $stmt = $this->db->prepare("UPDATE cart SET quantity = quantity - 1 WHERE sessionID = :sessionID and product = :productID");
      $stmt->execute(["productID" => $productID, "sessionID" => $sId]);
      
      // Проверяем, стало ли количество равным нулю или меньше
      $stmt = $this->db->prepare("SELECT quantity FROM cart WHERE sessionID = :sessionID and product = :productID");
      $stmt->execute(["productID" => $productID, "sessionID" => $sId]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      
      // Если количество равно нулю или меньше, удаляем товар из корзины
      if ($result && $result['quantity'] <= 0) {
         $stmt = $this->db->prepare("DELETE FROM cart WHERE sessionID = :sessionID and product = :productID");
         $stmt->execute(["productID" => $productID, "sessionID" => $sId]);
      }
   }

   function setProductQuantity($sId, $productID, $quantity){
      // Устанавливаем новое количество
      $stmt = $this->db->prepare("UPDATE cart SET quantity = :quantity WHERE sessionID = :sessionID and product = :productID");
      $stmt->execute(["productID" => $productID, "sessionID" => $sId, "quantity" => $quantity]);
      
      // Если количество равно нулю или меньше, удаляем товар из корзины
      if ($quantity <= 0) {
         $stmt = $this->db->prepare("DELETE FROM cart WHERE sessionID = :sessionID and product = :productID");
         $stmt->execute(["productID" => $productID, "sessionID" => $sId]);
      }
   }

   function clearCart($sId){
      $stmt = $this->db->prepare("DELETE FROM cart WHERE sessionID = :sessionID");
      $stmt->execute(["sessionID" => $sId]);
      return true;
   }
}

?>