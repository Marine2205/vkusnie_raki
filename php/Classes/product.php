<?php

class Product{
   public function __construct($dataBase) {
      $this->db = $dataBase;
   }
   private $db;
   function getProducts() {
      $stmt = $this->db->prepare("SELECT * FROM products");
      $stmt->execute();
      return $stmt->fetchAll();
   }

   function getCategories(){
      $stmt = $this->db->prepare("SELECT * FROM category");
      $stmt->execute();
      return $stmt->fetchAll();
   }

   function getProductByCategory($category){
      $stmt = $this->db->prepare("SELECT * FROM products WHERE category_id = :category");
      $stmt->execute(["category" => $category]);
      return $stmt->fetchAll();
   }

   function getProductById($id){
      $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
      $stmt->execute(["id" => $id]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
   }

}
?>