<?php
// edit.php
include_once 'Database.php';
include_once 'Book.php';

$database = new Database();
$db = $database->getConnection();

$book = new Book($db);

$bookId = isset($_GET['id']) ? $_GET['id'] : die('ID inválido');

$book->readOne($bookId);

if($_POST) {
    $book->setTitle($_POST['title']);
    $book->setAuthor($_POST['author']);
    $book->setPages($_POST['pages']);
    $book->setGenre($_POST['genre']);
    $book->setYear($_POST['year']);
    
    if($book->update()) {
        header("Location: index.php?status=updated");
    } else {
        echo "<div class='alert alert-danger'>Não foi possível atualizar o registro.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Livro - Book Storage</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="assets/images/logo.png" alt="logo" class="logo-placeholder">
            <h1>Book Storage</h1>
        </div>
    </header>

    <div class="container">
        <div class="header-actions">
            <h2>Editar Livro</h2>
            <a href="index.php" class="btn">Voltar</a>
        </div>

        <div class="card">
            <form action="edit.php?id=<?php echo $book->getId(); ?>" method="POST">
                <div class="form-group">
                    <label for="title">Título*</label>
                    <input type="text" id="title" name="title" value="<?php echo $book->getTitle(); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="author">Autor*</label>
                    <input type="text" id="author" name="author" value="<?php echo $book->getAuthor(); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="pages">Número de Páginas*</label>
                    <input type="number" id="pages" name="pages" min="1" value="<?php echo $book->getPages(); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="genre">Gênero*</label>
                    <input type="text" id="genre" name="genre" value="<?php echo $book->getGenre(); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="year">Ano de Publicação*</label>
                    <input type="number" id="year" name="year" min="1000" max="<?php echo date('Y'); ?>" value="<?php echo $book->getYear(); ?>" required>
                </div>
                
                <button type="submit" class="btn">Atualizar Livro</button>
            </form>
        </div>
    </div>
</body>
</html>