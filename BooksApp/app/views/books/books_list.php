<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Knihovna - Seznam knih</title>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50 text-slate-800 font-sans min-h-screen flex flex-col">

    <header class="bg-white/80 backdrop-blur-md border-b border-blue-100 sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-6 py-5 flex justify-between items-center gap-4">
            <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 tracking-tighter">
                Knihovna App
            </h1>
            <nav>
                <a href="<?= BASE_URL ?>/index.php" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-200 transition-colors">
                    Seznam knih
                </a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-10 flex-1 w-full">
        
        <?php if (isset($_SESSION['messages']) && !empty($_SESSION['messages'])): ?>
            <div class="space-y-3 mb-8">
                <?php foreach ($_SESSION['messages'] as $type => $messages): ?>
                    <?php 
                        $styles = 'bg-white border-l-4 p-4 rounded-r-xl shadow-sm';
                        if ($type === 'success') $styles .= ' border-green-500 text-green-800 bg-green-50';
                        elseif ($type === 'error') $styles .= ' border-red-500 text-red-800 bg-red-50';
                        elseif ($type === 'notice') $styles .= ' border-blue-500 text-blue-800 bg-blue-50';
                    ?>
                    
                    <?php foreach ($messages as $message): ?>
                        <div class="<?= $styles ?>" role="alert">
                            <p class="font-bold uppercase text-xs tracking-widest mb-1"><?= $type ?></p>
                            <p><?= htmlspecialchars($message) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <?php unset($_SESSION['messages']); ?>
            </div>
        <?php endif; ?>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800">Katalog knih</h2>
                <p class="text-slate-500 text-sm mt-1">Kompletní přehled vaší databáze</p>
            </div>
            <a href="<?= BASE_URL ?>/index.php?url=book/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/20 hover:-translate-y-0.5 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Přidat novou knihu
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 overflow-hidden border border-slate-100">
            <?php if (empty($books)): ?>
                <div class="p-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-700 mb-2">Žádné knihy k zobrazení</h3>
                    <p class="text-slate-500">Katalog je momentálně prázdný.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr class="bg-slate-800 text-white text-xs uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold rounded-tl-2xl">ID</th>
                                <th class="px-6 py-4 font-semibold">Kniha</th>
                                <th class="px-6 py-4 font-semibold">Autor</th>
                                <th class="px-6 py-4 font-semibold">Rok</th>
                                <th class="px-6 py-4 font-semibold text-right">Cena</th>
                                <th class="px-6 py-4 font-semibold text-center rounded-tr-2xl">Akce</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php foreach ($books as $book): ?>
                                <tr class="hover:bg-blue-50/40 transition-colors group">
                                    <td class="px-6 py-4 text-slate-400 font-mono text-xs">#<?= htmlspecialchars($book['id']) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-slate-800 block"><?= htmlspecialchars($book['title']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium"><?= htmlspecialchars($book['author']) ?></td>
                                    <td class="px-6 py-4 text-slate-500"><?= htmlspecialchars($book['year']) ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="inline-block bg-green-50 text-green-700 font-bold px-3 py-1 rounded-lg">
                                            <?= htmlspecialchars($book['price']) ?> Kč
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                            <a href="<?= BASE_URL ?>/index.php?url=book/show/<?= $book['id'] ?>" class="px-3 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-md font-medium text-xs transition-colors">
                                                Detail
                                            </a>
                                            <a href="<?= BASE_URL ?>/index.php?url=book/edit/<?= $book['id'] ?>" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-md font-medium text-xs transition-colors">
                                                Upravit
                                            </a>
                                            <a href="<?= BASE_URL ?>/index.php?url=book/delete/<?= $book['id'] ?>" 
                                               onclick="return confirm('Opravdu chcete tuto knihu smazat?')"
                                               class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md font-medium text-xs transition-colors">
                                                Smazat
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="mt-auto py-8 text-center text-slate-400 text-xs font-medium uppercase tracking-widest border-t border-slate-200 bg-white">
        &copy; WA 2026 - Knihovna App
    </footer>

</body>
</html>