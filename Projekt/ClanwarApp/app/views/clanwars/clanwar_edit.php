<?php require_once '../app/views/layout/header.php'; ?>

<?php
    // Předpřipravení dat pro předvyplnění formuláře
    $teamSize = htmlspecialchars($clanwar['team_size']);
    $t1p = $clanwar['team1_players']; // pole hráčů team1 (JSON už dekódovaný v kontroleru)
    $t2p = $clanwar['team2_players']; // pole hráčů team2
    $rounds = [
        1 => [$clanwar['round1_team1'], $clanwar['round1_team2']],
        2 => [$clanwar['round2_team1'], $clanwar['round2_team2']],
        3 => [$clanwar['round3_team1'], $clanwar['round3_team2']],
    ];
?>

<!-- Formulář pro úpravu existujícího clanwaru — odesílá POST na clanwar/update/{id} -->
<main class="container mx-auto px-6 py-10 flex-grow">
    <div class="max-w-4xl mx-auto">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-orbitron font-black text-2xl tracking-widest" style="color:#4ade80;text-shadow:0 0 20px rgba(74,222,128,0.4);">
                    UPRAVIT CLANWAR <span style="color:var(--ice-glow);">#<?= $clanwar['id'] ?></span>
                </h2>
                <p class="font-rajdhani text-sm mt-1" style="color:var(--ice-muted);">
                    fSc vs <?= htmlspecialchars($clanwar['team2_name']) ?>
                </p>
            </div>
            <a href="<?= BASE_URL ?>/index.php?url=clanwar/show/<?= $clanwar['id'] ?>" class="font-rajdhani text-sm tracking-wider hover:text-white transition-colors" style="color:var(--ice-muted);">&larr; Zpět</a>
        </div>

        <div class="ice-card rounded-xl p-6 md:p-8">
            <form action="<?= BASE_URL ?>/index.php?url=clanwar/update/<?= $clanwar['id'] ?>" method="post" enctype="multipart/form-data">

                <!-- Sekce: formát zápasu — předvyplněno z DB -->
                <div class="mb-8">
                    <div class="pb-2 mb-4" style="border-bottom:1px solid var(--ice-border);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-mid);">FORMÁT ZÁPASU</span>
                    </div>
                    <div class="flex gap-6">
                        <?php foreach (['2v2','3v3'] as $sz): ?>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="team_size" value="<?= $sz ?>"
                                       <?= $teamSize===$sz?'checked':'' ?>
                                       onchange="updatePlayerFields(this.value)"
                                       style="accent-color:var(--ice-glow);">
                                <span class="font-orbitron text-sm tracking-widest" style="color:var(--ice-text);"><?= $sz ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Sekce: týmy a hráči — předvyplněno z DB -->
                <div class="mb-8">
                    <div class="pb-2 mb-4" style="border-bottom:1px solid var(--ice-border);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-mid);">TÝMY A HRÁČI</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <!-- Team1 je vždy fSc — nelze změnit -->
                            <label class="ice-label block mb-1">Náš tým <span style="color:#f87171;">*</span></label>
                            <input type="text" value="fSc" disabled
                                   class="ice-input w-full rounded px-4 py-2.5 text-sm mb-3 opacity-50 cursor-not-allowed">
                            <div class="space-y-2">
                                <!-- Třetí pole se skryje nebo zobrazí JS funkcí podle zvoleného formátu -->
                                <?php for ($i=0;$i<3;$i++): ?>
                                    <input type="text" name="team1_players[]"
                                           placeholder="Hráč <?= $i+1 ?>"
                                           value="<?= htmlspecialchars($t1p[$i]??'') ?>"
                                           id="team1_player<?= $i+1 ?>"
                                           class="ice-input w-full rounded px-4 py-2 text-sm placeholder-slate-700 <?= ($i===2&&$teamSize!=='3v3')?'hidden':'' ?>">
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div>
                            <label class="ice-label block mb-1">Nepřátelský tým <span style="color:#f87171;">*</span></label>
                            <input type="text" name="team2_name" required
                                   value="<?= htmlspecialchars($clanwar["team2_name"]) ?>"
                                   class="ice-input w-full rounded px-4 py-2.5 text-sm mb-3" style="color:#a7f3d0;">
                            <div class="space-y-2">
                                <?php for ($i=0;$i<3;$i++): ?>
                                    <input type="text" name="team2_players[]"
                                           placeholder="Hráč <?= $i+1 ?>"
                                           value="<?= htmlspecialchars($t2p[$i]??'') ?>"
                                           id="team2_player<?= $i+1 ?>"
                                           class="ice-input w-full rounded px-4 py-2 text-sm placeholder-slate-700 <?= ($i===2&&$teamSize!=='3v3')?'hidden':'' ?>">
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sekce: skóre kol — předvyplněno stávajícími hodnotami -->
                <div class="mb-8">
                    <div class="pb-2 mb-4" style="border-bottom:1px solid var(--ice-border);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-mid);">SKÓRE KOL</span>
                        <span class="font-rajdhani text-xs ml-2" style="color:var(--ice-muted);">max 30 bodů za kolo</span>
                    </div>
                    <div class="space-y-3">
                        <?php foreach ($rounds as $r => [$s1,$s2]): ?>
                            <div class="flex items-center gap-4 px-4 py-3 rounded" style="background:rgba(0,30,60,0.4);border:1px solid var(--ice-border);">
                                <span class="font-orbitron text-xs tracking-widest w-16" style="color:var(--ice-muted);">KOLO <?= $r ?></span>
                                <input type="number" name="round<?= $r ?>_team1" min="0" max="30" value="<?= $s1 ?>"
                                       class="ice-input w-20 rounded px-3 py-2 text-center font-orbitron text-sm" oninput="updateTotal()">
                                <span class="font-orbitron font-black" style="color:var(--ice-border);">:</span>
                                <input type="number" name="round<?= $r ?>_team2" min="0" max="30" value="<?= $s2 ?>"
                                       class="ice-input w-20 rounded px-3 py-2 text-center font-orbitron text-sm" oninput="updateTotal()">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Live náhled celkového skóre (načte se z DB hodnot při DOMContentLoaded) -->
                    <div class="mt-4 flex items-center justify-between px-6 py-4 rounded" style="background:rgba(0,50,90,0.3);border:1px solid var(--ice-dark);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-muted);">CELKOVÉ SKÓRE</span>
                        <div class="font-orbitron font-black text-3xl">
                            <span id="total1" class="glow-text">0</span>
                            <span style="color:var(--ice-border);margin:0 8px;">:</span>
                            <span id="total2" class="glow-text">0</span>
                        </div>
                        <div class="font-rajdhani text-sm" style="color:var(--ice-muted);">
                            Vítěz: <span id="winner_preview" class="font-bold" style="color:var(--ice-glow);">—</span>
                        </div>
                    </div>
                </div>

                <!-- Sekce: poznámka — předvyplněna z DB -->
                <div class="mb-8">
                    <div class="pb-2 mb-4" style="border-bottom:1px solid var(--ice-border);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-muted);">POZNÁMKA</span>
                    </div>
                    <textarea name="note" rows="3" class="ice-input w-full rounded px-4 py-2.5 text-sm"><?= htmlspecialchars($clanwar['note']??'') ?></textarea>
                </div>

                <!-- Sekce: screenshot zápasu -->
                <div class="mb-8">
                    <div class="pb-2 mb-4" style="border-bottom:1px solid var(--ice-border);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-mid);">SCREENSHOT ZÁPASU</span>
                    </div>
                    <?php if (!empty($clanwar['image_path'])): ?>
                        <div class="mb-3 p-3 rounded text-sm font-rajdhani" style="background:rgba(161,110,0,0.15);border:1px solid rgba(251,191,36,0.3);color:#fbbf24;">
                            Aktuální obrázek: <span class="font-bold"><?= htmlspecialchars(basename($clanwar['image_path'])) ?></span><br>
                            <span style="color:#f59e0b;">Upozornění: Pokud nyní nahrajete nový soubor, tento starý bude přepsán.</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                           class="ice-input w-full rounded px-4 py-2.5 text-sm" style="color:var(--ice-text);">
                </div>

                <button type="submit" class="w-full py-3 rounded font-orbitron font-bold text-sm tracking-widest transition-all"
                        style="background:linear-gradient(135deg,#064e3b,#022c22);border:1px solid #16a34a;color:#4ade80;box-shadow:0 0 15px rgba(74,222,128,0.15);"
                        onmouseover="this.style.boxShadow='0 0 25px rgba(74,222,128,0.35)'"
                        onmouseout="this.style.boxShadow='0 0 15px rgba(74,222,128,0.15)'">
                    Uložit změny
                </button>

            </form>
        </div>
    </div>
</main>

<script>
// Zobrazí nebo skryje pole pro třetího hráče podle zvoleného formátu
function updatePlayerFields(size) {
    [1,2].forEach(t => {
        const el = document.getElementById(`team${t}_player3`);
        if (el) el.classList.toggle('hidden', size !== '3v3');
    });
}

// Aktualizuje live náhled celkového skóre a vítěze
function updateTotal() {
    const v = name => Math.min(30,Math.max(0,parseInt(document.querySelector(`input[name="${name}"]`)?.value)||0));
    const t1 = v('round1_team1')+v('round2_team1')+v('round3_team1');
    const t2 = v('round1_team2')+v('round2_team2')+v('round3_team2');
    document.getElementById('total1').textContent = t1;
    document.getElementById('total2').textContent = t2;
    const n1 = 'fSc';
    const n2 = document.querySelector('input[name="team2_name"]')?.value||'Tým 2';
    document.getElementById('winner_preview').textContent = t1>t2?n1:t2>t1?n2:(t1>0?'Remíza':'—');
}

// Spustí výpočet ihned po načtení, aby náhled odpovídal předvyplněným hodnotám z DB
document.addEventListener('DOMContentLoaded', updateTotal);
document.addEventListener('input', e => { if(e.target.name === 'team2_name') updateTotal(); });
</script>

<?php require_once '../app/views/layout/footer.php'; ?>
