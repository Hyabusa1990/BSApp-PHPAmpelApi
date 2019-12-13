<?php
header('Content-Type: application/json');

$msg = "";
$status = "OK";

$pdo = new SQLiteConnection();
if ($pdo == null)
{
    $msg = 'Whoops, could not connect to the SQLite database!';
}


if (isset($_POST["act"])) {
    $buffer = [];
    if ($_POST["act"] == "set") {
       foreach($_POST as $key => $value)
       {
            if($key != "act")
            {
                try
                {
                    $pdo->setValuefromKey($key, $value);
                }
                catch(Exception $e)
                {
                    $msg = $e;
                    $status = "ERR";
                }
            }
       } 
    } 
    else if ($_POST["act"] == "get") {
        $querys = str_split($_POST["q"], 2);
        foreach ($querys as $query) {
            try
            {
                $buffer = $buffer + array($query => $pdo->getValuefromKey($query));
            }
            catch (Exception $e)
            {
                $msg = $e;
                $status = "ERR";
            }
        }
    }
    $buffer = $buffer + array("MSG" => $msg, "STATUS" => $status);
    echo json_encode($buffer);
} 
else {
    echo "Ampel";
}




class SQLiteConnection {
    /**
     * PDO instance
     * @var type 
     */
    private $pdo;

    /**
     * connect to the SQLite database
     */
    public function __construct() {
        $this->pdo = new \PDO("sqlite:database.sqlite");
    }

     /**
     * get value from key
     */
    public function getValuefromKey($key) 
    {
        $stmt = $this->pdo->prepare("SELECT value FROM params WHERE key = :KEY");
        $stmt->execute([':KEY' => $key]);
        $value = "";
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $value = $row['value'];
        }
 
        return $value;
    }

    /**
     * set value from key
     */
    public function setValuefromKey($key, $value) 
    {
        $sql = "REPLACE INTO params (key, value) VALUES(:KEY, :VALUE);";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':KEY'=> $key, ':VALUE' => $value]);
    }

}
?>