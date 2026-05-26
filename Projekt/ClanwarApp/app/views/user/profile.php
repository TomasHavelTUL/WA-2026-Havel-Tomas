<?php require_once '../app/views/layout/header.php'; ?>

<!-- Zobrazení profilu přihlášeného uživatele (read-only) -->
<main class="container mx-auto px-6 py-10 flex-grow flex justify-center">
    <div class="w-full max-w-2xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-orbitron font-black text-2xl tracking-widest glow-text">PROFIL</h2>
            </div>
            <a href="<?= BASE_URL ?>/index.php" class="font-rajdhani text-sm tracking-wider hover:text-white transition-colors" style="color:var(--ice-muted);">&larr; Zpět</a>
        </div>

        <div class="ice-card rounded-xl overflow-hidden">
            <!-- Záhlaví karty: avatar + jméno + role -->
            <div class="px-8 py-6" style="border-bottom:1px solid var(--ice-border);">
                <div class="flex items-center gap-6">
                    <!-- Avatar zobrazuje první 2 znaky nickname nebo username -->
                    <div class="w-20 h-20 rounded-full flex items-center justify-center font-orbitron font-black text-2xl"
                         style="background:rgba(0,200,255,0.1);border:2px solid var(--ice-glow);color:var(--ice-glow);">
                        <?= strtoupper(substr(!empty($user['nickname']) ? $user['nickname'] : $user['username'], 0, 2)) ?>
                    </div>
                    <div>
                        <!-- Zobrazuje nickname, pokud existuje, jinak username -->
                        <h3 class="font-orbitron font-black text-2xl" style="color:var(--ice-text);">
                            <?= htmlspecialchars(!empty($user['nickname']) ? $user['nickname'] : $user['username']) ?>
                        </h3>
                        <p class="font-rajdhani text-sm" style="color:var(--ice-muted);">
                            @<?= htmlspecialchars($user['username']) ?>
                        </p>
                        <div class="mt-2">
                            <span class="role-badge" style="background:rgba(0,72,112,0.3);color:var(--ice-glow);border:1px solid var(--ice-dark);">
                                <?= strtoupper($user['role']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailní informace o účtu -->
            <div class="px-8 py-6 space-y-6">
                <div>
                    <span class="font-rajdhani font-bold text-xs tracking-widest block mb-1" style="color:var(--ice-muted);">E-MAIL</span>
                    <span class="font-rajdhani text-base" style="color:var(--ice-text);"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <div>
                    <span class="font-rajdhani font-bold text-xs tracking-widest block mb-1" style="color:var(--ice-muted);">DATUM REGISTRACE</span>
                    <span class="font-rajdhani text-base" style="color:var(--ice-text);"><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></span>
                </div>
            </div>

            <!-- Akce: odkaz na editaci profilu -->
            <div class="px-8 py-6" style="background:rgba(0,20,40,0.4);border-top:1px solid var(--ice-border);">
                <a href="<?= BASE_URL ?>/index.php?url=user/edit" class="ice-btn inline-block px-6 py-2 rounded text-sm font-bold">
                    Upravit profil
                </a>
            </div>
        </div>
    </div>
</main>

<?php require_once '../app/views/layout/footer.php'; ?>
