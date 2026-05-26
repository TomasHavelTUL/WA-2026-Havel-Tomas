<?php require_once '../app/views/layout/header.php'; ?>

<?php
    // Předpočítání celkového skóre a pomocných proměnných pro zobrazení
    $total1  = $clanwar['round1_team1'] + $clanwar['round2_team1'] + $clanwar['round3_team1'];
    $total2  = $clanwar['round1_team2'] + $clanwar['round2_team2'] + $clanwar['round3_team2'];
    $isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';
    $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] === $clanwar['created_by'];
    $author  = !empty($clanwar['nickname']) ? $clanwar['nickname'] : ($clanwar['username'] ?? '—');
    // Pole kol pro iteraci v sekci průběhu zápasu
    $rounds  = [
        1 => [$clanwar['round1_team1'], $clanwar['round1_team2']],
        2 => [$clanwar['round2_team1'], $clanwar['round2_team2']],
        3 => [$clanwar['round3_team1'], $clanwar['round3_team2']],
    ];

    // Barva výsledku: zelená = výhra fSc, bílá = remíza, červená = prohra
    $winnerStr = htmlspecialchars($clanwar['winner']);
    if (strtolower($winnerStr) === 'fsc') {
        $winColor = '#4ade80';
    } elseif ($winnerStr === 'Remíza') {
        $winColor = '#ffffff';
    } else {
        $winColor = '#f87171';
    }
?>

<main class="container mx-auto px-6 py-10 flex-grow">
    <div class="max-w-4xl mx-auto">

        <!-- Záhlaví: číslo clanwaru, autor, datum, formát a akční tlačítka -->
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="font-orbitron font-black text-2xl tracking-widest glow-text">
                    CLANWAR <span style="color:var(--ice-mid);">#<?= $clanwar['id'] ?></span>
                </h2>
                <p class="font-rajdhani text-sm mt-1" style="color:var(--ice-muted);">
                    Přidal: <span style="color:var(--ice-text);"><?= htmlspecialchars($author) ?></span>
                    &bull; <?= date('d.m.Y H:i', strtotime($clanwar['created_at'])) ?>
                    &bull; <span class="font-orbitron text-xs px-2 py-0.5 rounded" style="background:rgba(0,72,112,0.3);border:1px solid var(--ice-border);color:var(--ice-text);"><?= $clanwar['team_size'] ?></span>
                </p>
            </div>
            <!-- Tlačítka úpravy a mazání vidí jen vlastník záznamu nebo admin -->
            <div class="flex items-center gap-3">
                <?php if ($isOwner || $isAdmin): ?>
                    <a href="<?= BASE_URL ?>/index.php?url=clanwar/edit/<?= $clanwar['id'] ?>"
                       class="font-rajdhani font-semibold text-sm px-3 py-1 rounded transition-colors"
                       style="border:1px solid #16a34a;color:#4ade80;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#4ade80'">Upravit</a>
                    <a href="<?= BASE_URL ?>/index.php?url=clanwar/delete/<?= $clanwar['id'] ?>"
                       onclick="return confirm('Opravdu smazat?')"
                       class="font-rajdhani font-semibold text-sm px-3 py-1 rounded transition-colors"
                       style="border:1px solid #dc2626;color:#f87171;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#f87171'">Smazat</a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/index.php" class="font-rajdhani text-sm tracking-wider hover:text-white transition-colors" style="color:var(--ice-muted);">&larr; Zpět</a>
            </div>
        </div>

        <!-- Karta s výsledkem zápasu -->
        <div class="ice-card rounded-xl p-6 md:p-8 mb-6">

            <!-- Třísloupkový layout: tým1 | celkové skóre + vítěz | tým2 -->
            <div class="grid grid-cols-3 gap-4 items-center text-center mb-8">

                <!-- Tým 1: ztlumí se, pokud prohrál -->
                <div class="<?= ($clanwar['winner']!==$clanwar['team1_name'] && $clanwar['winner']!=='Remíza')?'opacity-50':'' ?>">
                    <div class="font-orbitron font-black text-xl mb-2" style="color:var(--ice-text);">
                        <?= htmlspecialchars($clanwar['team1_name']) ?>
                    </div>
                    <?php if (!empty(array_filter($clanwar['team1_players']))): ?>
                        <div class="font-rajdhani text-xs space-y-0.5" style="color:var(--ice-muted);">
                            <?php foreach (array_filter($clanwar['team1_players']) as $p): ?>
                                <div><?= htmlspecialchars($p) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Střed: velké skóre a výsledek -->
                <div class="text-center">
                    <div class="font-orbitron font-black mb-2" style="font-size:3.5rem;line-height:1;">
                        <span style="color:<?= $total1>=$total2?'var(--ice-glow)':'var(--ice-muted)' ?>;"><?= $total1 ?></span>
                        <span style="color:var(--ice-border);font-size:2rem;margin:0 6px;">:</span>
                        <span style="color:<?= $total2>$total1?'var(--ice-glow)':'var(--ice-muted)' ?>;"><?= $total2 ?></span>
                    </div>
                    <!-- Název vítěze s barevným zásvitem -->
                    <div class="font-orbitron text-xs tracking-widest" style="color:<?= $winColor ?>; text-shadow:0 0 10px <?= $winColor ?>;">
                        <?php if ($clanwar['winner']==='Remíza'): ?>
                            REMÍZA
                        <?php else: ?>
                            &#9733; <?= $winnerStr ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tým 2: ztlumí se, pokud prohrál -->
                <div class="<?= ($clanwar['winner']!==$clanwar['team2_name'] && $clanwar['winner']!=='Remíza')?'opacity-50':'' ?>">
                    <div class="font-orbitron font-black text-xl mb-2" style="color:var(--ice-text);">
                        <?= htmlspecialchars($clanwar['team2_name']) ?>
                    </div>
                    <?php if (!empty(array_filter($clanwar['team2_players']))): ?>
                        <div class="font-rajdhani text-xs space-y-0.5" style="color:var(--ice-muted);">
                            <?php foreach (array_filter($clanwar['team2_players']) as $p): ?>
                                <div><?= htmlspecialchars($p) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Průběh jednotlivých kol — proporcionální progress bary -->
            <div class="pt-6" style="border-top:1px solid var(--ice-border);">
                <p class="font-rajdhani font-bold text-xs tracking-widest mb-4" style="color:var(--ice-muted);">PRŮBĚH KOL</p>
                <div class="space-y-3">
                    <?php foreach ($rounds as $r => [$s1,$s2]):
                        $rw = $s1>$s2?1:($s2>$s1?2:0); // vítěz kola: 1, 2 nebo 0 (remíza)
                        $sum = $s1+$s2;
                    ?>
                        <div class="flex items-center gap-3">
                            <span class="font-orbitron text-xs w-14 text-right" style="color:var(--ice-muted);">KOLO <?= $r ?></span>
                            <span class="font-orbitron text-sm w-7 text-right <?= $rw===1?'glow-text':'' ?>" style="<?= $rw!==1?'color:var(--ice-muted)':'' ?>"><?= $s1 ?></span>
                            <!-- Šířka pruhu = podíl skóre z celkového součtu; při 0:0 se dělí napůl -->
                            <div class="flex-1 flex h-2 rounded overflow-hidden gap-0.5">
                                <div class="rounded-l transition-all" style="width:<?= $sum>0?round($s1/$sum*100):50 ?>%;background:<?= $rw===1?'var(--ice-glow)':'var(--ice-border)' ?>;"></div>
                                <div class="rounded-r transition-all" style="width:<?= $sum>0?round($s2/$sum*100):50 ?>%;background:<?= $rw===2?'var(--ice-glow)':'var(--ice-border)' ?>;"></div>
                            </div>
                            <span class="font-orbitron text-sm w-7 <?= $rw===2?'glow-text':'' ?>" style="<?= $rw!==2?'color:var(--ice-muted)':'' ?>"><?= $s2 ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Poznámka — zobrazí se jen pokud byla vyplněna -->
            <?php if (!empty($clanwar['note'])): ?>
                <div class="mt-6 pt-4" style="border-top:1px solid var(--ice-border);">
                    <p class="font-orbitron text-xs tracking-widest mb-2" style="color:var(--ice-muted);">POZNÁMKA</p>
                    <p class="font-rajdhani text-sm italic" style="color:var(--ice-text);"><?= htmlspecialchars($clanwar['note']) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sekce komentářů -->
        <div class="ice-card rounded-xl p-6 md:p-8 mb-6">
            <div class="pb-3 mb-5" style="border-bottom:1px solid var(--ice-border);">
                <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-mid);">KOMENTÁŘE</span>
                <span class="font-rajdhani text-sm ml-2" style="color:var(--ice-muted);">(<?= count($comments) ?>)</span>
            </div>

            <?php if (empty($comments)): ?>
                <p class="font-rajdhani text-sm text-center py-6" style="color:var(--ice-muted);">Zatím žádné komentáře. Buďte první!</p>
            <?php else: ?>
                <div class="space-y-3 mb-6">
                    <?php foreach ($comments as $c): ?>
                        <div class="flex items-start justify-between gap-4 px-4 py-3 rounded" style="background:rgba(0,20,40,0.6);border:1px solid var(--ice-border);">
                            <div>
                                <!-- Hlavička komentáře: jméno, role-odznak, datum -->
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-orbitron text-xs" style="color:var(--ice-glow);"><?= htmlspecialchars($c['author_name']) ?></span>
                                    <?php
                                        // Barva role-odznaku komentátora
                                        if ($c['author_role']==='admin')       $rb='background:rgba(127,29,29,0.3);color:#f87171;border:1px solid #7f1d1d;';
                                        elseif ($c['author_role']==='member')  $rb='background:rgba(0,50,90,0.4);color:var(--ice-glow);border:1px solid var(--ice-dark);';
                                        else                                    $rb='background:rgba(10,20,40,0.6);color:var(--ice-muted);border:1px solid var(--ice-border);';
                                    ?>
                                    <span class="role-badge" style="<?= $rb ?>"><?= strtoupper($c['author_role']) ?></span>
                                    <span class="font-rajdhani text-xs" style="color:var(--ice-muted);"><?= date('d.m.Y H:i', strtotime($c['created_at'])) ?></span>
                                </div>
                                <p class="font-rajdhani text-sm" style="color:var(--ice-text);"><?= htmlspecialchars($c['content']) ?></p>
                            </div>
                            <!-- Akce komentáře: úprava jen pro autora, mazání pro autora nebo admina -->
                            <div class="flex gap-3 flex-shrink-0 mt-1">
                                <?php if (isset($_SESSION['user_id']) && $c['user_id'] === $_SESSION['user_id']): ?>
                                    <a href="<?= BASE_URL ?>/index.php?url=clanwar/editComment/<?= $c['id'] ?>"
                                       class="font-rajdhani font-semibold text-xs transition-colors hover:text-white"
                                       style="color:#4ade80;">Upravit</a>
                                <?php endif; ?>
                                <?php if (isset($_SESSION['user_id']) && ($c['user_id'] === $_SESSION['user_id'] || $isAdmin)): ?>
                                    <a href="<?= BASE_URL ?>/index.php?url=clanwar/deleteComment/<?= $c['id'] ?>"
                                       onclick="return confirm('Smazat komentář?')"
                                       class="font-rajdhani font-semibold text-xs transition-colors hover:text-white"
                                       style="color:#f87171;">Smazat</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Formulář pro přidání komentáře — vidí jen přihlášení uživatelé -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="<?= BASE_URL ?>/index.php?url=clanwar/storeComment/<?= $clanwar['id'] ?>" method="post">
                    <label class="ice-label block mb-2">Váš komentář</label>
                    <textarea name="content" rows="3" required placeholder="Napište komentář..."
                              class="ice-input w-full rounded px-4 py-2.5 text-sm placeholder-slate-700 mb-3"></textarea>
                    <button type="submit" class="ice-btn px-6 py-2 rounded text-sm font-bold">
                        Přidat komentář
                    </button>
                </form>
            <?php else: ?>
                <!-- Nepřihlášený uživatel dostane výzvu k přihlášení -->
                <div class="font-rajdhani text-sm text-center py-4 rounded" style="border:1px solid var(--ice-border);color:var(--ice-muted);">
                    <a href="<?= BASE_URL ?>/index.php?url=auth/login" style="color:var(--ice-glow);" class="hover:text-white transition-colors">Přihlaste se</a> pro přidání komentáře.
                </div>
            <?php endif; ?>
        </div>

        <!-- Screenshot zápasu — zobrazí se jen pokud byl nahrán obrázek -->
        <?php if (!empty($clanwar['image_path'])): ?>
            <div class="ice-card rounded-xl p-6 md:p-8 mt-6 text-center">
                <span class="font-rajdhani font-bold text-xs tracking-widest block mb-4" style="color:var(--ice-mid);">SCREENSHOT ZÁPASU</span>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($clanwar['image_path']) ?>" alt="Výsledek zápasu" class="inline-block max-w-full h-auto rounded-lg" style="border:1px solid var(--ice-border);">
            </div>
        <?php endif; ?>

    </div>
</main>

<?php require_once '../app/views/layout/footer.php'; ?>
