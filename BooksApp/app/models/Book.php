<?php
require_once __DIR__ . '/Database.php';

class Book {
    private $conn;
    private $table_name = "books";

    public $title;
    public $author;
    public $category;
    public $subcategory;
    public $year;
    public $price;
    public $isbn;
    public $description;
    public $link;
    public $images;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (title, author, category, subcategory, year, price, isbn, description, link, images) 
                  VALUES 
                  (:title, :author, :category, :subcategory, :year, :price, :isbn, :description, :link, :images)";

        $stmt = $this->conn->prepare($query);

        // Očištění dat
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->author = htmlspecialchars(strip_tags($this->author));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->subcategory = htmlspecialchars(strip_tags($this->subcategory));
        $this->year = htmlspecialchars(strip_tags($this->year));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->link = htmlspecialchars(strip_tags($this->link));
        if (!empty($this->images)) {
        $clean_image = htmlspecialchars(strip_tags($this->images));
        $this->images = json_encode([$clean_image]);
            } else {
        $this->images = null;
        }

        // Změna prázdných řetězců na NULL pro numerické hodnoty
        $year_val = !empty($this->year) ? $this->year : null;
        $price_val = !empty($this->price) ? $this->price : null;

        // Navázání parametrů
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":author", $this->author);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":subcategory", $this->subcategory);
        $stmt->bindParam(":year", $year_val);
        $stmt->bindParam(":price", $price_val);
        $stmt->bindParam(":isbn", $this->isbn);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":link", $this->link);
        $images_val = !empty($this->images) ? $this->images : null;
        $stmt->bindParam(":images", $images_val);   

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}