<!DOCTYPE html>
<html lang="cs" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS načítaný přímo z CDN — pro produkci nahradit build procesem -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>FSC ClanWar Tracker</title>
    <style>
        /* Google Fonts: Orbitron (nadpisy) + Rajdhani (text) */
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap');

        /* Globální CSS proměnné — ice/cyan barevná paleta */
        :root {
            --ice-bg:       #050a12;
            --ice-surface:  #0a1628;
            --ice-border:   #1a3a5c;
            --ice-glow:     #00c8ff;  /* základní cyan záře */
            --ice-mid:      #0090c8;
            --ice-dark:     #004870;
            --ice-text:     #b8e8ff;
            --ice-muted:    #4a7a9b;
        }

        body { background-color: var(--ice-bg); }

        .font-orbitron { font-family: 'Orbitron', monospace; }
        .font-rajdhani { font-family: 'Rajdhani', sans-serif; }

        /* Karta s tmavým gradientem a jemným svítícím rámečkem */
        .ice-card {
            background: linear-gradient(135deg, rgba(10,22,40,0.95) 0%, rgba(5,15,30,0.98) 100%);
            border: 1px solid var(--ice-border);
            box-shadow: 0 0 30px rgba(0,200,255,0.05), inset 0 1px 0 rgba(0,200,255,0.1);
        }

        /* Vstupní pole — tmavé pozadí, při fokusu cyan záře */
        .ice-input {
            background: rgba(5,10,20,0.8);
            border: 1px solid var(--ice-border);
            color: var(--ice-text);
            transition: all 0.2s;
        }
        .ice-input:focus {
            outline: none;
            border-color: var(--ice-glow);
            box-shadow: 0 0 12px rgba(0,200,255,0.25);
        }

        /* Tlačítko s modrým gradientem a hover efektem */
        .ice-btn {
            background: linear-gradient(135deg, #005a8a 0%, #003a6a 50%, #001a4a 100%);
            border: 1px solid var(--ice-mid);
            color: var(--ice-glow);
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            transition: all 0.2s;
            box-shadow: 0 0 15px rgba(0,144,200,0.2), inset 0 1px 0 rgba(0,200,255,0.15);
        }
        .ice-btn:hover {
            border-color: var(--ice-glow);
            box-shadow: 0 0 25px rgba(0,200,255,0.4), inset 0 1px 0 rgba(0,200,255,0.25);
            color: #fff;
        }

        /* Popisek formulářového pole */
        .ice-label {
            font-family: 'Rajdhani', sans-serif;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--ice-muted);
            font-size: 0.7rem;
        }

        /* Svítící text — používá se pro nadpisy a zvýrazněné hodnoty */
        .glow-text { color: var(--ice-glow); text-shadow: 0 0 20px rgba(0,200,255,0.5); }

        .nav-link {
            font-family: 'Rajdhani', sans-serif;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--ice-text);
            transition: color 0.2s;
            font-size: 0.8rem;
        }
        .nav-link:hover { color: var(--ice-glow); }

        /* Tmavá hlavička s subtilním stínem */
        .header-bg {
            background: linear-gradient(180deg, #071520 0%, var(--ice-bg) 100%);
            border-bottom: 1px solid var(--ice-border);
            box-shadow: 0 4px 30px rgba(0,200,255,0.07);
        }

        /* Malý odznak zobrazující roli uživatele (admin/member/user) */
        .role-badge {
            font-family: 'Orbitron', monospace;
            font-size: 0.55rem;
            letter-spacing: 0.1em;
            padding: 2px 7px;
            border-radius: 3px;
        }
    </style>
</head>
<body class="text-slate-200 min-h-screen flex flex-col font-rajdhani" style="background-color:#050a12;">

    <header class="header-bg">
        <div class="container mx-auto px-6 py-4 flex flex-col md:flex-row justify-between items-center">

            <!-- Logo a název aplikace -->
            <a href="<?= BASE_URL ?>/index.php" class="flex items-center gap-3">
                <img src="<?= BASE_URL ?>/fsclogo.png" alt="FSC" class="h-10 w-auto drop-shadow-lg" onerror="this.style.display='none'">
                <div>
                    <span class="font-orbitron font-black text-xl tracking-widest glow-text">FSC</span>
                    <span class="font-rajdhani text-slate-400 text-sm ml-2 tracking-widest uppercase">ClanWar Tracker</span>
                </div>
            </a>

            <!-- Hlavní navigace -->
            <nav class="mt-4 md:mt-0">
                <ul class="flex items-center gap-6">
                    <li><a href="<?= BASE_URL ?>/index.php" class="nav-link">Seznam clanwarů</a></li>
                    <li><a href="<?= BASE_URL ?>/index.php?url=clanwar/team" class="nav-link">Team</a></li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Tlačítko pro přidání clanwaru — vidí jen admin a member -->
                        <?php if (in_array($_SESSION['user_role'] ?? '', ['admin','member'])): ?>
                            <li>
                                <a href="<?= BASE_URL ?>/index.php?url=clanwar/create" class="ice-btn px-4 py-2 rounded text-sm font-bold font-rajdhani">
                                    + Přidat clanwar
                                </a>
                            </li>
                        <?php endif; ?>

                        <li><a href="<?= BASE_URL ?>/index.php?url=user/profile" class="nav-link">Profil</a></li>

                        <!-- Zobrazení jména a barevně odlišeného role-odznaku -->
                        <li class="flex items-center gap-2 text-sm">
                            <span style="color:var(--ice-muted);">Vítej,</span>
                            <span class="font-orbitron text-xs" style="color:var(--ice-glow);"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                            <?php
                                // Styl odznaku se liší podle role: admin = červená, member = modrá, user = šedá
                                $role = $_SESSION['user_role'] ?? 'user';
                                if ($role === 'admin')        $rb = 'background:#0a1a30;color:#f87171;border:1px solid #7f1d1d;';
                                elseif ($role === 'member')   $rb = 'background:#0a1a30;color:var(--ice-glow);border:1px solid var(--ice-dark);';
                                else                          $rb = 'background:#0a1020;color:var(--ice-muted);border:1px solid #1a3050;';
                            ?>
                            <span class="role-badge" style="<?= $rb ?>"><?= strtoupper($role) ?></span>
                        </li>

                        <li>
                            <a href="<?= BASE_URL ?>/index.php?url=auth/logout" class="nav-link" style="color:#f87171;">Odhlásit</a>
                        </li>

                    <?php else: ?>
                        <!-- Nepřihlášený uživatel vidí jen přihlášení a registraci -->
                        <li><a href="<?= BASE_URL ?>/index.php?url=auth/login" class="nav-link">Přihlásit</a></li>
                        <li>
                            <a href="<?= BASE_URL ?>/index.php?url=auth/register"
                               style="border:1px solid var(--ice-border);color:var(--ice-text);font-family:'Rajdhani',sans-serif;font-weight:600;letter-spacing:0.08em;"
                               class="px-4 py-2 rounded text-sm uppercase hover:border-cyan-500 transition-colors">
                                Registrace
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Flash zprávy ze session (success, error, notice) — po vykreslení se smažou -->
    <div class="container mx-auto px-6 pt-5">
        <?php if (!empty($_SESSION['messages'])): ?>
            <div class="space-y-2">
                <?php foreach ($_SESSION['messages'] as $type => $msgs): ?>
                    <?php
                        // Mapování typu zprávy na vizuální styl (barva pozadí, rámečku a textu)
                        $s = [
                            'success' => 'background:rgba(0,40,20,0.6);border-color:#16a34a;color:#4ade80;',
                            'error'   => 'background:rgba(40,0,0,0.6);border-color:#dc2626;color:#f87171;',
                            'notice'  => 'background:rgba(40,30,0,0.6);border-color:#ca8a04;color:#fbbf24;',
                        ];
                        $style = $s[$type] ?? 'background:rgba(10,22,40,0.8);border-color:var(--ice-border);color:var(--ice-text);';
                    ?>
                    <?php foreach ($msgs as $msg): ?>
                        <div style="<?= $style ?> border-left:3px solid;" class="px-4 py-3 rounded-r font-rajdhani font-semibold text-sm tracking-wide">
                            <?= htmlspecialchars($msg) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <?php unset($_SESSION['messages']); ?>
            </div>
        <?php endif; ?>
    </div>
