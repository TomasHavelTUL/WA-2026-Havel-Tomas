<?php
/**
 * Hlavní kontroler aplikace.
 * Spravuje seznam clanwarů, jejich CRUD operace, komentáře a stránku týmu.
 */
class ClanwarController {

    /**
     * Zkontroluje, zda má přihlášený uživatel roli "admin".
     *
     * @return bool True pokud je uživatel admin.
     */
    private function isAdmin(): bool {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Zkontroluje, zda má přihlášený uživatel roli "member" nebo "admin".
     * Tato úroveň opravňuje k vytváření a úpravě clanwarů.
     *
     * @return bool True pokud je uživatel člen nebo admin.
     */
    private function isMember(): bool {
        return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'member']);
    }

    /**
     * Zkontroluje, zda je uživatel přihlášen (má platnou session).
     *
     * @return bool True pokud session obsahuje user_id.
     */
    private function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    /**
     * Výchozí akce — zobrazí seznam všech clanwarů seřazených od nejnovějšího.
     * Dostupné pro všechny návštěvníky bez přihlášení.
     * GET /
     */
    public function index(): void {
        require_once '../app/models/Database.php';
        require_once '../app/models/Clanwar.php';

        $db           = (new Database())->getConnection();
        $clanwarModel = new Clanwar($db);
        $clanwars     = $clanwarModel->getAll();

        require_once '../app/views/clanwars/clanwars_list.php';
    }

    /**
     * Zobrazí prázdný formulář pro vytvoření nového clanwaru.
     * Přístupné pouze pro členy a adminy.
     * GET /clanwar/create
     */
    public function create(): void {
        if (!$this->isMember()) {
            $this->addErrorMessage('Pro přidání clanwaru musíte mít roli člena nebo administrátora.');
            header('Location: ' . BASE_URL . '/index.php?url=auth/login');
            exit;
        }
        require_once '../app/views/clanwars/clanwar_create.php';
    }

    /**
     * Zpracuje POST data z formuláře pro vytvoření clanwaru.
     * Volitelně nahraje obrázek, validuje vstupy, vypočítá vítěze a uloží záznam.
     * Skóre každého kola je ořezáno na rozsah 0–30 (max kills v kole).
     * POST /clanwar/store
     */
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->addNoticeMessage('Neplatný požadavek.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        if (!$this->isMember()) {
            $this->addErrorMessage('Nemáte oprávnění přidávat clanwary.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/Clanwar.php';

        // Obrázek je volitelný; uložíme ho pouze pokud byl nahrán a je v povoleném formátu
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (in_array($_FILES['image']['type'], $allowedTypes)) {
                $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('cw_') . '.' . $ext;

                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }

                $targetPath = 'uploads/' . $filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = $targetPath;
                }
            } else {
                $this->addErrorMessage('Nepodporovaný formát obrázku (pouze JPG, PNG, WEBP, GIF).');
                header('Location: ' . BASE_URL . '/index.php?url=clanwar/create');
                exit;
            }
        }

        $team1Name = 'fSc'; // náš tým je vždy team1
        $team2Name = htmlspecialchars($_POST['team2_name'] ?? '');
        $teamSize  = in_array($_POST['team_size'] ?? '', ['2v2', '3v3']) ? $_POST['team_size'] : '2v2';
        $note      = htmlspecialchars($_POST['note'] ?? '');

        // Sestavení polí hráčů podle zvoleného formátu (2 nebo 3 hráči)
        $maxPlayers   = ($teamSize === '3v3') ? 3 : 2;
        $team1Players = [];
        $team2Players = [];
        for ($i = 0; $i < $maxPlayers; $i++) {
            $team1Players[] = htmlspecialchars($_POST['team1_players'][$i] ?? '');
            $team2Players[] = htmlspecialchars($_POST['team2_players'][$i] ?? '');
        }

        // Skóre omezeno na 0–30 (maximální počet kills v jednom kole)
        $round1Team1 = min(30, max(0, (int)($_POST['round1_team1'] ?? 0)));
        $round1Team2 = min(30, max(0, (int)($_POST['round1_team2'] ?? 0)));
        $round2Team1 = min(30, max(0, (int)($_POST['round2_team1'] ?? 0)));
        $round2Team2 = min(30, max(0, (int)($_POST['round2_team2'] ?? 0)));
        $round3Team1 = min(30, max(0, (int)($_POST['round3_team1'] ?? 0)));
        $round3Team2 = min(30, max(0, (int)($_POST['round3_team2'] ?? 0)));

        $winner = Clanwar::calculateWinner(
            $team1Name, $team2Name,
            $round1Team1, $round1Team2,
            $round2Team1, $round2Team2,
            $round3Team1, $round3Team2
        );

        $db           = (new Database())->getConnection();
        $clanwarModel = new Clanwar($db);

        $isSaved = $clanwarModel->create(
            $team1Name, $team2Name, $teamSize,
            $team1Players, $team2Players,
            $round1Team1, $round1Team2,
            $round2Team1, $round2Team2,
            $round3Team1, $round3Team2,
            $winner, $note,
            $_SESSION['user_id'],
            $imagePath
        );

        if ($isSaved) {
            $this->addSuccessMessage('Clanwar byl úspěšně uložen!');
            header('Location: ' . BASE_URL . '/index.php');
        } else {
            $this->addErrorMessage('Nastala chyba při ukládání clanwaru.');
            header('Location: ' . BASE_URL . '/index.php?url=clanwar/create');
        }
        exit;
    }

    /**
     * Zobrazí detail jednoho clanwaru včetně komentářů.
     * JSON pole hráčů jsou dekódována z DB formátu do PHP pole.
     * Dostupné pro všechny návštěvníky.
     * GET /clanwar/show/{id}
     *
     * @param mixed $id ID clanwaru z URL.
     */
    public function show($id = null): void {
        if (!$id) {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/Clanwar.php';
        require_once '../app/models/Comment.php';

        $db           = (new Database())->getConnection();
        $clanwarModel = new Clanwar($db);
        $commentModel = new Comment($db);

        $clanwar = $clanwarModel->getById((int)$id);
        if (!$clanwar) {
            $this->addErrorMessage('Clanwar nebyl nalezen.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $comments = $commentModel->getByClanwarId((int)$id);

        // Dekódování JSON polí hráčů uložených v DB jako řetězec
        $clanwar['team1_players'] = json_decode($clanwar['team1_players'] ?? '[]', true) ?? [];
        $clanwar['team2_players'] = json_decode($clanwar['team2_players'] ?? '[]', true) ?? [];

        require_once '../app/views/clanwars/clanwar_detail.php';
    }

    /**
     * Zpracuje POST data pro přidání komentáře k clanwaru.
     * Komentovat mohou pouze přihlášení uživatelé.
     * POST /clanwar/storeComment/{id}
     *
     * @param mixed $id ID clanwaru, ke kterému se komentář přidává.
     */
    public function storeComment($id = null): void {
        if (!$this->isLoggedIn()) {
            $this->addErrorMessage('Pro komentování se musíte přihlásit.');
            header('Location: ' . BASE_URL . '/index.php?url=auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $content = htmlspecialchars(trim($_POST['content'] ?? ''));
        if (empty($content)) {
            $this->addErrorMessage('Komentář nemůže být prázdný.');
            header('Location: ' . BASE_URL . '/index.php?url=clanwar/show/' . $id);
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/Comment.php';

        $db           = (new Database())->getConnection();
        $commentModel = new Comment($db);

        if ($commentModel->create((int)$id, $_SESSION['user_id'], $content)) {
            $this->addSuccessMessage('Komentář byl přidán.');
        } else {
            $this->addErrorMessage('Nepodařilo se přidat komentář.');
        }

        header('Location: ' . BASE_URL . '/index.php?url=clanwar/show/' . $id);
        exit;
    }

    /**
     * Zobrazí formulář pro úpravu komentáře.
     * Úprava je povolena pouze autorovi komentáře.
     * GET /clanwar/editComment/{id}
     *
     * @param mixed $id ID komentáře k úpravě.
     */
    public function editComment($id = null): void {
        if (!$this->isLoggedIn() || !$id) {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/Comment.php';

        $db           = (new Database())->getConnection();
        $commentModel = new Comment($db);
        $comment      = $commentModel->getById((int)$id);

        // Admin nemůže upravovat cizí komentáře — editace je výsada autora
        if (!$comment || $comment['user_id'] !== $_SESSION['user_id']) {
            $this->addErrorMessage('Nemáte oprávnění upravovat tento komentář.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        require_once '../app/views/clanwars/comment_edit.php';
    }

    /**
     * Zpracuje POST data z formuláře pro úpravu komentáře.
     * Oprávnění ověří znovu na serveru (obrana proti CSRF/přímému POST).
     * POST /clanwar/updateComment/{id}
     *
     * @param mixed $id ID komentáře k aktualizaci.
     */
    public function updateComment($id = null): void {
        if (!$this->isLoggedIn() || !$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/Comment.php';

        $db           = (new Database())->getConnection();
        $commentModel = new Comment($db);
        $comment      = $commentModel->getById((int)$id);

        if (!$comment || $comment['user_id'] !== $_SESSION['user_id']) {
            $this->addErrorMessage('Nemáte oprávnění upravovat tento komentář.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $content = htmlspecialchars(trim($_POST['content'] ?? ''));
        if (empty($content)) {
            $this->addErrorMessage('Komentář nemůže být prázdný.');
            header('Location: ' . BASE_URL . '/index.php?url=clanwar/editComment/' . $id);
            exit;
        }

        if ($commentModel->update((int)$id, $content)) {
            $this->addSuccessMessage('Komentář byl úspěšně upraven.');
        } else {
            $this->addErrorMessage('Nepodařilo se upravit komentář.');
        }

        header('Location: ' . BASE_URL . '/index.php?url=clanwar/show/' . $comment['clanwar_id']);
        exit;
    }

    /**
     * Smaže komentář.
     * Smazat může autor komentáře nebo admin (moderace).
     * GET /clanwar/deleteComment/{id}
     *
     * @param mixed $id ID komentáře ke smazání.
     */
    public function deleteComment($id = null): void {
        if (!$this->isLoggedIn() || !$id) {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/Comment.php';

        $db           = (new Database())->getConnection();
        $commentModel = new Comment($db);
        $comment      = $commentModel->getById((int)$id);

        if (!$comment) {
            $this->addErrorMessage('Komentář nebyl nalezen.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $clanwarId = $comment['clanwar_id'];

        // Admin může smazat libovolný komentář (moderace); běžný uživatel jen svůj
        if ($comment['user_id'] !== $_SESSION['user_id'] && !$this->isAdmin()) {
            $this->addErrorMessage('Nemáte oprávnění smazat tento komentář.');
            header('Location: ' . BASE_URL . '/index.php?url=clanwar/show/' . $clanwarId);
            exit;
        }

        if ($commentModel->delete((int)$id)) {
            $this->addSuccessMessage('Komentář byl smazán.');
        } else {
            $this->addErrorMessage('Nepodařilo se smazat komentář.');
        }

        header('Location: ' . BASE_URL . '/index.php?url=clanwar/show/' . $clanwarId);
        exit;
    }

    /**
     * Zobrazí formulář pro úpravu existujícího clanwaru.
     * Editovat může autor záznamu nebo admin.
     * JSON pole hráčů jsou dekódována pro předvyplnění formuláře.
     * GET /clanwar/edit/{id}
     *
     * @param mixed $id ID clanwaru k úpravě.
     */
    public function edit($id = null): void {
        if (!$this->isMember()) {
            $this->addErrorMessage('Nemáte oprávnění upravovat clanwary.');
            header('Location: ' . BASE_URL . '/index.php?url=auth/login');
            exit;
        }

        if (!$id) {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/Clanwar.php';

        $db           = (new Database())->getConnection();
        $clanwarModel = new Clanwar($db);
        $clanwar      = $clanwarModel->getById((int)$id);

        if (!$clanwar) {
            $this->addErrorMessage('Clanwar nebyl nalezen.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        if ($clanwar['created_by'] !== $_SESSION['user_id'] && !$this->isAdmin()) {
            $this->addErrorMessage('Nemáte oprávnění upravovat tento clanwar.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        // Dekódování JSON polí hráčů pro předvyplnění formuláře
        $clanwar['team1_players'] = json_decode($clanwar['team1_players'] ?? '[]', true) ?? [];
        $clanwar['team2_players'] = json_decode($clanwar['team2_players'] ?? '[]', true) ?? [];

        require_once '../app/views/clanwars/clanwar_edit.php';
    }

    /**
     * Zpracuje POST data z formuláře pro úpravu clanwaru.
     * Stejná validace skóre a výpočet vítěze jako při vytváření.
     * Obrázek nelze při úpravě měnit.
     * POST /clanwar/update/{id}
     *
     * @param mixed $id ID clanwaru k aktualizaci.
     */
    public function update($id = null): void {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        if (!$this->isMember()) {
            $this->addErrorMessage('Nemáte oprávnění upravovat clanwary.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/Clanwar.php';

        $db           = (new Database())->getConnection();
        $clanwarModel = new Clanwar($db);
        $clanwar      = $clanwarModel->getById((int)$id);

        // Ověření vlastnictví — autor nebo admin smí upravovat
        if (!$clanwar || ($clanwar['created_by'] !== $_SESSION['user_id'] && !$this->isAdmin())) {
            $this->addErrorMessage('Nemáte oprávnění upravovat tento clanwar.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        // Zpracování volitelného uploadu nového obrázku
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (in_array($_FILES['image']['type'], $allowedTypes)) {
                $ext        = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename   = uniqid('cw_') . '.' . $ext;
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }
                $targetPath = 'uploads/' . $filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = $targetPath;
                }
            } else {
                $this->addErrorMessage('Nepodporovaný formát obrázku (pouze JPG, PNG, WEBP, GIF).');
                header('Location: ' . BASE_URL . '/index.php?url=clanwar/edit/' . $id);
                exit;
            }
        }

        // Pokud nebyl nahrán nový obrázek, zachovej stávající z databáze
        if (empty($imagePath) && !empty($clanwar['image_path'])) {
            $imagePath = $clanwar['image_path'];
        }

        $team1Name = 'fSc'; // náš tým je vždy team1
        $team2Name = htmlspecialchars($_POST['team2_name'] ?? '');
        $teamSize  = in_array($_POST['team_size'] ?? '', ['2v2', '3v3']) ? $_POST['team_size'] : '2v2';
        $note      = htmlspecialchars($_POST['note'] ?? '');

        $maxPlayers   = ($teamSize === '3v3') ? 3 : 2;
        $team1Players = [];
        $team2Players = [];
        for ($i = 0; $i < $maxPlayers; $i++) {
            $team1Players[] = htmlspecialchars($_POST['team1_players'][$i] ?? '');
            $team2Players[] = htmlspecialchars($_POST['team2_players'][$i] ?? '');
        }

        // Skóre omezeno na 0–30 (maximální počet kills v jednom kole)
        $round1Team1 = min(30, max(0, (int)($_POST['round1_team1'] ?? 0)));
        $round1Team2 = min(30, max(0, (int)($_POST['round1_team2'] ?? 0)));
        $round2Team1 = min(30, max(0, (int)($_POST['round2_team1'] ?? 0)));
        $round2Team2 = min(30, max(0, (int)($_POST['round2_team2'] ?? 0)));
        $round3Team1 = min(30, max(0, (int)($_POST['round3_team1'] ?? 0)));
        $round3Team2 = min(30, max(0, (int)($_POST['round3_team2'] ?? 0)));

        $winner = Clanwar::calculateWinner(
            $team1Name, $team2Name,
            $round1Team1, $round1Team2,
            $round2Team1, $round2Team2,
            $round3Team1, $round3Team2
        );

        $isUpdated = $clanwarModel->update(
            (int)$id,
            $team1Name, $team2Name, $teamSize,
            $team1Players, $team2Players,
            $round1Team1, $round1Team2,
            $round2Team1, $round2Team2,
            $round3Team1, $round3Team2,
            $winner, $note,
            $imagePath
        );

        if ($isUpdated) {
            $this->addSuccessMessage('Clanwar byl úspěšně upraven.');
            header('Location: ' . BASE_URL . '/index.php?url=clanwar/show/' . $id);
        } else {
            $this->addErrorMessage('Nastala chyba při ukládání změn.');
            header('Location: ' . BASE_URL . '/index.php?url=clanwar/edit/' . $id);
        }
        exit;
    }

    /**
     * Smaže clanwar.
     * Smazat může autor záznamu nebo admin.
     * Poznámka: přiřazený soubor v /uploads se nesmaže automaticky.
     * GET /clanwar/delete/{id}
     *
     * @param mixed $id ID clanwaru ke smazání.
     */
    public function delete($id = null): void {
        if (!$this->isMember()) {
            $this->addErrorMessage('Nemáte oprávnění mazat clanwary.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        if (!$id) {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/Clanwar.php';

        $db           = (new Database())->getConnection();
        $clanwarModel = new Clanwar($db);
        $clanwar      = $clanwarModel->getById((int)$id);

        if (!$clanwar) {
            $this->addErrorMessage('Clanwar nebyl nalezen.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        if ($clanwar['created_by'] !== $_SESSION['user_id'] && !$this->isAdmin()) {
            $this->addErrorMessage('Nemáte oprávnění smazat tento clanwar.');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        if ($clanwarModel->delete((int)$id)) {
            $this->addSuccessMessage('Clanwar byl smazán.');
        } else {
            $this->addErrorMessage('Nepodařilo se smazat clanwar.');
        }

        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }

    /**
     * Zobrazí stránku týmu se seznamem adminů a členů.
     * Admin navíc vidí seznam běžných uživatelů čekajících na přijetí do týmu.
     * Dostupné pro všechny návštěvníky.
     * GET /clanwar/team
     */
    public function team(): void {
        require_once '../app/models/Database.php';
        require_once '../app/models/User.php';

        $db        = (new Database())->getConnection();
        $userModel = new User($db);

        $teamMembers = $userModel->getTeamMembers();
        // Seznam běžných uživatelů k povýšení vidí jen admin
        $pendingUsers = $this->isAdmin() ? $userModel->getUsers() : [];

        require_once '../app/views/clanwars/team.php';
    }

    /**
     * Povýší uživatele z role "user" na "member".
     * Dostupné pouze pro admina.
     * GET /clanwar/promoteUser/{id}
     *
     * @param mixed $id ID uživatele k povýšení.
     */
    public function promoteUser($id = null): void {
        if (!$this->isAdmin()) {
            $this->addErrorMessage('Nemáte oprávnění povyšovat uživatele.');
            header('Location: ' . BASE_URL . '/index.php?url=clanwar/team');
            exit;
        }

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID uživatele.');
            header('Location: ' . BASE_URL . '/index.php?url=clanwar/team');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/User.php';

        $db        = (new Database())->getConnection();
        $userModel = new User($db);

        if ($userModel->promote((int)$id)) {
            $this->addSuccessMessage('Uživatel byl úspěšně povýšen na člena.');
        } else {
            $this->addErrorMessage('Nepodařilo se povýšit uživatele.');
        }

        header('Location: ' . BASE_URL . '/index.php?url=clanwar/team');
        exit;
    }

    /**
     * Smaže uživatelský účet (pouze pro admina, admini nejsou mazatelní).
     * GET /clanwar/deleteUser/{id}
     *
     * @param mixed $id ID uživatele ke smazání.
     */
    public function deleteUser($id = null): void {
        if (!$this->isAdmin()) {
            $this->addErrorMessage('Nemáte oprávnění mazat uživatele.');
            header('Location: ' . BASE_URL . '/index.php?url=clanwar/team');
            exit;
        }

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID uživatele.');
            header('Location: ' . BASE_URL . '/index.php?url=clanwar/team');
            exit;
        }

        if ((int)$id === (int)$_SESSION['user_id']) {
            $this->addErrorMessage('Nemůžete smazat vlastní účet.');
            header('Location: ' . BASE_URL . '/index.php?url=clanwar/team');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/User.php';

        $db        = (new Database())->getConnection();
        $userModel = new User($db);

        if ($userModel->delete((int)$id)) {
            $this->addSuccessMessage('Uživatel byl úspěšně smazán.');
        } else {
            $this->addErrorMessage('Nepodařilo se smazat uživatele (administrátorské účty nelze mazat).');
        }

        header('Location: ' . BASE_URL . '/index.php?url=clanwar/team');
        exit;
    }

    /**
     * Přidá zprávu o úspěchu do fronty flash zpráv v session.
     * @param string $message Text zprávy.
     */
    protected function addSuccessMessage(string $message): void {
        $_SESSION['messages']['success'][] = $message;
    }

    /**
     * Přidá informační zprávu (notice) do fronty flash zpráv v session.
     * @param string $message Text zprávy.
     */
    protected function addNoticeMessage(string $message): void {
        $_SESSION['messages']['notice'][] = $message;
    }

    /**
     * Přidá chybovou zprávu do fronty flash zpráv v session.
     * @param string $message Text zprávy.
     */
    protected function addErrorMessage(string $message): void {
        $_SESSION['messages']['error'][] = $message;
    }
}
