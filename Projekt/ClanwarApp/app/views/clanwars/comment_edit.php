<?php require_once '../app/views/layout/header.php'; ?>

<!-- Formulář pro úpravu existujícího komentáře — odesílá POST na clanwar/updateComment/{id} -->
<main class="container mx-auto px-6 py-12 flex-grow flex items-center justify-center">
    <div class="w-full max-w-xl">
        <div class="text-center mb-8">
            <h2 class="font-orbitron font-black text-3xl tracking-widest glow-text mb-2">UPRAVIT KOMENTÁŘ</h2>
        </div>

        <div class="ice-card rounded-xl p-8">
            <form action="<?= BASE_URL ?>/index.php?url=clanwar/updateComment/<?= $comment['id'] ?>" method="post">
                <div class="space-y-5">
                    <div>
                        <label class="ice-label block mb-1">Text komentáře <span style="color:#f87171;">*</span></label>
                        <!-- Stávající obsah komentáře je předvyplněn pro editaci -->
                        <textarea name="content" required rows="4" class="ice-input w-full rounded px-4 py-2.5 text-sm"><?= htmlspecialchars($comment['content']) ?></textarea>
                    </div>
                    <div class="pt-2 flex gap-4">
                        <button type="submit" class="ice-btn w-full py-3 rounded text-sm font-bold">
                            Uložit změny
                        </button>
                        <!-- Tlačítko Zrušit vrátí uživatele zpět na detail clanwaru bez uložení -->
                        <a href="<?= BASE_URL ?>/index.php?url=clanwar/show/<?= $comment['clanwar_id'] ?>" class="w-full py-3 rounded text-sm font-bold text-center transition-colors" style="border:1px solid var(--ice-border);color:var(--ice-muted);" onmouseover="this.style.color='var(--ice-text)';this.style.borderColor='var(--ice-mid)'" onmouseout="this.style.color='var(--ice-muted)';this.style.borderColor='var(--ice-border)'">
                            Zrušit
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once '../app/views/layout/footer.php'; ?>
