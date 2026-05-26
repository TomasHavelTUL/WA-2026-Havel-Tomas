<?php
/**
 * Model pro práci se záznamy clanwarů.
 * Zajišťuje CRUD operace a výpočet vítěze.
 */
class Clanwar {
    private PDO $db;

    /**
     * @param PDO $db Aktivní databázové spojení.
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Vloží nový clanwar do databáze.
     * Pole hráčů jsou serializována jako JSON, protože DB sloupce jsou VARCHAR.
     *
     * @param string      $team1Name    Název týmu 1 (vždy "fSc").
     * @param string      $team2Name    Název soupeře.
     * @param string      $teamSize     Formát zápasu: "2v2" nebo "3v3".
     * @param array       $team1Players Pole jmen hráčů týmu 1.
     * @param array       $team2Players Pole jmen hráčů týmu 2.
     * @param int         $round1Team1  Skóre týmu 1 v 1. kole (0–30).
     * @param int         $round1Team2  Skóre týmu 2 v 1. kole (0–30).
     * @param int         $round2Team1  Skóre týmu 1 ve 2. kole (0–30).
     * @param int         $round2Team2  Skóre týmu 2 ve 2. kole (0–30).
     * @param int         $round3Team1  Skóre týmu 1 ve 3. kole (0–30).
     * @param int         $round3Team2  Skóre týmu 2 ve 3. kole (0–30).
     * @param string      $winner       Název vítězného týmu nebo "Remíza".
     * @param string      $note         Volitelná textová poznámka.
     * @param int         $userId       ID uživatele, který záznam vytvořil.
     * @param string|null $imagePath    Relativní cesta k nahranému screenshotu, nebo null.
     * @return bool True při úspěšném vložení.
     */
    public function create(
        string $team1Name,
        string $team2Name,
        string $teamSize,
        array  $team1Players,
        array  $team2Players,
        int    $round1Team1, int $round1Team2,
        int    $round2Team1, int $round2Team2,
        int    $round3Team1, int $round3Team2,
        string $winner,
        string $note,
        int    $userId,
        ?string $imagePath = null
    ): bool {
        $sql = "INSERT INTO clanwars (
                    team1_name, team2_name, team_size,
                    team1_players, team2_players,
                    round1_team1, round1_team2,
                    round2_team1, round2_team2,
                    round3_team1, round3_team2,
                    winner, note, created_by, image_path
                ) VALUES (
                    :team1_name, :team2_name, :team_size,
                    :team1_players, :team2_players,
                    :round1_team1, :round1_team2,
                    :round2_team1, :round2_team2,
                    :round3_team1, :round3_team2,
                    :winner, :note, :created_by, :image_path
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':team1_name'    => $team1Name,
            ':team2_name'    => $team2Name,
            ':team_size'     => $teamSize,
            ':team1_players' => json_encode($team1Players), // hráči uloženi jako JSON pole v DB
            ':team2_players' => json_encode($team2Players),
            ':round1_team1'  => $round1Team1,
            ':round1_team2'  => $round1Team2,
            ':round2_team1'  => $round2Team1,
            ':round2_team2'  => $round2Team2,
            ':round3_team1'  => $round3Team1,
            ':round3_team2'  => $round3Team2,
            ':winner'        => $winner,
            ':note'          => $note,
            ':created_by'    => $userId,
            ':image_path'    => $imagePath
        ]);
    }

    /**
     * Vrátí všechny clanwary seřazené od nejnovějšího, včetně jména autora.
     * LEFT JOIN zajistí, že záznamy s odstraněným uživatelem zůstanou viditelné.
     *
     * @return array Pole asociativních polí s daty clanwarů.
     */
    public function getAll(): array {
        $sql = "SELECT clanwars.*, users.nickname, users.username
                FROM clanwars
                LEFT JOIN users ON clanwars.created_by = users.id
                ORDER BY clanwars.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vrátí jeden clanwar podle ID, včetně jména autora.
     *
     * @param int $id ID clanwaru.
     * @return array|false Asociativní pole s daty, nebo false pokud záznam neexistuje.
     */
    public function getById(int $id): array|false {
        $sql = "SELECT clanwars.*, users.nickname, users.username
                FROM clanwars
                LEFT JOIN users ON clanwars.created_by = users.id
                WHERE clanwars.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Aktualizuje existující clanwar. Obrázek se při úpravě nemění
     * (nahrazení screenshotu není podporováno).
     *
     * @param int    $id           ID záznamu k aktualizaci.
     * @param string $team1Name    Název týmu 1.
     * @param string $team2Name    Název soupeře.
     * @param string $teamSize     Formát zápasu: "2v2" nebo "3v3".
     * @param array  $team1Players Pole jmen hráčů týmu 1.
     * @param array  $team2Players Pole jmen hráčů týmu 2.
     * @param int    $round1Team1  Skóre týmu 1 v 1. kole.
     * @param int    $round1Team2  Skóre týmu 2 v 1. kole.
     * @param int    $round2Team1  Skóre týmu 1 ve 2. kole.
     * @param int    $round2Team2  Skóre týmu 2 ve 2. kole.
     * @param int    $round3Team1  Skóre týmu 1 ve 3. kole.
     * @param int    $round3Team2  Skóre týmu 2 ve 3. kole.
     * @param string      $winner       Název vítězného týmu nebo "Remíza".
     * @param string      $note         Volitelná textová poznámka.
     * @param string|null $imagePath    Relativní cesta k obrázku, nebo null.
     * @return bool True při úspěšné aktualizaci.
     */
    public function update(
        int    $id,
        string $team1Name,
        string $team2Name,
        string $teamSize,
        array  $team1Players,
        array  $team2Players,
        int    $round1Team1, int $round1Team2,
        int    $round2Team1, int $round2Team2,
        int    $round3Team1, int $round3Team2,
        string $winner,
        string $note,
        ?string $imagePath = null
    ): bool {
        $sql = "UPDATE clanwars SET
                    team1_name    = :team1_name,
                    team2_name    = :team2_name,
                    team_size     = :team_size,
                    team1_players = :team1_players,
                    team2_players = :team2_players,
                    round1_team1  = :round1_team1,
                    round1_team2  = :round1_team2,
                    round2_team1  = :round2_team1,
                    round2_team2  = :round2_team2,
                    round3_team1  = :round3_team1,
                    round3_team2  = :round3_team2,
                    winner        = :winner,
                    note          = :note,
                    image_path    = :image_path
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id'            => $id,
            ':team1_name'    => $team1Name,
            ':team2_name'    => $team2Name,
            ':team_size'     => $teamSize,
            ':team1_players' => json_encode($team1Players),
            ':team2_players' => json_encode($team2Players),
            ':round1_team1'  => $round1Team1,
            ':round1_team2'  => $round1Team2,
            ':round2_team1'  => $round2Team1,
            ':round2_team2'  => $round2Team2,
            ':round3_team1'  => $round3Team1,
            ':round3_team2'  => $round3Team2,
            ':winner'        => $winner,
            ':note'          => $note,
            ':image_path'    => $imagePath
        ]);
    }

    /**
     * Smaže clanwar podle ID.
     * Poznámka: nahrané obrázky v /uploads se nesmažou — je nutné je mazat zvlášť.
     *
     * @param int $id ID záznamu ke smazání.
     * @return bool True při úspěšném smazání.
     */
    public function delete(int $id): bool {
        $sql  = "DELETE FROM clanwars WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Určí vítěze zápasu na základě součtu kills ze všech tří kol.
     * Rozhoduje celkový součet, nikoli počet vyhraných kol.
     *
     * @param string $team1Name Název týmu 1.
     * @param string $team2Name Název týmu 2.
     * @param int    $r1t1      Skóre týmu 1 v 1. kole.
     * @param int    $r1t2      Skóre týmu 2 v 1. kole.
     * @param int    $r2t1      Skóre týmu 1 ve 2. kole.
     * @param int    $r2t2      Skóre týmu 2 ve 2. kole.
     * @param int    $r3t1      Skóre týmu 1 ve 3. kole.
     * @param int    $r3t2      Skóre týmu 2 ve 3. kole.
     * @return string Název vítězného týmu, nebo "Remíza" při shodném součtu.
     */
    public static function calculateWinner(
        string $team1Name, string $team2Name,
        int $r1t1, int $r1t2,
        int $r2t1, int $r2t2,
        int $r3t1, int $r3t2
    ): string {
        $total1 = $r1t1 + $r2t1 + $r3t1;
        $total2 = $r1t2 + $r2t2 + $r3t2;

        if ($total1 > $total2) return $team1Name;
        if ($total2 > $total1) return $team2Name;
        return 'Remíza';
    }
}
