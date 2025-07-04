-- Cria o banco de dados se não existir
CREATE DATABASE IF NOT EXISTS book_storage;

-- Seleciona o banco de dados
USE book_storage;

-- Cria a tabela de livros
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    pages INT NOT NULL,
    genre VARCHAR(100) NOT NULL,
    year INT NOT NULL
);

-- (Opcional) Insere dados iniciais
INSERT INTO books (title, author, pages, genre, year)
VALUES 
    ('O Senhor dos Anéis', 'J.R.R. Tolkien', 1178, 'Fantasia', 1954),
    ('1984', 'George Orwell', 328, 'Ficção Científica', 1949),
    ('Orgulho e Preconceito', 'Jane Austen', 432, 'Romance', 1813);