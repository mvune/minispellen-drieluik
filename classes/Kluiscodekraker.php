<?php
class Mastermind {
    
    const NUMBER_OF_DIGITS = 4;
    const MAX_RANGE = 6;
    const MAX_GUESSES = 10;
    private $secret;
    private $guessed_list;
    private $times_guessed;
    private $cracked;
    private $failed;
    private $finished;
    
    /**
     * Stelt de properties in.
     */
    function __construct() {
        $this->secret = $this->createSecret();
        $this->guessed_list = array();
        $this->times_guessed = 0;
        $this->cracked = false;
        $this->failed = false;
        $this->finished = false;
    }
    
    /**
     * Speelt het spel.
     *
     * @param $post De array verzonden via de HTTP POST method.
     */
    public function playGame($post) {
        
        if(isset($post['reset'])) {$this->__construct();}
        
        if(!$this->finished) {
            
            if(isset($post['enter'])) {$this->assessGuess($post);}
            
            if($this->times_guessed == self::MAX_GUESSES) {
                
                $this->failed = true;
            }
            
            $this->finished = $this->cracked || $this->failed;
        }
    }
    
    /**
     * Laadt en geeft het spel uit de $_SESSION of maakt een nieuwe 
     * als deze nog niet bestaat.
     *
     * @param $post De array verzonden via de HTTP POST method.
     * @return Object van het lopende spel.
     */
    public static function loadGame() {
        
        if(!isset($_SESSION)) {session_start();}
        if(!isset($_SESSION['mastermind'])) {
            $_SESSION['mastermind'] = new Mastermind();
        }
        return $_SESSION['mastermind'];
    }
    
    /**
     * Kijkt in hoeverre de input matcht met $secret. Wijst hiervoor 
     * waarden toe aan 3 parameters:
     *
     * $green:  Aantal juist getallen op de juiste plek in $secret.
     * $yellow: Aantal getallen dat wel voorkomt, maar niet op de juiste 
     *          plek staat in $secret.
     * $red:    Aantal overige getallen.
     *
     * Gooit vervolgens de input als string, samen met de waarden van 
     * $green, $yellow en $red, in een array in de $guessed_list.
     *
     * @param $input Array met daarin de input. Input heeft de keys 
     * 0 t/m NUMBER_OF_DIGITS.
     */
    private function assessGuess($input) {
        
        if($this->inputIsValid($input)) {
            
            $secret = $this->secret;
            $input_as_string = "";
            $input_2 = array();
            $green = 0;
            $yellow = 0;
            $red = 0;
            
            for($i = 0; $i < self::NUMBER_OF_DIGITS; $i++) {
                
                $input_as_string .= $input[$i];
                
                if($input[$i] == $secret[$i]) {
                    
                    $secret[$i] = 0;
                    $green++;
                    
                    if($green == self::NUMBER_OF_DIGITS) {
                        
                        $this->cracked = true;
                    }
                    
                } else {
                    $input_2[] = $input[$i];
                }
            }
            
            for($i = 0; $i < count($input_2); $i++) {
                
                if(in_array($input_2[$i], $secret)) {
                    
                    $secret[array_search($input_2[$i], $secret)] = 0;
                    $yellow++;
                    
                } else {
                    $red++;
                }
            }
            
            $this->times_guessed++;
            $this->guessed_list[] = array(
                'code'      => $input_as_string,
                'green'     => $green,
                'yellow'    => $yellow,
                'red'       => $red
            );
        }
    }
    
    /**
     * Bepaalt of de input geldig is.
     *
     * @param $input Array met daarin de input. Input heeft de keys 
     * 0 t/m NUMBER_OF_DIGITS.
     * @return True als de input geldig is, anders false.
     */
    private function inputIsValid($input) {
                
        for($i = 0; $i < self::NUMBER_OF_DIGITS; $i++) {
            
            if(isset($input[$i]) && ctype_digit($input[$i]) 
                    && $input[$i] >= 0 && $input[$i] <= self::MAX_RANGE) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    /**
     * Maakt een geheime code bestaande uit NUMBER_OF_DIGITS willekeurige 
     * getallen in het bereik van 1 t/m MAX_RANGE en gooit deze in een array.
     *
     * @return Een array met de gemaakte code. Elk cijfer zit in een 
     * aparte index.
     */
    private function createSecret() {
                
        for($i = 0; $i < self::NUMBER_OF_DIGITS; $i++) {
            
            $secret[$i] = mt_rand(1, self::MAX_RANGE);
        }
        
        return $secret;
    }
    
    /**
     * Maakt en geeft html-code van de kluis.
     *
     * @return Html-code van de kluis.
     */
    public function showSafe() {
        
        $html = "\t<form action=\"\" method=\"post\" id=\"safe-form\" ";
        $html .= "autocomplete=\"off\">\n";
        
        for($i = 0; $i < self::NUMBER_OF_DIGITS; $i++) {
            $html .= "\t<div class=\"wheel-container\">\n";
            $html .= "\t\t<div class=\"wheel\">\n";
            $html .= "\t\t\t<input type=\"number\" class=\"number\" name=\"$i\" ";
            $html .= "value=\"".($this->cracked ? $this->secret[$i] : 0)."\" ";
            $html .= "maxlength=\"1\" min=\"1\" max=\"".self::MAX_RANGE."\" ";
            $html .= $this->finished ? "disabled " : "";
            $html .= "required>\n\t\t</div>\n\t</div>\n";
        }
        
        $html .= "\t<input type=\"submit\" id=\"enter-button\" ";
        $html .= $this->finished ? "disabled " : "";
        $html .= "name=\"enter\" value=\"Enter\">\n\t</form>\n";
        
        return $html;
    }
    
    /**
     * Maakt en geeft de html-code van de lijst met geprobeerde 
     * combinaties en de daarbij behorende hints.
     *
     * @return Html-code van de 'geprobeerd'-lijst.
     */
    public function showGuessedList() {
        
        $html = "\t<table id=\"guessed-list\">\n";
        
        foreach($this->guessed_list as $guessed) {
            
            $html .= "\t\t<tr class=\"line\">\n\t\t\t<td class=\"code\">";
            $html .= $guessed['code']."</td>\n\t\t\t<td>";
            
            for($i = 0; $i < $guessed['green']; $i++) {
                $html .= "<div class=\"color green\"></div>";
            }
            
            for($i = 0; $i < $guessed['yellow']; $i++) {
                $html .= "<div class=\"color yellow\"></div>";
            }
            
            for($i = 0; $i < $guessed['red']; $i++) {
                $html .= "<div class=\"color red\"></div>";
            }
            
            $html .= "</td>\n\t\t</tr>\n";
        }
        
        $html .= "\t</table>\n";
        return $html;
    }
    
    /**
     * Spuugt een bericht op basis van je resultaat.
     * @return Een bericht als string.
     */
    public function showResultMessage() {
        
        $message = "\t<p id=\"result-message\">";
        
        if($this->cracked) {
            
            $message .= "Geniaal! Je hebt de code gekraakt!";
            
        } else if($this->failed) {
            
            $message .= "Helaas. De code was ".implode($this->secret).".";
        }
        
        $message .= "</p>" . PHP_EOL;
        return $message;
    }
}
