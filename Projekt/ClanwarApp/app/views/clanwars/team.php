<?php require_once '../app/views/layout/header.php'; ?>

<main class="container mx-auto px-6 py-10 flex-grow">
    <div class="max-w-4xl mx-auto">

        <!-- Záhlaví stránky s počtem členů týmu -->
        <div class="mb-8">
            <h2 class="font-orbitron font-black text-2xl tracking-widest glow-text">TEAM</h2>
            <p class="font-rajdhani text-sm tracking-wider mt-1" style="color:var(--ice-muted);">
                Členů týmu: <span style="color:var(--ice-glow);font-weight:700;"><?= count($teamMembers) ?></span>
            </p>
        </div>

        <!-- Karta se členy týmu (admini + memberové) -->
        <div class="ice-card rounded-xl overflow-hidden mb-8">

            <?php if (empty($teamMembers)): ?>
                <div class="p-12 text-center font-rajdhani" style="color:var(--ice-muted);">
                    Tým zatím nemá žádné členy.
                </div>
            <?php else: ?>

                <!-- Sekce: Clan Leadeři (role = admin) -->
                <?php $leaders = array_filter($teamMembers, fn($u) => $u['role'] === 'admin'); ?>
                <?php if (!empty($leaders)): ?>
                    <div class="px-6 py-3" style="background:rgba(0,72,112,0.15);border-bottom:1px solid var(--ice-border);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-mid);">CLAN LEADERS</span>
                    </div>
                    <?php foreach ($leaders as $u):
                        $displayName = !empty($u['nickname']) ? $u['nickname'] : $u['username'];
                        $joinDate    = date('d.m.Y', strtotime($u['created_at']));
                    ?>
                        <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid rgba(26,58,92,0.3);">
                            <div class="flex items-center gap-4">
                                <!-- Avatar s iniciálami (první 2 znaky jména) -->
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 font-orbitron font-black text-sm"
                                     style="background:rgba(220,38,38,0.15);border:1px solid #7f1d1d;color:#f87171;">
                                    <?= strtoupper(substr($displayName, 0, 2)) ?>
                                </div>
                                <div>
                                    <div class="font-orbitron font-black text-sm" style="color:var(--ice-glow);">
                                        <?= htmlspecialchars($displayName) ?>
                                    </div>
                                    <!-- Username se zobrazí jen tehdy, když se liší od nicku -->
                                    <?php if (!empty($u['nickname']) && $u['nickname'] !== $u['username']): ?>
                                        <div class="font-rajdhani text-xs" style="color:var(--ice-muted);">@<?= htmlspecialchars($u['username']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <div class="font-rajdhani text-xs" style="color:var(--ice-muted);">Člen od</div>
                                    <div class="font-rajdhani font-semibold text-sm" style="color:var(--ice-text);"><?= $joinDate ?></div>
                                </div>
                                <span class="font-orbitron text-xs px-3 py-1 rounded"
                                      style="background:rgba(127,29,29,0.25);border:1px solid #7f1d1d;color:#f87171;letter-spacing:0.1em;">
                                    CLAN LEADER
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Sekce: Řadoví členové (role = member) -->
                <?php $members = array_filter($teamMembers, fn($u) => $u['role'] === 'member'); ?>
                <?php if (!empty($members)): ?>
                    <div class="px-6 py-3" style="background:rgba(0,72,112,0.1);border-bottom:1px solid var(--ice-border);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-muted);">ČLENOVÉ</span>
                    </div>
                    <?php foreach ($members as $u):
                        $displayName = !empty($u['nickname']) ? $u['nickname'] : $u['username'];
                        $joinDate    = date('d.m.Y', strtotime($u['created_at']));
                    ?>
                        <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid rgba(26,58,92,0.3);">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 font-orbitron font-black text-sm"
                                     style="background:rgba(0,72,112,0.2);border:1px solid var(--ice-border);color:var(--ice-glow);">
                                    <?= strtoupper(substr($displayName, 0, 2)) ?>
                                </div>
                                <div>
                                    <div class="font-orbitron font-black text-sm" style="color:var(--ice-text);">
                                        <?= htmlspecialchars($displayName) ?>
                                    </div>
                                    <?php if (!empty($u['nickname']) && $u['nickname'] !== $u['username']): ?>
                                        <div class="font-rajdhani text-xs" style="color:var(--ice-muted);">@<?= htmlspecialchars($u['username']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <div class="font-rajdhani text-xs" style="color:var(--ice-muted);">Člen od</div>
                                    <div class="font-rajdhani font-semibold text-sm" style="color:var(--ice-text);"><?= $joinDate ?></div>
                                </div>
                                <span class="font-rajdhani font-bold text-xs px-3 py-1 rounded"
                                      style="background:rgba(0,50,90,0.3);border:1px solid var(--ice-dark);color:var(--ice-glow);letter-spacing:0.08em;">
                                    ČLEN
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php endif; ?>
        </div>

        <!-- Admin panel: správa čekatelů na přijetí — vidí jen admin -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <div class="ice-card rounded-xl overflow-hidden">
                <div class="px-6 py-4" style="background:rgba(127,29,29,0.1);border-bottom:1px solid rgba(127,29,29,0.3);">
                    <p class="font-rajdhani font-bold text-xs tracking-widest" style="color:#f87171;">SPRÁVA UŽIVATELŮ</p>
                    <p class="font-rajdhani text-xs mt-1" style="color:var(--ice-muted);">
                        Uživatelé čekající na přijetí do týmu:
                        <span style="color:#f87171;font-weight:700;"><?= count($pendingUsers) ?></span>
                    </p>
                </div>

                <?php if (empty($pendingUsers)): ?>
                    <div class="p-8 text-center font-rajdhani text-sm" style="color:var(--ice-muted);">
                        Žádní uživatelé nečekají na přijetí.
                    </div>
                <?php else: ?>
                    <!-- Seznam uživatelů s rolí "user" čekajících na povýšení na "member" -->
                    <?php foreach ($pendingUsers as $u):
                        $displayName = !empty($u['nickname']) ? $u['nickname'] : $u['username'];
                        $joinDate    = date('d.m.Y', strtotime($u['created_at']));
                    ?>
                        <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid rgba(26,58,92,0.3);">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 font-orbitron font-black text-sm"
                                     style="background:rgba(10,22,40,0.8);border:1px solid var(--ice-border);color:var(--ice-muted);">
                                    <?= strtoupper(substr($displayName, 0, 2)) ?>
                                </div>
                                <div>
                                    <div class="font-orbitron font-black text-sm" style="color:var(--ice-text);">
                                        <?= htmlspecialchars($displayName) ?>
                                    </div>
                                    <?php if (!empty($u['nickname']) && $u['nickname'] !== $u['username']): ?>
                                        <div class="font-rajdhani text-xs" style="color:var(--ice-muted);">@<?= htmlspecialchars($u['username']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <div class="font-rajdhani text-xs" style="color:var(--ice-muted);">Registrace</div>
                                    <div class="font-rajdhani font-semibold text-sm" style="color:var(--ice-text);"><?= $joinDate ?></div>
                                </div>
                                <!-- Odkaz provede GET požadavek na clanwar/promoteUser/{id} -->
                                <a href="<?= BASE_URL ?>/index.php?url=clanwar/promoteUser/<?= $u['id'] ?>"
                                   onclick="return confirm('Povýšit <?= htmlspecialchars($displayName) ?> na člena?')"
                                   class="font-rajdhani font-bold text-sm px-4 py-2 rounded transition-all"
                                   style="background:rgba(0,50,90,0.4);border:1px solid var(--ice-dark);color:var(--ice-glow);"
                                   onmouseover="this.style.borderColor='var(--ice-glow)';this.style.boxShadow='0 0 12px rgba(0,200,255,0.3)'"
                                   onmouseout="this.style.borderColor='var(--ice-dark)';this.style.boxShadow='none'">
                                    + Přijmout do týmu
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?url=clanwar/deleteUser/<?= $u['id'] ?>"
                                   onclick="return confirm('Smazat uživatele <?= htmlspecialchars($displayName) ?>? Tato akce je nevratná.')"
                                   class="font-rajdhani font-bold text-sm px-4 py-2 rounded transition-all"
                                   style="background:rgba(127,29,29,0.25);border:1px solid #7f1d1d;color:#f87171;"
                                   onmouseover="this.style.borderColor='#f87171';this.style.boxShadow='0 0 12px rgba(248,113,113,0.3)'"
                                   onmouseout="this.style.borderColor='#7f1d1d';this.style.boxShadow='none'">
                                    ✕ Smazat
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php require_once '../app/views/layout/footer.php'; ?>
