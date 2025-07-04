<?php
/**
 * Class Book
 * 
 * Representa um livro e encapsula operações básicas de CRUD.
 * Projeto simples, mas com princípios básicos de OO: encapsulamento,
 * tipagem, sanitização e clareza de métodos.
 */
class Book
{
    /** @var PDO */
    private PDO $conn;
    private const TABLE = 'books';

    private ?int $id = null;
    private string $title;
    private string $author;
    private int $pages;
    private string $genre;
    private int $year;

    /**
     * Book constructor.
     * @param PDO $db Conexão PDO
     */
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Sanitiza valor para evitar XSS/injeção de HTML
     */
    private function sanitize(string $value): string
    {
        return htmlspecialchars(strip_tags($value));
    }

    // ----------------- Getters e Setters -----------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $this->sanitize($title);
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $this->sanitize($author);
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function setPages(int $pages): void
    {
        $this->pages = $pages;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $this->sanitize($genre);
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    // ----------------- Operações CRUD -----------------

    public static function find(int $id, PDO $db): ?Book
    {
        $book = new Book($db);
        $book->readOne($id);
        return $book;
    }

    /**
     * Insere um novo livro no banco
     * @return bool sucesso
     */
    public function create(): bool
    {
        $sql = "INSERT INTO " . self::TABLE . "
                (title, author, pages, genre, year)
                VALUES
                (:title, :author, :pages, :genre, :year)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':title', $this->title);
        $stmt->bindValue(':author', $this->author);
        $stmt->bindValue(':pages', $this->pages, PDO::PARAM_INT);
        $stmt->bindValue(':genre', $this->genre);
        $stmt->bindValue(':year', $this->year, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Retorna todos os livros (PDOStatement para iteração)
     */
    public function readAll(): PDOStatement
    {
        $sql = "SELECT id, title, author, pages, genre, year
                FROM " . self::TABLE;
        $stmt = $this->conn->query($sql);
        return $stmt;
    }

    /**
     * Carrega dados do livro no objeto a partir do ID
     * @param int $id
     * @return bool sucesso
     */
    public function readOne(int $id): bool
    {
        $sql = "SELECT title, author, pages, genre, year
                FROM " . self::TABLE . "
                WHERE id = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if (! $stmt->execute()) {
            return false;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (! $row) {
            return false;
        }

        $this->id     = $id;
        $this->title  = $row['title'];
        $this->author = $row['author'];
        $this->pages  = (int)$row['pages'];
        $this->genre  = $row['genre'];
        $this->year   = (int)$row['year'];

        return true;
    }

    /**
     * Atualiza registro existente
     * @return bool sucesso
     */
    public function update(): bool
    {
        if ($this->id === null) {
            throw new InvalidArgumentException("ID do livro não definido");
        }

        $sql = "UPDATE " . self::TABLE . "
                SET title = :title,
                    author = :author,
                    pages = :pages,
                    genre = :genre,
                    year = :year
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':title', $this->title);
        $stmt->bindValue(':author', $this->author);
        $stmt->bindValue(':pages', $this->pages, PDO::PARAM_INT);
        $stmt->bindValue(':genre', $this->genre);
        $stmt->bindValue(':year', $this->year, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Remove o livro do banco
     * @return bool sucesso
     */
    public function delete(): bool
    {
        if ($this->id === null) {
            throw new InvalidArgumentException("ID do livro não definido");
        }

        $sql = "DELETE FROM " . self::TABLE . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
