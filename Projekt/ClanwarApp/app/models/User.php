<?php
/**
 * Model pro práci s uživatelskými účty.
 * Zajišťuje registraci, přihlášení, vyhledávání a správu rolí.
 */
class User {
    private PDO $db;

    /**
     * @param PDO $db Aktivní databázové spojení.
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Zaregistruje nového uživatele s rolí "user".
     * Pokud e-mail již existuje, registraci odmítne a vrátí false.
     *
     * @param string      $username Přihlašovací jméno.
     * @param string      $email    E-mailová adresa (musí být unikátní).
     * @param string      $password Heslo v čitelné podobě — bude zahashováno.
     * @param string|null $nickname Volitelná herní přezdívka.
     * @return bool True při úspěšném vložení, false pokud e-mail již existuje.
     */
    public function register(
        string $username,
        string $email,
        string $password,
        ?string $nickname = null
    ): bool {
        if ($this->findByEmail($email)) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password, nickname, role)
                VALUES (:username, :email, :password, :nickname, 'user')";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':username' => $username,
            ':email'    => $email,
            ':password' => $hashedPassword,
            ':nickname' => $nickname
        ]);
    }

    /**
     * Vyhledá uživatele podle e-mailové adresy.
     * Vrátí i sloupec "password" (hash) pro ověření při přihlášení.
     *
     * @param string $email E-mailová adresa.
     * @return array|false Asociativní pole s daty uživatele, nebo false pokud nenalezen.
     */
    public function findByEmail(string $email): array|false {
        $sql  = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Vyhledá uživatele podle jeho ID.
     * Záměrně nevybírá sloupec "password" — slouží jen pro zobrazení profilu.
     *
     * @param int $id ID uživatele.
     * @return array|false Asociativní pole bez hesla, nebo false pokud nenalezen.
     */
    public function findById(int $id): array|false {
        $sql  = "SELECT id, username, email, nickname, role, created_at FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Vrátí všechny členy týmu (role admin a member) seřazené pro zobrazení.
     * FIELD() zajišťuje pořadí admin → member; v rámci role řadí dle zobrazovaného jména.
     *
     * @return array Pole asociativních polí s daty členů.
     */
    public function getTeamMembers(): array {
        $sql = "SELECT id, username, nickname, role, created_at
                FROM users
                WHERE role IN ('admin', 'member')
                ORDER BY FIELD(role, 'admin', 'member'), COALESCE(nickname, username) ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vrátí všechny registrované uživatele s rolí "user" (ještě ne členové týmu).
     * Slouží adminu pro rozhodnutí o přijetí do týmu.
     *
     * @return array Pole asociativních polí s daty uživatelů.
     */
    public function getUsers(): array {
        $sql = "SELECT id, username, nickname, role, created_at
                FROM users
                WHERE role = 'user'
                ORDER BY COALESCE(nickname, username) ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Povýší uživatele z role "user" na "member".
     * WHERE role = 'user' brání opakovanému povýšení nebo přepsání role admina.
     *
     * @param int $id ID uživatele k povýšení.
     * @return bool True pokud byl řádek aktualizován, false pokud uživatel nebyl "user".
     */
    public function promote(int $id): bool {
        $sql  = "UPDATE users SET role = 'member' WHERE id = :id AND role = 'user'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Smaže uživatelský účet podle ID.
     * Administrátorské účty nelze touto metodou smazat.
     *
     * @param int $id ID uživatele ke smazání.
     * @return bool True při úspěšném smazání.
     */
    public function delete(int $id): bool {
        $sql  = "DELETE FROM users WHERE id = :id AND role != 'admin'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Aktualizuje e-mail a přezdívku přihlášeného uživatele.
     * Username nelze změnit — slouží jako trvalý identifikátor.
     *
     * @param int         $id       ID uživatele.
     * @param string      $email    Nová e-mailová adresa.
     * @param string|null $nickname Nová přezdívka, nebo null pro smazání.
     * @return bool True při úspěšné aktualizaci.
     */
    public function updateProfile(int $id, string $email, ?string $nickname): bool {
        $sql  = "UPDATE users SET email = :email, nickname = :nickname WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'       => $id,
            ':email'    => $email,
            ':nickname' => $nickname
        ]);
    }
}
