<?php
// index.php
include_once 'Database.php';
include_once 'Book.php';

$database = new Database();
$db = $database->getConnection();

$book = new Book($db);
$stmt = $book->readAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Storage</title>
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
            <h2>Meus Livros Lidos</h2>
            <a href="create.php" class="btn">+ Adicionar Livro</a>
        </div>

        <div class="card">
            <?php if($stmt->rowCount() > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Páginas</th>
                            <th>Gênero</th>
                            <th>Ano</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['author']); ?></td>
                                <td><?php echo htmlspecialchars($row['pages']); ?></td>
                                <td><?php echo htmlspecialchars($row['genre']); ?></td>
                                <td><?php echo htmlspecialchars($row['year']); ?></td>
                                <td class="actions">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Editar</a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div>📖</div>
                    <h3>Nenhum livro cadastrado</h3>
                    <p>Comece adicionando seu primeiro livro!</p>
                    <a href="create.php" class="btn">Adicionar Livro</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>