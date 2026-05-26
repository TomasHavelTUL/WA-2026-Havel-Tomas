<?php
/**
 * Vytváří a vrací PDO spojení s MySQL databází.
 * Chyby připojení se vypisují přímo na výstup — vhodné pro vývoj.
 */
class Database {
    private $host     = "localhost";
    private $db_name  = "wa_2026_clanwars";
    private $username = "root";
    private $password = "";
    public  $conn;

    /**
     * Otevře nové PDO spojení s databází a nastaví error mode na výjimky.
     * Při selhání vypíše chybovou zprávu a vrátí null.
     *
     * @return PDO|null Aktivní spojení, nebo null při chybě připojení.
     */
    public function getConnection(): ?PDO {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            // PDO::ERRMODE_EXCEPTION zajistí, že chyby SQL vyvolají výjimku
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Chyba připojení: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
