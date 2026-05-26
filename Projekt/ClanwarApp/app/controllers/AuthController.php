<?php
/**
 * Kontroler pro autentizaci uživatelů.
 * Zpracovává registraci, přihlášení a odhlášení.
 */
class AuthController {

    /**
     * Zobrazí formulář pro registraci nového účtu.
     * GET /auth/register
     */
    public function register(): void {
        require_once '../app/views/auth/register.php';
    }

    /**
     * Zpracuje POST data z registračního formuláře.
     * Ověří povinná pole, shodu hesel a unikátnost e-mailu.
     * Po úspěšné registraci přesměruje na přihlášení.
     * POST /auth/storeUser
     */
    public function storeUser(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username        = htmlspecialchars($_POST['username'] ?? '');
            $email           = htmlspecialchars($_POST['email'] ?? '');
            $nickname        = htmlspecialchars($_POST['nickname'] ?? '');
            $password        = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            if (empty($username) || empty($email) || empty($password)) {
                $this->addErrorMessage('Vyplňte prosím všechna povinná pole.');
                header('Location: ' . BASE_URL . '/index.php?url=auth/register');
                exit;
            }

            if ($password !== $passwordConfirm) {
                $this->addErrorMessage('Zadaná hesla se neshodují.');
                header('Location: ' . BASE_URL . '/index.php?url=auth/register');
                exit;
            }

            if (strlen($password) < 8 || !preg_match('/\d/', $password)) {
                $this->addErrorMessage('Heslo musí mít alespoň 8 znaků a obsahovat alespoň 1 číslo.');
                header('Location: ' . BASE_URL . '/index.php?url=auth/register');
                exit;
            }

            require_once '../app/models/Database.php';
            require_once '../app/models/User.php';

            $db        = (new Database())->getConnection();
            $userModel = new User($db);

            if ($userModel->register($username, $email, $password, $nickname)) {
                $this->addSuccessMessage('Registrace byla úspěšná. Nyní se můžete přihlásit.');
                header('Location: ' . BASE_URL . '/index.php?url=auth/login');
                exit;
            } else {
                $this->addErrorMessage('Uživatel s tímto e-mailem již existuje.');
                header('Location: ' . BASE_URL . '/index.php?url=auth/register');
                exit;
            }
        }
    }

    /**
     * Zobrazí formulář pro přihlášení.
     * GET /auth/login
     */
    public function login(): void {
        require_once '../app/views/auth/login.php';
    }

    /**
     * Zpracuje POST data z přihlašovacího formuláře.
     * Ověří e-mail a heslo pomocí password_verify().
     * Po úspěchu uloží do session pouze id, roli a zobrazované jméno — nikdy heslo.
     * POST /auth/authenticate
     */
    public function authenticate(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = htmlspecialchars($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            require_once '../app/models/Database.php';
            require_once '../app/models/User.php';

            $db        = (new Database())->getConnection();
            $userModel = new User($db);
            $user      = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                // Do session ukládáme jen id, roli a zobrazované jméno — nikdy heslo
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = !empty($user['nickname']) ? $user['nickname'] : $user['username'];

                $this->addSuccessMessage('Vítejte zpět, ' . $_SESSION['user_name'] . '!');
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            } else {
                $this->addErrorMessage('Nesprávný e-mail nebo heslo.');
                header('Location: ' . BASE_URL . '/index.php?url=auth/login');
                exit;
            }
        }
    }

    /**
     * Odhlásí uživatele smazáním session klíčů a přesměruje na úvodní stránku.
     * GET /auth/logout
     */
    public function logout(): void {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role']);
        $this->addSuccessMessage('Byli jste úspěšně odhlášeni.');
        header('Location: ' . BASE_URL . '/index.php');
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
