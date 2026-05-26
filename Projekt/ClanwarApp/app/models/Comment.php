<?php
/**
 * Model pro práci s komentáři u clanwarů.
 * Zajišťuje přidávání, čtení, úpravu a mazání komentářů.
 */
class Comment {
    private PDO $db;

    /**
     * @param PDO $db Aktivní databázové spojení.
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Vloží nový komentář k danému clanwaru.
     *
     * @param int    $clanwarId ID clanwaru, ke kterému komentář patří.
     * @param int    $userId    ID přihlášeného uživatele, který komentář napsal.
     * @param string $content   Text komentáře (již sanitizovaný kontrolerem).
     * @return bool True při úspěšném vložení.
     */
    public function create(int $clanwarId, int $userId, string $content): bool {
        $sql = "INSERT INTO comments (clanwar_id, user_id, content)
                VALUES (:clanwar_id, :user_id, :content)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':clanwar_id' => $clanwarId,
            ':user_id'    => $userId,
            ':content'    => $content
        ]);
    }

    /**
     * Vrátí všechny komentáře k danému clanwaru seřazené chronologicky.
     * COALESCE vrátí nickname, pokud je nastaven, jinak username autora.
     * LEFT JOIN zachová komentáře i po smazání uživatele (author_name bude null).
     *
     * @param int $clanwarId ID clanwaru.
     * @return array Pole asociativních polí s daty komentářů včetně author_name a author_role.
     */
    public function getByClanwarId(int $clanwarId): array {
        $sql = "SELECT comments.*,
                       COALESCE(users.nickname, users.username) AS author_name,
                       users.role AS author_role
                FROM comments
                LEFT JOIN users ON comments.user_id = users.id
                WHERE comments.clanwar_id = :clanwar_id
                ORDER BY comments.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':clanwar_id' => $clanwarId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Smaže komentář podle ID.
     * Oprávnění ke smazání ověřuje kontroler (autor nebo admin).
     *
     * @param int $id ID komentáře ke smazání.
     * @return bool True při úspěšném smazání.
     */
    public function delete(int $id): bool {
        $sql  = "DELETE FROM comments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Vrátí jeden komentář podle ID.
     * Slouží kontroleru k ověření existence a vlastnictví před úpravou/smazáním.
     *
     * @param int $id ID komentáře.
     * @return array|false Asociativní pole s daty, nebo false pokud komentář neexistuje.
     */
    public function getById(int $id): array|false {
        $sql  = "SELECT * FROM comments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Aktualizuje text existujícího komentáře.
     *
     * @param int    $id      ID komentáře k aktualizaci.
     * @param string $content Nový text komentáře (již sanitizovaný kontrolerem).
     * @return bool True při úspěšné aktualizaci.
     */
    public function update(int $id, string $content): bool {
        $sql  = "UPDATE comments SET content = :content WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'      => $id,
            ':content' => $content
        ]);
    }
}
