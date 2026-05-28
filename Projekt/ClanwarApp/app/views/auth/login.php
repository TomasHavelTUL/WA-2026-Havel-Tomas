<?php require_once '../app/views/layout/header.php'; ?>

<!-- Přihlašovací formulář — odesílá POST na auth/authenticate -->
<main class="container mx-auto px-6 py-12 flex-grow flex items-center justify-center">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h2 class="font-rajdhani font-black text-3xl tracking-widest glow-text mb-2">PŘIHLÁŠENÍ</h2>
            <p class="font-rajdhani text-sm tracking-wider" style="color:var(--ice-muted);">Vítejte zpět v FSC ClanWar Trackeru</p>
        </div>

        <div class="ice-card rounded-xl p-8">
            <form action="<?= BASE_URL ?>/index.php?url=auth/authenticate" method="post">
                <div class="space-y-5">

                    <!-- E-mail jako přihlašovací identifikátor (nikoli username) -->
                    <div>
                        <label class="ice-label block mb-1">E-mail</label>
                        <input type="email" name="email" required autofocus
                               class="ice-input w-full rounded px-4 py-2.5 text-sm">
                    </div>

                    <div>
                        <label class="ice-label block mb-1">Heslo</label>
                        <input type="password" name="password" required
                               class="ice-input w-full rounded px-4 py-2.5 text-sm">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="ice-btn w-full py-3 rounded text-sm font-bold">
                            Přihlásit se
                        </button>
                    </div>

                    <!-- Odkaz na registraci pro nové uživatele -->
                    <p class="text-center font-rajdhani text-sm pt-2" style="color:var(--ice-muted);border-top:1px solid var(--ice-border);">
                        Nemáte účet?
                        <a href="<?= BASE_URL ?>/index.php?url=auth/register" style="color:var(--ice-glow);" class="hover:text-white transition-colors ml-1">Zaregistrujte se</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once '../app/views/layout/footer.php'; ?>
