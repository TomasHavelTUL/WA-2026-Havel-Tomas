<?php

class BookController {

    // 0. Výchozí metoda pro zobrazení úvodní stránky
        public function index() {
        // Načtení potřebných tříd
        require_once '../app/models/Database.php';
        require_once '../app/models/Book.php';

        // Vytvoření připojení k databázi
        $database = new Database();
        $db = $database->getConnection();

        // Inicializace modelu a získání dat
        $bookModel = new Book($db);
        $books = $bookModel->getAll(); // Proměnná $books nyní obsahuje pole všech knih
        
        // Načte se (vloží) připravený soubor s HTML strukturou
        require_once '../app/views/books/books_list.php';
    }


    // 1. Zobrazení formuláře pro přidání nové knihy
    public function create() {
        // Zde se pouze načte (vloží) připravený soubor s HTML formulářem
        require_once '../app/views/books/book_create.php';
    }

       // 2. Zpracování dat odeslaných z formuláře
    public function store() {
        // Kontrola, zda byl formulář odeslán metodou POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Získání a očištění textových dat (ochrana proti XSS)
            $title = htmlspecialchars($_POST['title'] ?? '');
            $author = htmlspecialchars($_POST['author'] ?? '');
            $isbn = htmlspecialchars($_POST['isbn'] ?? '');
            $category = htmlspecialchars($_POST['category'] ?? '');
            $subcategory = htmlspecialchars($_POST['subcategory'] ?? '');
            
            // U číselných hodnot se provádí explicitní přetypování
            $year = (int)($_POST['year'] ?? 0);
            $price = (float)($_POST['price'] ?? 0);
            
            $link = htmlspecialchars($_POST['link'] ?? '');
            $description = htmlspecialchars($_POST['description'] ?? '');

            // Prozatímní zástupce pro obrázky (bude řešeno v budoucnu)
            $uploadedImages = []; 

            // 2. Komunikace s databází a modelem
            require_once '../app/models/Database.php';
            require_once '../app/models/Book.php';

            // Vytvoření připojení k DB
            $database = new Database();
            $db = $database->getConnection();

            // Vytvoření objektu knihy a volání metody pro uložení
            $bookModel = new Book($db);
            $isSaved = $bookModel->create(
                $title, $author, $category, $subcategory, 
                $year, $price, $isbn, $description, $link, $uploadedImages
            );

            // 3. Vyhodnocení výsledku a přesměrování
            if ($isSaved) {
                // Vyvolání zelené notifikace pro úspěšnou akci
                $this->addSuccessMessage('Kniha byla úspěšně uložena do databáze.');
                
                // Přesměrování zpět na hlavní stránku s využitím dynamické BASE_URL
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            } else {
                // Vyvolání červené notifikace pro kritické selhání
                $this->addErrorMessage('Nastala chyba. Nepodařilo se uložit knihu do databáze.');
            }
            
        } else {
            // Pokud je stránka navštívena napřímo bez odeslání dat, zobrazí se žlutá informativní zpráva
            $this->addNoticeMessage('Pro přidání knihy je nutné odeslat formulář.');
        }
    }

    // 3. Smazání existující knihy
    public function delete($id = null) {
        // Kontrola, zda bylo v URL předáno ID
        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID knihy ke smazání.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        // Načtení potřebných tříd a spojení s databází
        require_once '../app/models/Database.php';
        require_once '../app/models/Book.php';

        $database = new Database();
        $db = $database->getConnection();

        // Inicializace modelu a zavolání metody pro smazání
        $bookModel = new Book($db);
        $isDeleted = $bookModel->delete($id);

        // Vyhodnocení výsledku a přesměrování s notifikací
        if ($isDeleted) {
            // Zelená zpráva o úspěchu
            $this->addSuccessMessage('Kniha byla trvale smazána z databáze.');
        } else {
            // Červená zpráva pro případ, že kniha neexistovala nebo selhal dotaz
            $this->addErrorMessage('Nastala chyba. Knihu se nepodařilo smazat.');
        }

        // Vždy následuje návrat na seznam knih
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }

        // 4. Zobrazení formuláře pro úpravu existující knihy
    public function edit($id = null) {
        // Kontrola, zda bylo v URL vůbec předáno nějaké ID
        if (!$id) {
            // Vyvolání červené notifikace pro kritickou chybu
            $this->addErrorMessage('Nebylo zadáno ID knihy k úpravě.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        // Načtení potřebných tříd a spojení s databází
        require_once '../app/models/Database.php';
        require_once '../app/models/Book.php';

        $database = new Database();
        $db = $database->getConnection();

        // Získání dat o konkrétní knize
        $bookModel = new Book($db);
        $book = $bookModel->getById($id); // Proměnná $book nyní obsahuje asociativní pole dat

        // Bezpečnostní kontrola: Zda kniha s daným ID vůbec existuje
        if (!$book) {
            // Pokud knihu někdo mezitím smazal, nebo uživatel zadal do URL neexistující ID
            $this->addErrorMessage('Požadovaná kniha nebyla v databázi nalezena.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        // Pokud je vše v pořádku, načte se připravený soubor s HTML formulářem pro úpravy.
        // Šablona bude mít automaticky přístup k proměnné $book.
        require_once '../app/views/books/book_edit.php';
    }

        // 5. Zpracování dat odeslaných z editačního formuláře
    public function update($id = null) {
        // Zabezpečení: Je k dispozici ID a byl odeslán formulář?
        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID knihy k aktualizaci.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Získání a očištění textových dat
            $title = htmlspecialchars($_POST['title'] ?? '');
            $author = htmlspecialchars($_POST['author'] ?? '');
            $isbn = htmlspecialchars($_POST['isbn'] ?? '');
            $category = htmlspecialchars($_POST['category'] ?? '');
            $subcategory = htmlspecialchars($_POST['subcategory'] ?? '');
            
            // Přetypování číselných hodnot
            $year = (int)($_POST['year'] ?? 0);
            $price = (float)($_POST['price'] ?? 0);
            
            $link = htmlspecialchars($_POST['link'] ?? '');
            $description = htmlspecialchars($_POST['description'] ?? '');

            // Prozatímní zástupce pro obrázky
            $uploadedImages = []; 

            // 2. Komunikace s databází a modelem
            require_once '../app/models/Database.php';
            require_once '../app/models/Book.php';

            $database = new Database();
            $db = $database->getConnection();

            // 3. Volání updatu nad modelem
            $bookModel = new Book($db);
            $isUpdated = $bookModel->update(
                $id, $title, $author, $category, $subcategory, 
                $year, $price, $isbn, $description, $link, $uploadedImages
            );

            // 4. Vyhodnocení výsledku a přesměrování
            if ($isUpdated) {
                // Vyvolání zelené notifikace o úspěchu
                $this->addSuccessMessage('Kniha byla úspěšně upravena.');
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            } else {
                // Vyvolání červené chybové notifikace
                $this->addErrorMessage('Nastala chyba. Změny se nepodařilo uložit.');
            }
            
        } else {
            // Pokud by někdo zkusil přistoupit na URL napřímo bez odeslání formuláře (žlutá notifikace)
            $this->addNoticeMessage('Pro úpravu knihy je nutné odeslat formulář.');
        }
    }

    // --- Pomocné metody pro systém notifikací ---
    // (V reálném projektu by tyto metody ideálně ležely v hlavní nadřazené třídě Controller)

    protected function addSuccessMessage($message) {
        // Zelená zpráva o úspěchu
        $_SESSION['messages']['success'][] = $message;
    }

    protected function addNoticeMessage($message) {
        // Žlutá informativní zpráva
        $_SESSION['messages']['notice'][] = $message;
    }

    protected function addErrorMessage($message) {
        // Červená chybová zpráva
        $_SESSION['messages']['error'][] = $message;
    }




}