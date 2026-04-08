<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> -->
    <title>Knihovna - Seznam knih</title>
</head>
<body>
    <header>
        <h1>Aplikace Knihovna</h1>
        
        <nav>
            <ul>
                <!-- Poznámka: do odkazu href se musí dát reálná absolutní/úplná cesta k souboru index.php -->
                <li><a href="<?= BASE_URL ?>/index.php">Seznam knih (Domů)</a></li>
                <li><a href="<?= BASE_URL ?>/index.php?url=book/create">Přidat novou knihu</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php if (isset($_SESSION['messages']) && !empty($_SESSION['messages'])): ?>
            <div class="notifications-container">
                
                <?php foreach ($_SESSION['messages'] as $type => $messages): ?>
                    <?php 
                        // Jednoduché určení barvy podle typu zprávy
                        $color = 'black';
                        if ($type === 'success') $color = 'green';
                        if ($type === 'error') $color = 'red';
                        if ($type === 'notice') $color = 'orange';
                    ?>
                    
                    <?php foreach ($messages as $message): ?>
                        <div style="color: <?= $color ?>; border: 1px solid <?= $color ?>; padding: 10px; margin-bottom: 10px;">
                            <strong><?= htmlspecialchars($message) ?></strong>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                
            </div>
            
            <?php 
                // ZÁSADNÍ KROK: Po vypsání musíme zprávy ze session vymazat, 
                // aby se nezobrazovaly při každém dalším obnovení stránky!
                unset($_SESSION['messages']); 
            ?>
        <?php endif; ?>
        <h2>Dostupné knihy</h2>
        
        <?php if (empty($books)): ?>
            <p>V databázi se zatím nenachází žádné knihy.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Název knihy</th>
                        <th>Autor</th>
                        <th>Rok vydání</th>
                        <th>Cena</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?= htmlspecialchars($book['id']) ?></td>
                            <td><?= htmlspecialchars($book['title']) ?></td>
                            <td><?= htmlspecialchars($book['author']) ?></td>
                            <td><?= htmlspecialchars($book['year']) ?></td>
                            <td><?= htmlspecialchars($book['price']) ?> Kč</td>
                            <td>
                                <a href="<?= BASE_URL ?>/index.php?url=book/show/<?= $book['id'] ?>">Detail</a> | 
                                <a href="<?= BASE_URL ?>/index.php?url=book/edit/<?= $book['id'] ?>">Upravit</a> | 
                                <a href="<?= BASE_URL ?>/index.php?url=book/delete/<?= $book['id'] ?>" onclick="return confirm('Opravdu chcete tuto knihu smazat?')">Smazat</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; WA 2026 - Výukový projekt</p>
    </footer>
</body>
</html>