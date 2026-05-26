<?php
/**
 * Kontroler pro správu uživatelského profilu.
 * Zobrazení a editace vlastního profilu přihlášeného uživatele.
 */
class UserController {

    /**
     * Zkontroluje, zda je uživatel přihlášen (má platnou session).
     *
     * @return bool True pokud session obsahuje user_id.
     */
    private function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    /**
     * Zobrazí profil přihlášeného uživatele.
     * Pokud uživatel neexistuje v DB (smazaný účet), odhlásí ho.
     * GET /user/profile
     */
    public function profile(): void {
        if (!$this->isLoggedIn()) {
            header('Location: ' . BASE_URL . '/index.php?url=auth/login');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/User.php';

        $db        = (new Database())->getConnection();
        $userModel = new User($db);
        $user      = $userModel->findById($_SESSION['user_id']);

        // Pokud účet v DB neexistuje, session je neplatná — odhlásíme uživatele
        if (!$user) {
            header('Location: ' . BASE_URL . '/index.php?url=auth/logout');
            exit;
        }

        require_once '../app/views/user/profile.php';
    }

    /**
     * Zobrazí formulář pro úpravu profilu s předvyplněnými aktuálními hodnotami.
     * GET /user/edit
     */
    public function edit(): void {
        if (!$this->isLoggedIn()) {
            header('Location: ' . BASE_URL . '/index.php?url=auth/login');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/User.php';

        $db        = (new Database())->getConnection();
        $userModel = new User($db);
        $user      = $userModel->findById($_SESSION['user_id']);

        require_once '../app/views/user/profile_edit.php';
    }

    /**
     * Zpracuje POST data z formuláře pro úpravu profilu.
     * Po úspěšném uložení aktualizuje zobrazované jméno v session,
     * aby se změna projevila okamžitě v navigaci bez nutnosti odhlásit se.
     * POST /user/update
     */
    public function update(): void {
        if (!$this->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        require_once '../app/models/Database.php';
        require_once '../app/models/User.php';

        $db        = (new Database())->getConnection();
        $userModel = new User($db);

        $email    = htmlspecialchars($_POST['email'] ?? '');
        $nickname = htmlspecialchars($_POST['nickname'] ?? '');

        if (empty($email)) {
            $this->addErrorMessage('E-mail je povinný.');
            header('Location: ' . BASE_URL . '/index.php?url=user/edit');
            exit;
        }

        if ($userModel->updateProfile($_SESSION['user_id'], $email, $nickname)) {
            // Obnovení jména v session, aby se změna projevila okamžitě v hlavičce
            $user                  = $userModel->findById($_SESSION['user_id']);
            $_SESSION['user_name'] = !empty($user['nickname']) ? $user['nickname'] : $user['username'];
            $this->addSuccessMessage('Profil byl úspěšně aktualizován.');
            header('Location: ' . BASE_URL . '/index.php?url=user/profile');
        } else {
            $this->addErrorMessage('Při aktualizaci profilu nastala chyba.');
            header('Location: ' . BASE_URL . '/index.php?url=user/edit');
        }
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
     * Přidá chybovou zprávu do fronty flash zpráv v session.
     * @param string $message Text zprávy.
     */
    protected function addErrorMessage(string $message): void {
        $_SESSION['messages']['error'][] = $message;
    }
}
