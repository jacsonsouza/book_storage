<?php
// create.php
include_once 'Database.php';
include_once 'Book.php';

$database = new Database();
$db = $database->getConnection();

$book = new Book($db);

if($_POST) {
    $book->title = $_POST['title'];
    $book->author = $_POST['author'];
    $book->pages = $_POST['pages'];
    $book->genre = $_POST['genre'];
    $book->year = $_POST['year'];
    
    if($book->create()) {
        header("Location: index.php?status=created");
    } else {
        echo "<div class='alert alert-danger'>Não foi possível criar o registro.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Livro - Book Storage</title>
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
            <h2>Adicionar Novo Livro</h2>
            <a href="index.php" class="btn">Voltar</a>
        </div>

        <div class="card">
            <form action="create.php" method="POST">
                <div class="form-group">
                    <label for="title">Título*</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="author">Autor*</label>
                    <input type="text" id="author" name="author" required>
                </div>
                
                <div class="form-group">
                    <label for="pages">Número de Páginas*</label>
                    <input type="number" id="pages" name="pages" min="1" required>
                </div>
                
                <div class="form-group">
                    <label for="genre">Gênero*</label>
                    <input type="text" id="genre" name="genre" required>
                </div>
                
                <div class="form-group">
                    <label for="year">Ano de Publicação*</label>
                    <input type="number" id="year" name="year" min="1000" max="<?php echo date('Y'); ?>" required>
                </div>
                
                <button type="submit" class="btn">Salvar Livro</button>
            </form>
        </div>
    </div>
</body>
</html>