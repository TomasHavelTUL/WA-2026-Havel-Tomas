<?php
require_once __DIR__ . '/../models/Book.php';

class BookController {
    public function index() {
        require_once '../app/views/books/books_list.php';
    }

    public function create() {
        require_once '../app/views/books/book_create.php';
    }

    public function store() {
        $book = new Book();
        $book->title       = $_POST['title']       ?? '';
        $book->author      = $_POST['author']      ?? '';
        $book->category    = $_POST['category']    ?? '';
        $book->subcategory = $_POST['subcategory'] ?? '';
        $book->year        = $_POST['year']        ?? null;
        $book->price       = $_POST['price']       ?? null;
        $book->isbn        = $_POST['isbn']        ?? '';
        $book->description = $_POST['description'] ?? '';
        $book->link        = $_POST['link']        ?? '';
        $book->images      = $_POST['images']      ?? '';

        if ($book->create()) {
            header('Location: /WA-2026-Havel-Tomas/BooksApp/public/index.php');
            exit;
        } else {
            echo "Chyba při ukládání knihy.";
        }
    }
}