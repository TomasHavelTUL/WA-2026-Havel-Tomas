<?php require_once '../app/views/layout/header.php'; ?>

<?php
// Předpočítání celkového bilance fSc ze všech záznamů pro zobrazení v hlavičce
$fscWins = 0;
$fscLosses = 0;
foreach ($clanwars as $cw) {
    if (strtolower($cw['winner']) === 'fsc') {
        $fscWins++;
    } elseif ($cw['winner'] !== 'Remíza') {
        $fscLosses++;
    }
}
?>

<main class="container mx-auto px-6 py-10 flex-grow">

    <!-- Záhlaví stránky s celkovou statistikou -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="font-orbitron font-black text-2xl tracking-widest glow-text">CLANWARY</h2>
            <p class="font-rajdhani text-sm tracking-wider mt-1" style="color:var(--ice-muted);">
                Celkem záznamů: <span id="total-count" style="color:var(--ice-glow);font-weight:700;"><?= count($clanwars) ?></span>
                &bull; fSc Výhry: <span style="color:#4ade80;font-weight:700;"><?= $fscWins ?></span>
                &bull; fSc Prohry: <span style="color:#f87171;font-weight:700;"><?= $fscLosses ?></span>
            </p>
        </div>
        <?php if (in_array($_SESSION['user_role'] ?? '', ['admin','member'])): ?>
            <a href="<?= BASE_URL ?>/index.php?url=clanwar/create" class="ice-btn font-rajdhani px-5 py-2 rounded text-sm font-bold">
                + Přidat clanwar
            </a>
        <?php endif; ?>
    </div>

    <!-- Filtrování tabulky podle názvu týmu (live search přes JS) -->
    <div class="ice-card rounded-xl p-4 mb-4 flex flex-col md:flex-row gap-3 items-stretch md:items-center">
        <div class="flex-1 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-orbitron text-xs pointer-events-none" style="color:var(--ice-muted);">&#9906;</span>
            <input type="text" id="team-filter"
                   placeholder="Filtrovat podle názvu týmu..."
                   class="ice-input w-full rounded pl-8 pr-4 py-2.5 text-sm placeholder-slate-700 font-rajdhani">
        </div>
        <button onclick="clearFilter()"
                class="font-rajdhani font-bold text-sm px-4 py-2.5 rounded transition-colors"
                style="border:1px solid var(--ice-border);color:var(--ice-muted);"
                onmouseover="this.style.color='var(--ice-glow)';this.style.borderColor='var(--ice-mid)'"
                onmouseout="this.style.color='var(--ice-muted)';this.style.borderColor='var(--ice-border)'">
            Zrušit filtr
        </button>
    </div>

    <!-- Panel statistik konkrétního týmu — zobrazí se při aktivním filtru -->
    <div id="stats-panel" class="hidden mb-4 p-4 rounded-xl" style="background:rgba(0,50,90,0.25);border:1px solid var(--ice-dark);">
        <p class="font-rajdhani font-bold text-xs tracking-widest mb-3" style="color:var(--ice-mid);">STATISTIKY TÝMU</p>
        <div class="flex flex-wrap gap-6">
            <div class="text-center">
                <div id="stat-team" class="font-orbitron font-black text-lg glow-text">—</div>
                <div class="font-rajdhani text-xs mt-1" style="color:var(--ice-muted);">Název týmu</div>
            </div>
            <div class="text-center">
                <div id="stat-matches" class="font-orbitron font-black text-lg" style="color:var(--ice-text);">0</div>
                <div class="font-rajdhani text-xs mt-1" style="color:var(--ice-muted);">Zápasy celkem</div>
            </div>
            <div class="text-center">
                <div id="stat-wins" class="font-orbitron font-black text-lg" style="color:#4ade80;">0</div>
                <div class="font-rajdhani text-xs mt-1" style="color:var(--ice-muted);">Výhry</div>
            </div>
            <div class="text-center">
                <div id="stat-losses" class="font-orbitron font-black text-lg" style="color:#f87171;">0</div>
                <div class="font-rajdhani text-xs mt-1" style="color:var(--ice-muted);">Prohry</div>
            </div>
            <div class="text-center">
                <div id="stat-draws" class="font-orbitron font-black text-lg" style="color:var(--ice-muted);">0</div>
                <div class="font-rajdhani text-xs mt-1" style="color:var(--ice-muted);">Remízy</div>
            </div>
            <div class="text-center">
                <div id="stat-winrate" class="font-orbitron font-black text-lg" style="color:var(--ice-glow);">—</div>
                <div class="font-rajdhani text-xs mt-1" style="color:var(--ice-muted);">Winrate</div>
            </div>
        </div>
    </div>

    <!-- Tabulka clanwarů -->
    <div class="ice-card rounded-xl overflow-hidden">
        <?php if (empty($clanwars)): ?>
            <div class="p-12 text-center font-rajdhani" style="color:var(--ice-muted);">
                V databázi se zatím nenachází žádné clanwary.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left" id="clanwars-table">
                    <thead>
                        <tr style="background:rgba(0,72,112,0.2);border-bottom:1px solid var(--ice-border);">
                            <th class="px-4 py-4 font-orbitron text-xs tracking-widest text-center" style="color:var(--ice-muted);">ID</th>
                            <th class="px-4 py-4 font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-muted);">TÝM 1</th>
                            <th class="px-4 py-4 font-orbitron text-xs tracking-widest text-center" style="color:var(--ice-muted);">VS</th>
                            <th class="px-4 py-4 font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-muted);">TÝM 2</th>
                            <th class="px-4 py-4 font-orbitron text-xs tracking-widest text-center" style="color:var(--ice-muted);">FMT</th>
                            <th class="px-4 py-4 font-rajdhani font-bold text-xs tracking-widest text-center" style="color:var(--ice-muted);">SKÓRE</th>
                            <th class="px-4 py-4 font-rajdhani font-bold text-xs tracking-widest text-center" style="color:var(--ice-muted);">VÍTĚZ</th>
                            <th class="px-4 py-4 font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-muted);">PŘIDAL</th>
                            <th class="px-4 py-4 font-orbitron text-xs tracking-widest text-center" style="color:var(--ice-muted);">AKCE</th>
                        </tr>
                    </thead>
                    <tbody id="clanwars-body">
                        <?php foreach ($clanwars as $cw): ?>
                            <?php
                                // Celkové skóre = součet tří kol
                                $total1  = $cw['round1_team1'] + $cw['round2_team1'] + $cw['round3_team1'];
                                $total2  = $cw['round1_team2'] + $cw['round2_team2'] + $cw['round3_team2'];
                                $isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';
                                $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] === $cw['created_by'];
                                $author  = !empty($cw['nickname']) ? $cw['nickname'] : ($cw['username'] ?? '—');
                            ?>
                            <!-- data-* atributy slouží JS filtru pro case-insensitive porovnání -->
                            <tr style="border-bottom:1px solid rgba(26,58,92,0.4);"
                                class="clanwar-row hover:bg-cyan-900/5 transition-colors"
                                data-team1="<?= htmlspecialchars(strtolower($cw['team1_name'])) ?>"
                                data-team2="<?= htmlspecialchars(strtolower($cw['team2_name'])) ?>"
                                data-winner="<?= htmlspecialchars(strtolower($cw['winner'])) ?>"
                                data-winner-display="<?= htmlspecialchars($cw['winner']) ?>"
                                data-team1-display="<?= htmlspecialchars($cw['team1_name']) ?>"
                                data-team2-display="<?= htmlspecialchars($cw['team2_name']) ?>">

                                <td class="px-4 py-4 text-center font-orbitron text-xs" style="color:var(--ice-muted);"><?= $cw['id'] ?></td>

                                <!-- Vítězný tým dostane glow-text zvýraznění -->
                                <td class="px-4 py-4 font-rajdhani font-bold text-base <?= ($cw['winner'] === $cw['team1_name']) ? 'glow-text' : '' ?>" style="<?= ($cw['winner'] !== $cw['team1_name']) ? 'color:var(--ice-text)' : '' ?>">
                                    <?= htmlspecialchars($cw['team1_name']) ?>
                                </td>

                                <td class="px-4 py-4 text-center font-orbitron text-xs" style="color:var(--ice-border);">VS</td>

                                <td class="px-4 py-4 font-rajdhani font-bold text-base <?= ($cw['winner'] === $cw['team2_name']) ? 'glow-text' : '' ?>" style="<?= ($cw['winner'] !== $cw['team2_name']) ? 'color:var(--ice-text)' : '' ?>">
                                    <?= htmlspecialchars($cw['team2_name']) ?>
                                </td>

                                <td class="px-4 py-4 text-center">
                                    <span class="font-orbitron text-xs px-2 py-1 rounded" style="background:rgba(0,72,112,0.3);color:var(--ice-text);border:1px solid var(--ice-border);">
                                        <?= htmlspecialchars($cw['team_size']) ?>
                                    </span>
                                </td>

                                <!-- Vyšší skóre dostane cyan barvu -->
                                <td class="px-4 py-4 text-center font-orbitron text-sm">
                                    <span style="color:<?= $total1 >= $total2 ? 'var(--ice-glow)' : 'var(--ice-text)' ?>;"><?= $total1 ?></span>
                                    <span style="color:var(--ice-border);margin:0 4px;">:</span>
                                    <span style="color:<?= $total2 > $total1 ? 'var(--ice-glow)' : 'var(--ice-text)' ?>;"><?= $total2 ?></span>
                                </td>

                                <!-- Odznak vítěze: zelená = výhra fSc, červená = prohra, bílá = remíza -->
                                <td class="px-4 py-4 text-center">
                                    <?php if ($cw['winner'] === 'Remíza'): ?>
                                        <span class="font-rajdhani text-xs px-2 py-1 rounded" style="background:rgba(255,255,255,0.1);color:#ffffff;border:1px solid #ffffff;">Remíza</span>
                                    <?php elseif (strtolower($cw['winner']) === 'fsc'): ?>
                                        <span class="font-orbitron text-xs px-2 py-1 rounded" style="background:rgba(74,222,128,0.1);color:#4ade80;border:1px solid #4ade80;">
                                            <?= htmlspecialchars($cw['winner']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="font-orbitron text-xs px-2 py-1 rounded" style="background:rgba(248,113,113,0.1);color:#f87171;border:1px solid #f87171;">
                                            <?= htmlspecialchars($cw['winner']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-4 font-rajdhani text-sm" style="color:var(--ice-muted);">
                                    <?= htmlspecialchars($author) ?>
                                </td>

                                <!-- Úprava a mazání jsou dostupné jen autorovi záznamu nebo adminovi -->
                                <td class="px-4 py-4 text-center">
                                    <div class="flex justify-center gap-3 font-rajdhani font-semibold text-sm">
                                        <a href="<?= BASE_URL ?>/index.php?url=clanwar/show/<?= $cw['id'] ?>" style="color:var(--ice-glow);" class="hover:text-white transition-colors">Detail</a>
                                        <?php if ($isOwner || $isAdmin): ?>
                                            <a href="<?= BASE_URL ?>/index.php?url=clanwar/edit/<?= $cw['id'] ?>" style="color:#4ade80;" class="hover:text-white transition-colors">Upravit</a>
                                            <a href="<?= BASE_URL ?>/index.php?url=clanwar/delete/<?= $cw['id'] ?>" onclick="return confirm('Opravdu smazat?')" style="color:#f87171;" class="hover:text-white transition-colors">Smazat</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div id="no-results" class="hidden p-10 text-center font-rajdhani" style="color:var(--ice-muted);">
                    Žádný tým neodpovídá hledanému výrazu.
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
const filterInput  = document.getElementById('team-filter');
const rows         = document.querySelectorAll('.clanwar-row');
const noResults    = document.getElementById('no-results');
const totalCount   = document.getElementById('total-count');
const statsPanel   = document.getElementById('stats-panel');

filterInput?.addEventListener('input', applyFilter);

// Filtruje řádky tabulky a aktualizuje počítadlo a statistiky
function applyFilter() {
    const query = filterInput.value.trim().toLowerCase();
    let visible = 0;

    rows.forEach(row => {
        const t1 = row.dataset.team1;
        const t2 = row.dataset.team2;
        const match = !query || t1.includes(query) || t2.includes(query);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    totalCount.textContent = visible;
    noResults.classList.toggle('hidden', visible > 0);

    // Statistiky se zobrazí jen při aktivním filtru
    if (query) {
        updateStats(query);
        statsPanel.classList.remove('hidden');
    } else {
        statsPanel.classList.add('hidden');
    }
}

// Počítá výhry/prohry/remízy pro hledaný tým ze skrytých data-* atributů
function updateStats(query) {
    let matches = 0, wins = 0, losses = 0, draws = 0;

    let displayName = query;
    rows.forEach(row => {
        const t1      = row.dataset.team1;
        const t2      = row.dataset.team2;
        const winner  = row.dataset.winner;
        const t1disp  = row.dataset.team1display;
        const t2disp  = row.dataset.team2display;

        const isT1 = t1.includes(query);
        const isT2 = t2.includes(query);

        if (!isT1 && !isT2) return;

        // Zachytíme původní (neupravené) zobrazované jméno z prvního nalezeného záznamu
        if (displayName === query) {
            displayName = isT1 ? t1disp : t2disp;
        }

        matches++;

        if (winner === 'remíza') {
            draws++;
        } else if (isT1 && winner === t1) {
            wins++;
        } else if (isT2 && winner === t2) {
            wins++;
        } else {
            losses++;
        }
    });

    const winrate = matches > 0 ? Math.round((wins / matches) * 100) + '%' : '—';

    document.getElementById('stat-team').textContent     = displayName || query;
    document.getElementById('stat-matches').textContent  = matches;
    document.getElementById('stat-wins').textContent     = wins;
    document.getElementById('stat-losses').textContent   = losses;
    document.getElementById('stat-draws').textContent    = draws;
    document.getElementById('stat-winrate').textContent  = winrate;
}

function clearFilter() {
    if (filterInput) filterInput.value = '';
    applyFilter();
}
</script>

<?php require_once '../app/views/layout/footer.php'; ?>
