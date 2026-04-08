<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Upravit knihu</title>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen py-8">
    
    <main class="max-w-2xl mx-auto px-4">
        
        <div class="mb-6">
            <a href="<?= BASE_URL ?>/index.php" class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-2 text-sm">
                &larr; Zpět na seznam knih
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-blue-50/50">
                <h2 class="text-2xl font-bold text-slate-800">Upravit knihu <span class="text-blue-600 text-lg">#<?= htmlspecialchars($book['id']) ?></span></h2>
                <p class="text-slate-600 mt-1 text-sm">Upravujete data pro knihu: <strong class="text-slate-800"><?= htmlspecialchars($book['title']) ?></strong></p>
            </div>
            
            <form action="<?= BASE_URL ?>/index.php?url=book/update/<?= htmlspecialchars($book['id']) ?>" method="post" enctype="multipart/form-data" class="p-6 space-y-5">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    
                    <div class="sm:col-span-2">
                        <label for="id_display" class="block text-sm font-medium text-slate-700 mb-1">ID v databázi</label>
                        <input type="text" id="id_display" value="<?= htmlspecialchars($book['id']) ?>" readonly class="w-full px-4 py-2 bg-slate-100 border border-slate-200 text-slate-500 rounded-lg outline-none cursor-not-allowed">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Název knihy <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label for="author" class="block text-sm font-medium text-slate-700 mb-1">Autor <span class="text-red-500">*</span></label>
                        <input type="text" id="author" name="author" value="<?= htmlspecialchars($book['author']) ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label for="isbn" class="block text-sm font-medium text-slate-700 mb-1">ISBN <span class="text-red-500">*</span></label>
                        <input type="text" id="isbn" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-700 mb-1">Kategorie</label>
                        <input type="text" id="category" name="category" value="<?= htmlspecialchars($book['category']) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label for="subcategory" class="block text-sm font-medium text-slate-700 mb-1">Podkategorie</label>
                        <input type="text" id="subcategory" name="subcategory" value="<?= htmlspecialchars($book['subcategory']) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label for="year" class="block text-sm font-medium text-slate-700 mb-1">Rok vydání <span class="text-red-500">*</span></label>
                        <input type="number" id="year" name="year" value="<?= htmlspecialchars($book['year']) ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-slate-700 mb-1">Cena knihy (Kč)</label>
                        <input type="number" id="price" name="price" step="0.5" value="<?= htmlspecialchars($book['price']) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="link" class="block text-sm font-medium text-slate-700 mb-1">Odkaz</label>
                        <input type="text" id="link" name="link" value="<?= htmlspecialchars($book['link']) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Popis knihy</label>
                        <textarea id="description" name="description" rows="5" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"><?= htmlspecialchars($book['description']) ?></textarea>
                    </div>    

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Obrázky (zatím neřešíme, můžete ignorovat)</label>
                        <input type="file" id="images" name="images[]" multiple accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition shadow-md">
                        Uložit změny do DB
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>