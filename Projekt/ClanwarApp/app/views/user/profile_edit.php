<?php require_once '../app/views/layout/header.php'; ?>

<!-- Formulář pro úpravu profilu — odesílá POST na user/update -->
<main class="container mx-auto px-6 py-10 flex-grow flex justify-center">
    <div class="w-full max-w-2xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-orbitron font-black text-2xl tracking-widest glow-text">ÚPRAVA PROFILU</h2>
            </div>
            <a href="<?= BASE_URL ?>/index.php?url=user/profile" class="font-rajdhani text-sm tracking-wider hover:text-white transition-colors" style="color:var(--ice-muted);">&larr; Zpět</a>
        </div>

        <div class="ice-card rounded-xl p-8">
            <form action="<?= BASE_URL ?>/index.php?url=user/update" method="post">
                <div class="space-y-6">

                    <!-- Username nelze měnit — slouží jako trvalý identifikátor účtu -->
                    <div>
                        <label class="ice-label block mb-1">Uživatelské jméno <span style="color:var(--ice-muted); text-transform:none;">(nelze změnit)</span></label>
                        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled
                               class="ice-input w-full rounded px-4 py-2.5 text-sm opacity-50 cursor-not-allowed">
                    </div>

                    <!-- E-mail je povinný — slouží k přihlášení -->
                    <div>
                        <label class="ice-label block mb-1">E-mail <span style="color:#f87171;">*</span></label>
                        <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>"
                               class="ice-input w-full rounded px-4 py-2.5 text-sm">
                    </div>

                    <!-- Nickname nahrazuje username ve všech zobrazeních (volitelný) -->
                    <div>
                        <label class="ice-label block mb-1">Herní přezdívka</label>
                        <input type="text" name="nickname" value="<?= htmlspecialchars($user['nickname'] ?? '') ?>"
                               class="ice-input w-full rounded px-4 py-2.5 text-sm">
                    </div>

                    <div class="pt-4 flex gap-4">
                        <button type="submit" class="ice-btn w-full py-3 rounded text-sm font-bold">
                            Uložit změny
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once '../app/views/layout/footer.php'; ?>
