<?php require_once '../app/views/layout/header.php'; ?>

<!-- Formulář pro vytvoření nového clanwaru — odesílá POST na clanwar/store -->
<main class="container mx-auto px-6 py-10 flex-grow">
    <div class="max-w-4xl mx-auto">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-orbitron font-black text-2xl tracking-widest glow-text">NOVÝ CLANWAR</h2>
                <p class="font-rajdhani text-sm tracking-wider mt-1" style="color:var(--ice-muted);">Zaznamenejte výsledky zápasu</p>
            </div>
            <a href="<?= BASE_URL ?>/index.php" class="font-rajdhani text-sm tracking-wider hover:text-white transition-colors" style="color:var(--ice-muted);">&larr; Zpět</a>
        </div>

        <!-- enctype="multipart/form-data" je nutný pro nahrávání obrázku -->
        <div class="ice-card rounded-xl p-6 md:p-8">
            <form action="<?= BASE_URL ?>/index.php?url=clanwar/store" method="post" enctype="multipart/form-data">

                <!-- Sekce: výběr formátu zápasu (2v2 nebo 3v3) -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4 pb-2" style="border-bottom:1px solid var(--ice-border);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-mid);">FORMÁT ZÁPASU</span>
                    </div>
                    <div class="flex gap-6">
                        <!-- onchange volá JS funkci, která zobrazí/skryje pole pro třetího hráče -->
                        <?php foreach (['2v2','3v3'] as $sz): ?>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="team_size" value="<?= $sz ?>" <?= $sz==='2v2'?'checked':'' ?>
                                       onchange="updatePlayerFields(this.value)"
                                       style="accent-color:var(--ice-glow);">
                                <span class="font-orbitron text-sm tracking-widest group-hover:text-white transition-colors" style="color:var(--ice-text);"><?= $sz ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Sekce: názvy a složení týmů -->
                <div class="mb-8">
                    <div class="pb-2 mb-4" style="border-bottom:1px solid var(--ice-border);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-mid);">TÝMY A HRÁČI</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <!-- Team1 je vždy fSc — pole je disabled a neposílá se v POST -->
                            <label class="ice-label block mb-1">Náš tým <span style="color:#f87171;">*</span></label>
                            <input type="text" value="fSc" disabled
                                   class="ice-input w-full rounded px-4 py-2.5 text-sm mb-3 opacity-50 cursor-not-allowed">
                            <div class="space-y-2">
                                <input type="text" name="team1_players[]" placeholder="Hráč 1" class="ice-input w-full rounded px-4 py-2 text-sm placeholder-slate-700">
                                <input type="text" name="team1_players[]" placeholder="Hráč 2" class="ice-input w-full rounded px-4 py-2 text-sm placeholder-slate-700">
                                <!-- Třetí hráč je skrytý při 2v2; JS ho zobrazí při 3v3 -->
                                <input type="text" name="team1_players[]" placeholder="Hráč 3" id="team1_player3"
                                       class="ice-input w-full rounded px-4 py-2 text-sm placeholder-slate-700 hidden">
                            </div>
                        </div>
                        <div>
                            <label class="ice-label block mb-1">Nepřátelský tým <span style="color:#f87171;">*</span></label>
                            <input type="text" name="team2_name" required
                                   class="ice-input w-full rounded px-4 py-2.5 text-sm mb-3">
                            <div class="space-y-2">
                                <input type="text" name="team2_players[]" placeholder="Hráč 1" class="ice-input w-full rounded px-4 py-2 text-sm placeholder-slate-700">
                                <input type="text" name="team2_players[]" placeholder="Hráč 2" class="ice-input w-full rounded px-4 py-2 text-sm placeholder-slate-700">
                                <input type="text" name="team2_players[]" placeholder="Hráč 3" id="team2_player3"
                                       class="ice-input w-full rounded px-4 py-2 text-sm placeholder-slate-700 hidden">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sekce: skóre tří kol (max 30 bodů za kolo) -->
                <div class="mb-8">
                    <div class="pb-2 mb-4" style="border-bottom:1px solid var(--ice-border);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-mid);">SKÓRE KOL</span>
                        <span class="font-rajdhani text-xs ml-2" style="color:var(--ice-muted);">max 30 bodů za kolo</span>
                    </div>
                    <div class="space-y-3">
                        <!-- Tři kola generovaná smyčkou -->
                        <?php foreach ([1,2,3] as $r): ?>
                            <div class="flex items-center gap-4 px-4 py-3 rounded" style="background:rgba(0,30,60,0.4);border:1px solid var(--ice-border);">
                                <span class="font-orbitron text-xs tracking-widest w-16" style="color:var(--ice-muted);">KOLO <?= $r ?></span>
                                <input type="number" name="round<?= $r ?>_team1" min="0" max="30" value="0"
                                       class="ice-input w-20 rounded px-3 py-2 text-center font-orbitron text-sm" oninput="updateTotal()">
                                <span class="font-orbitron font-black" style="color:var(--ice-border);">:</span>
                                <input type="number" name="round<?= $r ?>_team2" min="0" max="30" value="0"
                                       class="ice-input w-20 rounded px-3 py-2 text-center font-orbitron text-sm" oninput="updateTotal()">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Live náhled celkového skóre a předpovězený vítěz -->
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

                <!-- Sekce: volitelný screenshot výsledku -->
                <div class="mb-8">
                    <div class="pb-2 mb-4" style="border-bottom:1px solid var(--ice-border);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-mid);">PŘÍLOHY <span class="font-rajdhani normal-case text-xs">(volitelné)</span></span>
                    </div>
                    <!-- Povolené formáty jsou ověřovány i na serveru v ClanwarController::store() -->
                    <label class="ice-label block mb-1">Screenshot výsledku</label>
                    <input type="file" name="image" accept="image/jpeg, image/png, image/webp, image/gif"
                           class="ice-input w-full rounded px-4 py-2.5 text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-cyan-900 file:text-cyan-100 hover:file:bg-cyan-800">
                </div>

                <!-- Sekce: volitelná textová poznámka -->
                <div class="mb-8">
                    <div class="pb-2 mb-4" style="border-bottom:1px solid var(--ice-border);">
                        <span class="font-rajdhani font-bold text-xs tracking-widest" style="color:var(--ice-mid);">POZNÁMKA <span class="font-rajdhani normal-case text-xs">(volitelné)</span></span>
                    </div>
                    <textarea name="note" rows="3" placeholder="Doplňující informace k zápasu..."
                              class="ice-input w-full rounded px-4 py-2.5 text-sm placeholder-slate-700"></textarea>
                </div>

                <button type="submit" class="ice-btn w-full py-3 rounded font-bold text-sm">
                    Uložit clanwar
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

// Aktualizuje live náhled celkového skóre a vítěze při každé změně vstupu
function updateTotal() {
    // Hodnoty jsou oříznuty na rozsah 0–30 stejně jako v PHP kontroleru
    const v = name => Math.min(30, Math.max(0, parseInt(document.querySelector(`input[name="${name}"]`)?.value)||0));
    const t1 = v('round1_team1')+v('round2_team1')+v('round3_team1');
    const t2 = v('round1_team2')+v('round2_team2')+v('round3_team2');
    document.getElementById('total1').textContent = t1;
    document.getElementById('total2').textContent = t2;
    const n1 = 'fSc';
    const n2 = document.querySelector('input[name="team2_name"]')?.value||'Tým 2';
    // Při remíze (t1===t2 > 0) zobrazí "Remíza", při 0:0 "—"
    document.getElementById('winner_preview').textContent = t1>t2?n1:t2>t1?n2:(t1>0?'Remíza':'—');
}

// Přepočítá náhled i při změně názvu soupeře
document.addEventListener('input', e => { if(e.target.name === 'team2_name') updateTotal(); });
</script>

<?php require_once '../app/views/layout/footer.php'; ?>
