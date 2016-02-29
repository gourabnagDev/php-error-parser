<?php
/**
 * Created by PhpStorm.
 * User: GourabNag
 * Date: 2/29/2016
 * Time: 8:16 AM
 */

/**
 * Class error_parser
 * Members:
 * @param $log_file_path - private
 * @param $current_line - private
 * @param $recent - private
 * @param $errors_array - private
 *
 * Methods:
 * @method __construct() - public
 * @method _parse() - private
 * @method returnJson() - public
 * @method returnXml() - public
 *
 * Function:
 * TO parse the PHP ERROR LOG and provide a readable and a programmable array, consisting of the errors!
 */
final class error_parser
{
    private $log_file_path;
    private $current_line;
    private $recent;
    private $errors_array = array();

    /**
     * error_parser constructor.
     * Takes in the path of the error log file of PHP ERROR LOG FILE.
     * And another param for checking to get the direction to traverse the file.
     * @param string $log_file_path
     * @param bool $recent
     */
    public function __construct($log_file_path, $recent = true)
    {
        $this->log_file_path = $log_file_path;
        $this->recent = $recent;
        $this->_parse();
        return true;
    }

    /**
     * Parses the PHP ERROR LOG, and pushes an array with the following structure:
     * array(
     * "date" => {DATE},
     * "severity" => {SEVERITY},
     * "message" => {message},
     * "stack_trace" => array(each({STACK_TRACE})) || false;
     * );
     * to the main array.
     * !!!! IMPORTANT !!!!
     * STACK TRACE IS NOT SUPPORTED AT THIS MOMENT
     * TODO: IMPLEMENT STACK TRACE
     * MILESTONE: NEXT_MAJOR RELEASE
     */
    private function _parse() {
        $contents = file_get_contents($this->log_file_path);
        if(!$contents){
            throw new Exception("Log file does not exist.", 2);
        }
        $lines = explode("\n", $contents);
        if($this->recent) {
            $lines = array_reverse($lines);
        }
        for($this->current_line = 0; $this->current_line < count($lines); $this->current_line++) {
            parse_loop:
            $current_line = trim($lines[$this->current_line]);
            if(strlen($current_line) == 0) {
                //If the line is empty throw it to the dustbin.
                // SORRY, FOR THE GOTO.
                // GOD PLEASE FORGIVE ME!
                $this->current_line = $this->current_line + 1;
                goto parse_loop;
            }
            if($current_line[0] != "[") {
                // NOT SUPPORTING STACK TRACES AT THE MOMENT
                $this->current_line = $this->current_line + 1;
                goto parse_loop;
            }
            $dateArr = array();
            preg_match('~^\[(.*?)\]~', $current_line, $dateArr);
            $current_line = str_replace($dateArr[0], "", $current_line);
            $current_line = trim($current_line);
            $date = array(
                "date" => explode(" ", $dateArr[1])[0],
                "time" => explode(" ", $dateArr[1])[1]
            );
            $severity = "";
            if(strpos($current_line, "PHP Warning") !== false) {
                $current_line = str_replace("PHP Warning:", "", $current_line);
                $current_line = trim($current_line);
                $severity = "WARNING";
            } elseif(strpos($current_line, "PHP Notice") !== false) {
                $current_line = str_replace("PHP Notice:", "", $current_line);
                $current_line = trim($current_line);
                $severity = "NOTICE";
            } elseif(strpos($current_line, "PHP Fatal error") !== false) {
                $current_line = str_replace("PHP Fatal error:", "", $current_line);
                $current_line = trim($current_line);
                $severity = "FATAL";
            } elseif(strpos($current_line, "PHP Parse error") !== false) {
                $current_line = str_replace("PHP Parse error:", "", $current_line);
                $current_line = trim($current_line);
                $severity = "SYNTAX_ERROR";
            } else {
                $severity = "UNIDENTIFIED_ERROR";
            }
            $message = $current_line;
            /* Final Array *//* Add nodes while creating them */
            $finalArray = array(
                "date" => $date,
                "severity" => $severity,
                "message" => $message
            );
            array_push($this->errors_array, $finalArray);
        }
    }

    /**
     * Function for returning the the error things in JSON format.
     * @return string <JSON STRING>
     */
    public function returnJson() {
        return json_encode($this->errors_array);
    }
    
    /**
    * Function to return the error things in XML format.
    * @return string <XML STRING>
    */
    public function returnXml() {
        var_dump($this->errors_array);
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><errors></errors>");
        $this->_array_to_xml($xml, $this->errors_array);
        // By this time we would get a xml "STRING"
        return $xml->asXML();
    }

    /* Factory Function */
    /**
     * Converts an array to an xml-document
     * @param $data
     * @param $xml_data <SimpleXMLObject>
     */
    private function _array_to_xml(&$xml_data, $data) {
        foreach( $data as $key => $value ) {
            if( is_array($value) ) {
                if(is_numeric($key)){
                    $key = 'item' . $key;
                }
                $subnode = $xml_data->addChild($key);
                $this->_array_to_xml($subnode, $value);
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }
}
