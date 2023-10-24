<?php
$servername = '127.0.0.1';
$username = 'root';
$password = "";
$dbname = 'produtobd';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão estabelecida com sucesso!";
    

    $createTableQuery = "
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        image VARCHAR(255) NOT NULL
    )";

    $conn->exec($createTableQuery);
    echo "Tabela criada com sucesso!";
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}

$message = [];

if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'imagens/' . $product_image;

    if (empty($product_name) || empty($product_price) || empty($product_image)) {
        $message[] = 'Por favor, preencha todos os campos.';
    } else {
        try {
            $insert = "INSERT INTO products(name, price, image) VALUES(:product_name, :product_price, :product_image)";
            $stmt = $conn->prepare($insert);
            $stmt->bindParam(':product_name', $product_name);
            $stmt->bindParam(':product_price', $product_price);
            $stmt->bindParam(':product_image', $product_image);
            $stmt->execute();
            move_uploaded_file($product_image_tmp_name, $product_image_folder);
            $message[] = 'Novo produto adicionado com sucesso';
        } catch (PDOException $e) {
            $message[] = 'Não foi possível adicionar o produto: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header('location: admin.php');
    } catch (PDOException $e) {
        $message[] = 'Não foi possível excluir o produto: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pagina de produtos</title>
  <link rel="stylesheet" href="admstyle.css">

</head>
<body>

<?php
if (!empty($message)) {
    foreach ($message as $msg) {
        echo '<span class="message">' . $msg . '</span>';
    }
}
?>

<div class="container">
  <div class="admin-product-form-container">
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
      <h3>Adicionar um novo produto</h3>
      <input type="text" placeholder="Coloque o nome do produto" name="product_name" class="box">
      <input type="number" placeholder="Coloque o preço do produto" name="product_price" class="box">
      <input type="file" accept="image/png, image/jpeg, image/jpg, image/webp" name="product_image" class="box">
      <input type="submit" class="btn" name="add_product" value="Adicionar Produto">
    </form>
  </div>

  <div class="product-display">
    <table class="product-display-table">
      <thead>
      <tr>
          <th>Imagem do produto</th>
          <th>Nome da Action Figure</th>
          <th>Preço do Produto</th>
          <th>Opções</th>
      </tr>
      </thead>
      <?php
      $select = $conn->query("SELECT * FROM products");
      while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <tr>
          <td><img src="imagens/<?php echo $row['image']; ?>" height="100" alt=""></td>
          <td><?php echo $row['name']; ?></td>
          <td>$<?php echo $row['price']; ?>/-</td>
          <td>
            <a href="admin_update.php?edit=<?php echo $row['id']; ?>" class="btn"> <i class="fas fa-edit"></i> editar </a>
            <a href="admin.php?delete=<?php echo $row['id']; ?>" class="btn"> <i class="fas fa-trash"></i> deletar </a>
          </td>
      </tr>
      <?php } ?>
    </table>
  </div>
</div>

</body>
</html>