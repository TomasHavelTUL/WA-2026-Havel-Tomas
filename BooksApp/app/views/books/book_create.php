<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> -->
    <title>Document</title>
</head>
<body>
    <div>
        <div>
            <h2>Pridat novou knihu</h2>
            <p>Vyplnte udaje a ulozte novou knihu do databaze.</p>
        </div>

        <div>
            <form action="">
                <div>
                    <div>
                        <label for="title">Nazev knihy <span>*</span></label>
                        <input type="text" id="title" name="title" required>
                    </div>                    
                    <div>
                        <label for="author">Autor <span>*</span></label>
                        <input type="text" id="author" name="title" required>
                    </div>
                     <div>
                        <label for="category">Kategorie</label>
                        <input type="text" id="category" name="title" >
                    </div>                   
                     <div>
                        <label for="subcategory">Podkategorie</label>
                        <input type="text" id="subcategory" name="title" >
                    </div>
                    <div>
                        <label for="year">Rok vydani<span>*</span></label>
                        <input type="number" id="year" name="year" required>
                    </div>                   
                </div>
                



                <div>
                    <div>
                        <button type="submit">Odeslat</button>
                    </div>
                </div>


            </form>
        </div>
    </div>
</body>
</html>