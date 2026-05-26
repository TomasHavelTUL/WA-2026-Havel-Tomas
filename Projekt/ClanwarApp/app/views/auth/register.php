<?php require_once '../app/views/layout/header.php'; ?>

<!-- Registrační formulář — odesílá POST na auth/storeUser -->
<main class="container mx-auto px-6 py-12 flex-grow flex items-center justify-center">
    <div class="w-full max-w-xl">

        <div class="text-center mb-8">
            <h2 class="font-orbitron font-black text-3xl tracking-widest glow-text mb-2">REGISTRACE</h2>
            <p class="font-rajdhani text-sm tracking-wider" style="color:var(--ice-muted);">Vytvořte si účet a sledujte výsledky clanwarů</p>
        </div>

        <div class="ice-card rounded-xl p-8">
            <form action="<?= BASE_URL ?>/index.php?url=auth/storeUser" method="post">
                <div class="space-y-5">

                    <!-- Sekce: povinné přihlašovací údaje -->
                    <div class="pb-2" style="border-bottom:1px solid var(--ice-border);">
                        <p class="font-orbitron text-xs tracking-widest" style="color:var(--ice-mid);">PŘIHLAŠOVACÍ ÚDAJE</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="ice-label block mb-1">Uživatelské jméno <span style="color:#f87171;">*</span></label>
                            <input type="text" name="username" required
                                   class="ice-input w-full rounded px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="ice-label block mb-1">E-mail <span style="color:#f87171;">*</span></label>
                            <input type="email" name="email" required
                                   class="ice-input w-full rounded px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="ice-label block mb-1">Heslo <span style="color:#f87171;">*</span></label>
                            <input type="password" name="password" required
                                   class="ice-input w-full rounded px-4 py-2.5 text-sm">
                        </div>
                        <!-- Potvrzení hesla — kontrola probíhá v AuthController::storeUser() -->
                        <div>
                            <label class="ice-label block mb-1">Potvrzení hesla <span style="color:#f87171;">*</span></label>
                            <input type="password" name="password_confirm" required
                                   class="ice-input w-full rounded px-4 py-2.5 text-sm">
                        </div>
                    </div>

                    <!-- Sekce: volitelný herní profil -->
                    <div class="pt-2 pb-2" style="border-bottom:1px solid var(--ice-border);">
                        <p class="font-orbitron text-xs tracking-widest" style="color:var(--ice-muted);">HERNÍ PROFIL <span class="font-rajdhani normal-case" style="font-size:0.65rem;">(volitelné)</span></p>
                    </div>

                    <!-- Nickname se zobrazuje místo username všude v aplikaci (pokud je nastaven) -->
                    <div>
                        <label class="ice-label block mb-1">Herní přezdívka</label>
                        <input type="text" name="nickname" placeholder="Jak vám máme říkat?"
                               class="ice-input w-full rounded px-4 py-2.5 text-sm placeholder-slate-600">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="ice-btn w-full py-3 rounded text-sm font-bold">
                            Vytvořit účet
                        </button>
                        <p class="text-center font-rajdhani text-sm mt-4" style="color:var(--ice-muted);">
                            Už máte účet?
                            <a href="<?= BASE_URL ?>/index.php?url=auth/login" style="color:var(--ice-glow);" class="hover:text-white transition-colors ml-1">Přihlaste se</a>
                        </p>
                    </div>

                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once '../app/views/layout/footer.php'; ?>
