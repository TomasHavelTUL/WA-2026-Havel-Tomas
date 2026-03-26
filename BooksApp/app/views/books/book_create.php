<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přidat knihu</title>
</head>
<body>
    <header>
        <h1>Aplikace Knihovna</h1>
        <nav>
            <ul>
                <li><a href="/WA-2026-Havel-Tomas/BooksApp/public/index.php">Seznam knih (Domů)</a></li>
                <li><a href="/WA-2026-Havel-Tomas/BooksApp/public/index.php?url=book/create">Přidat novou knihu</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Přidat novou knihu</h2>
        <form method="POST" action="/WA-2026-Havel-Tomas/BooksApp/public/index.php?url=book/store">
            <label>Název: <input type="text" name="title" required></label><br><br>
            <label>Autor: <input type="text" name="author" required></label><br><br>
            <label>Kategorie: <input type="text" name="category"></label><br><br>
            <label>Podkategorie: <input type="text" name="subcategory"></label><br><br>
            <label>Rok: <input type="number" name="year" min="1000" max="2100"></label><br><br>
            <label>Cena: <input type="number" step="0.01" name="price"></label><br><br>
            <label>ISBN: <input type="text" name="isbn"></label><br><br>
            <label>Popis: <textarea name="description"></textarea></label><br><br>
            <label>Odkaz: <input type="text" name="link"></label><br><br>
            <label>Obrázek (URL): <input type="text" name="images"></label><br><br>
            <button type="submit">Uložit knihu</button>
        </form>
    </main>
    <footer>
        <p>&copy; WA 2026 - Výukový projekt</p>
    </footer>
</body>
</html>