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
                        <label for="author">Autor knihy<span>*</span></label>
                        <input type="text" id="author" name="title" required placeholder="Prijmeni, jmeno">
                    </div>
                        <label for="isbn">ISBN <span>*</span></label>
                        <input type="text" id="isbn" name="isbn" >
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
                    <div>
                        <label for="price">Cena<span></span></label>
                        <input type="number" id="price" name="price" step="0.5">
                    </div>                   

                    <div>
                        <label for="link">Odkaz <span></span></label>
                        <input type="text" id="link" name="link" >
                    </div>
                    <div>
                        <label for="description">Popis knihy <span></span></label>
                        <textarea id="description" name="description" rows="10" cols="50">Popis knihy</textarea>
                    </div>
                    <div>
                        <label >Obrázky (můžete nahrát více)</label>
                        <label>
                            <span>Klikni pro výběr souborů</span>
                            <span>JPG / PNG / WebP – více souborů najednou</span>
                            <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden">
                        </label>
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