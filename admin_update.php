<?php

$id = $_GET['edit'];
$message = [];

if (isset($_POST['update_product'])) {
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_FILES['product_image']['name'];
   $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
   $product_image_folder = 'imagens/' . $product_image;

   if (empty($product_name) || empty($product_price) || empty($product_image)) {
      $message[] = 'Por favor, preencha todos os campos.';
   } else {
      try {
         include 'conexao.php';

         $update_data = "UPDATE products SET name=:product_name, price=:product_price, image=:product_image WHERE id = :id";
         $stmt = $conn->prepare($update_data);
         $stmt->bindParam(':product_name', $product_name);
         $stmt->bindParam(':product_price', $product_price);
         $stmt->bindParam(':product_image', $product_image);
         $stmt->bindParam(':id', $id);
         $stmt->execute();

         if ($stmt->rowCount() > 0) {
            move_uploaded_file($product_image_tmp_name, $product_image_folder);
            header('location: admin.php');
         } else {
            $message[] = 'Não foi possível atualizar o produto.';
         }
      } catch (PDOException $e) {
         $message[] = 'Erro: ' . $e->getMessage();
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv of="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="admstyle.css">
</head>
<body>

<?php
if (isset($message)) {
   foreach ($message as $message) {
      echo '<span class="message">' . $message . '</span>';
   }
}
?>

<div class="container">

<div class="admin-product-form-container centered">

   <?php
   try {
      include 'conexao.php';

      $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
      $stmt->bindParam(':id', $id);
      $stmt->execute();

      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   ?>

   <form action="" method="post" enctype="multipart/form-data">
      <h3 class="title">Atualize o produto</h3>
      <input type="text" class="box" name="product_name" value="<?php echo $row['name']; ?>" placeholder="Digite o nome do produto">
      <input type="number" min="0" class="box" name="product_price" value="<?php echo $row['price']; ?>" placeholder="Digite o preço do produto">
      <input type="file" class="box" name="product_image" accept="image/png, image/jpeg, image/jpg">
      <input type="submit" value="Atualizar produto" name="update_product" class="btn">
      <a href="admin.php" class="btn">Voltar</a>
   </form>

   <?php } 
   } catch (PDOException $e) {
      $message[] = 'Erro: ' . $e->getMessage();
   }
   ?>
   </div>
</div>
</body>
</html>