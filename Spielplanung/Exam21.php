<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';

class Exam21 extends Page
{

    protected function __construct()
    {
        parent::__construct();
    }


    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData():array
    {
        $data = array();
        $sql = "SELECT id, datetime, opposingTeam, status FROM games order by datetime asc";
        $recordset = $this->_database->query($sql);
        if (!$recordset) {
            throw new Exception("Fehler in Abfrage: " . $this->_database->error);
        }
        while ($record = $recordset->fetch_assoc()) {
            $data[] = $record;
        }
        $recordset->free();
        return $data;
    }

    protected function generateView():void
    {
        $data = $this->getViewData(); // NOSONAR ignore unused $data


        // just to be sure - escape all html-values...
		for ($i = 0; $i < count($data); $i++) {			
			foreach($data[$i] as $key => $value){
				$data[$i][$key] = htmlspecialchars($value);
			}
		}

        $this->generatePageHeader("Spielplanung");

        $nextGame = $this->generateNextGameSection($data);
        echo <<<EOT
        <body onload="pollData()">
            <header>
                <div class="logoWrapper">
                    <img alt="" src="Logo.png" />  
                </div>
                <div class="titleWrapper">
                    <h1>Spielplanung</h1>
                </div>
            </header>
            <main>
            $nextGame
EOT;
        $games = $this->generateGameTableRows($data);
        echo <<<EOT
        <section>
        <h2>Spiele</h2>
        <table>
            <tr><th>Datum</th><th>Team</th><th>Status</th></tr>
            $games
        </table>
        </section>
        </main>
        </body>
EOT;
            
        

    }
    private function getFormattedDateTime(string $date):string
    {
        $date = new DateTime($date);
            return $date->format("d.m.Y H:i");
    }

    protected function generateNextGameSection(array $dataRows):string
    {
        foreach($dataRows as $row){
            if($row["status"] == 1 || $row["status"] == 2){
                $gameId = $row["id"];
                $nextGameInfo  =$this->getFormattedDateTime($row["datetime"])  . " Uhr gegen " . $row["opposingTeam"];
                return "<section><h2>$nextGameInfo</h2>"        
                . '<h3>Zusagen Spieler:innen <span id="players">?</span></h3>'
                . '<form accept-charset="UTF-8" method="post"><input type="hidden" id="gameId" value="'
                . $gameId . '"><input type="submit" name="finishGame" value="Planung abschließen" /></form></section>';
            }
        }

        return "<h2>kein aktuelles Spiel</h2>";
    }

    protected function generateGameTableRows(array $dataRows){
        $htmlRows = "";
        foreach($dataRows as $row){
            $htmlRows .= "<tr><td>" . $this->getFormattedDateTime($row["datetime"]). "</td><td>" . $row["opposingTeam"] . "</td><td>" . $this->getStatusTextToInt($row["status"]) . "</td></tr>\n";
        }
        return $htmlRows;
    }

    private function getStatusTextToInt(string $status):string
    {
        switch ($status) {
            case "0":
                $ret = "zukünftig";
				break;
            case "1":
                $ret = "in Planung";
				break;
            case "2":
                $ret = "Planung abgeschlossen";
				break;
            case "3":
                $ret = "vorbei";
				break;
            default:
                $ret = "unbekannter Status";				
        }
		return $ret;
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();
        if (isset($_POST["finishGame"]) && isset($_POST["gameId"])){
            $gameId = $this->_database->real_escape_string($_POST["gameId"]); //oder is_numeric prüfen
            $sql = "UPDATE games set status = 2 WHERE id=$gameId AND status=1;";
            if (!$this->_database->query($sql)) {
                throw new Exception("Update failed: " . $this->_database->error);
            }
            header('Location:Exam21.php');
            //header();
            die();
        }
    }

    public static function main():void
    {
        try {
            $page = new Exam21();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}


Exam21::main();

