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
        $sql = "SELECT `Regler`.`ID`, beschriftung, min_value, max_value, Round(AVG(`Bewertung`.`value`),1) as average FROM `Regler`, `Bewertung` Where `Regler`.`ID` = `Bewertung`.`regler_id` GROUP BY `Regler`.`ID`, beschriftung, min_value, max_value";
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
        $this->generatePageHeader("Feedbackseite");

        echo <<<HTML
        <body>
            <header>
                <h1>Feedback zur vorlesung</h1>
            </header>
            <main>
                <h2 id="meldung"></h2>
                <form id="bewertungForm" method="post" action="Exam21.php">
HTML;
        $this->generateRegler($data);
        
        echo <<<HTML
                    <input id="Matrikelnummer" name="Matrikelnummer" type="text" placeholder="Matrikelnummer" require>
                    <button type="submit">Abschicken</button>
                </form>
            </main>
        </body>
HTML;      
    }


    private function generateRegler(array $rieglers)
    {
    
        $output = "";
        foreach($rieglers as $riegler){
            $id = $riegler["ID"];
            $beschriftung = $riegler["beschriftung"];
            $min = $riegler["min_value"];
            $max = $riegler["max_value"];
            $avg = $riegler["average"];

            echo <<<HTML
            
                <label for="$id">$beschriftung(Durschnitt $avg)</label>
                <input form="bewertungForm" type="range" min="$min" max="$max" class="slider" name="$id" onInput="updateValue(this.value, this.name)" onchange="updateValue(this.value, this.name)">
                <output id="$id"></output><br>        
HTML;
        }
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();
        if(isset($_POST["Matrikelnummer"])){
            $matrikelnummer = $this->_database->real_escape_string($_POST["Matrikelnummer"]); //oder is_numeric prüfen
            $regler_1 = $_POST['1']; 
            $regler_2 = $_POST['2']; 
            $regler_3 = $_POST['3']; 

            $reglers = array("1" => $_POST['1'],"2" => $_POST['2'],"3" => $_POST['3']);

            //var_dump($_POST);

    
            $sql = "SELECT * from Student WHERE matrikelnummer = $matrikelnummer";
                
            $student = $this->_database->query($sql);

            if (!$student) {
                throw new Exception("Fehler in Abfrage: " . $this->_database->error);
            }

            if($student->num_rows > 0){
                foreach($reglers as $key => $value){
                    //var_dump($key);
                    //var_dump($value);

                    $sql = "UPDATE Bewertung
                        SET value = $value
                        WHERE regler_id = $key AND matrikelnummer = $matrikelnummer";
                    
                    $recordset = $this->_database->query($sql);

                    if (!$recordset) {
                        throw new Exception("Fehler in Abfrage: " . $this->_database->error);
                    }
                }
            }
            else{
                foreach($reglers as $key => $value){
                    //var_dump($key);
                    //var_dump($value);

                    $sql = "INSERT INTO Bewertung
                        SET value = $value, matrikelnummer = $matrikelnummer, regler_id = $key";

                    $recordset = $this->_database->query($sql);

                    if (!$recordset) {
                        throw new Exception("Fehler in Abfrage: " . $this->_database->error);
                    }
                }
            }
            
            header('Location: Exam21.php');
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

