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
        $sql = "SELECT * from news";
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
        $this->generatePageHeader("News");
        $news = $this->generateNews($data);
        echo <<<HTML
        <body>
            <header>
                <div class="logoTitel">
                    <img alt="" src="Logo.png" >
                    <h1>Meine Zeitung</h1>
                </div>
                <div class="navigationBar">
                    <nav id="navigation" role="navigation">
                        <a href="Exam22.html">Home</a>
                        <a href="Exam22.html">Mediathek</a>
                        <a href="Exam22.html">Politik</a>
                        <a href="Exam22.html">Sport</a>
                    </nav>
                </div>
            </header>
            <main>
                <h2>News-Ticker</h2>
                <div class="news">
                    $news
                </div>
                <div class="formContainer">
                    <h2>Ihre News</h2>
                    <form id="newsForm" method="post" action="Exam21.php">
                        <input id="titel" name="titel" type="text" placeholder="Titel Ihrer News">
                        <textarea id="text" rows="5" cols="60" name="text" placeholder="Ihre News"></textarea>
                        <button type="submit">Absenden</button>
                    </form>
                </div>
            </main>
        </body>

HTML;      
    }

    protected function generateNews(array $news){
        $html = "";
        foreach($news as $element){
            $id = htmlspecialchars($element['id']);
            $timestamp = htmlspecialchars($element['timestamp']);
            $title = htmlspecialchars($element['title']);
            $text = htmlspecialchars($element['text']);

            $html .= '<div class="newsContainer"><h3>'
                        .$title .'</h3><p>'
                        .$timestamp .'</p><p>'
                        .$text .'</p></div>';
        }
        return $html;
    }


    protected function processReceivedData():void
    {
        parent::processReceivedData();
        if(isset($_POST["titel"]) && isset($_POST["text"])){
            
            $titel = $this->_database->real_escape_string($_POST["titel"]);
            $text = $this->_database->real_escape_string($_POST["text"]);
            $timeStamp = new DateTime();
        
            $date = $timeStamp->format('Y-m-d H:i:s');
    
            $sql = "INSERT INTO news SET title = '$titel', text = '$text', timestamp = '$date'";
                
            $student = $this->_database->query($sql);

            if (!$student) {
                throw new Exception("Fehler in Abfrage: " . $this->_database->error);
            }
            
            
            header('Location: Exam21.php');
            //header();
            die();
        }
    }

    private function getFormattedDateTime(string $date):string
    {
        $date = new DateTime($date);
            return $date->format("d.m.Y H:i");
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

