<?php
class Tictactoe {
    public  $in_a_row,
            $board_size,
            $opponent;
    private $_board = array(),
            $_analysis_board = array(),
            $_analysis_results = array(),
            $_fields_left,
            $_active_player = '&times;',
            $_winner;
            
    public function __construct($opponent = 'cpu-expert', $board_size = 7, $in_a_row = 5) {
        $this->in_a_row = $in_a_row;
        $this->board_size = $board_size;
        $this->opponent = $opponent;
        $this->_fields_left = $this->board_size * $this->board_size;
    }
    
    public function displayBoard($type = 'game') {
        if($type == 'analysis') {
            $board = $this->_analysis_board;
        } else {
            $board = $this->_board;
        }
        
        $html = "<table>";
        
        for($y = 1; $y <= $this->board_size; $y++) {
            $html .= "\n\t<tr>";
            
            for($x = 1; $x <= $this->board_size; $x++) {
                if(isset($board[$y][$x])) {
                    $html .= "\n\t\t<td>".$board[$y][$x]."</td>";
                } else {
                    if(!isset($this->_winner)) {
                        $html .= "\n\t\t<td><form class='pickfield' action='' method='post'>";
                        $html .= "<button class='pickfield' type='submit' name='field'";
                        $html .= " value='$y-$x'></button></form></td>";
                    } else {
                        $html .= "\n\t\t<td></td>";
                    }
                }
            }
            $html .= "\n\t</tr>";
        }
        $html .= "\n</table>";
        return $html;       
    }
    
    public function executeTurn($field) {
        if(strpos($field, "-") !== false) {
            $values = explode("-", $field);
            $y = $values[0];
            $x = $values[1];
            
            $this->_board[$y][$x] = ($this->_active_player == '&times;') ? "&times;" : "&cir;";
            $this->_fields_left--;
            $this->_checkForWinner();
            $this->_changeTurn();
            
            if($this->_active_player == '&cir;' && !isset($this->_winner) && $this->_fields_left > 0) {
                switch($this->opponent) {
                    case 'cpu-beginner':
                        $this->_executeCpuBeginnerTurn();
                        break;
                    case 'cpu-expert':
                        $this->_executeCpuExpertTurn();
                        break;
                }
            }
        }
    }
    
    private function _executeCpuBeginnerTurn() {
        $random_y = mt_rand(1, $this->board_size);
        $random_x = mt_rand(1, $this->board_size);
        if(!isset($this->_board[$random_y][$random_x])) {
            $this->executeTurn($random_y . "-" . $random_x);
        } else {
            $this->_executeCpuBeginnerTurn();
        }
    }
    
    private function _executeCpuExpertTurn() {
        // Resultatenarrays leeg maken.
        $this->_analysis_board = array();
        $this->_analysis_results = array();
        
        // Zetten analyseren.
        $this->_analyseMoves($this->_active_player);
        $this->_analyseMoves($this->_active_player == '&times;' ? '&cir;' : '&times;');
        
        // Zetten met hoogste score uit resultatenarray halen.
        $best_bets = array_keys($this->_analysis_results, max($this->_analysis_results));
        // Daaruit een willekeurige zet pakken.
        $field = $best_bets[mt_rand(0, count($best_bets) - 1)];
        
        // Zet uitvoeren.
        $this->executeTurn($field);
    }
    
    private function _analyseMoves($player) {
        // Voor elk veld op het bord met coördinaten ($x, $y):
        for($y = 1; $y <= $this->board_size; $y++) {
            for($x = 1; $x <= $this->board_size; $x++) {
                // Alleen als het veld leeg is:
                if(empty($this->_board[$y][$x])) {
                    // Check horizontaal.
                    $this->_analyseRow($y, $x, 0, 1, $player);
                    // Check verticaal.
                    $this->_analyseRow($y, $x, 1, 0, $player);
                    // Check diagonaal van linksboven naar rechtsonder.
                    $this->_analyseRow($y, $x, 1, 1, $player);
                    // Check diagonaal van rechtsboven naar linksonder.
                    $this->_analyseRow($y, $x, 1, -1, $player);
                }
            }
        }
    }
    
    private function _analyseRow($y, $x, $y_direction, $x_direction, $player) {
        $params = array(
            'occupied'  => 0,
            'adjacent'  => 0,
            'empty'         => 1,
            'dead_ends' => 0
        );
        
        // Check rij in ene richting.
        $this->_analyseRowOneDirection($y, $x, $y_direction, $x_direction, $player, $params);
        // Check zelfde rij, maar in tegengestelde richting.
        $this->_analyseRowOneDirection($y, $x, $y_direction * -1, $x_direction * -1, $player, $params);
        
        // Op basis van bovenstaande parameters, score toekennen aan zet voor deze rij.
        $score = $this->_assignScore($player, $params);
        
        // Plaats score in resultatenarray.
        @$this->_analysis_board[$y][$x] += $score;
        @$this->_analysis_results[$y."-".$x] += $score;
    }
        
    private function _analyseRowOneDirection($y, $x, $y_direction, $x_direction, $player, &$params) {
        $is_adjacent = true;
        $previous_field = '';
        
        for($i = 1; $i < $this->in_a_row; $i++) {
            $y_next = $y + $y_direction * $i;
            $x_next = $x + $x_direction * $i;
            
            switch(true) {
                case $this->_fieldIsNotOnBoard($y_next, $x_next):
                    if($previous_field == 'occupied') {
                        $params['dead_ends']++;
                    }
                    break 2;
                case $this->_fieldIsEmpty($y_next, $x_next):
                    $is_adjacent = false;
                    $params['empty']++;
                    break;
                case $this->_board[$y_next][$x_next] == $player:
                    $params['occupied']++;
                    $previous_field = 'occupied';
                    if($is_adjacent) {
                        $params['adjacent']++;
                    }
                    break;
                default:
                    if($previous_field == 'occupied') {
                        $params['dead_ends']++;
                    }
                    break 2;
            }
        }
    }
    
    private function _assignScore($player, $params) {
        if($params['empty'] + $params['occupied'] < $this->in_a_row) {
            $score = 0;
        } else if($params['adjacent'] >= $this->in_a_row - 1) {
            $score = ($player == $this->_active_player) ? 10000 : 1000;
        } else if($params['adjacent'] == $this->in_a_row - 2) {
            if($params['empty'] > 2) {
                if($params['dead_ends'] == 0) {
                    $score = ($player == $this->_active_player) ? 250 : 80;
                } else {
                    $score = ($player == $this->_active_player) ? 119 : 39;
                }
            } else if($params['empty'] == 2) {
                if($params['dead_ends'] == 0) {
                    $score = ($player == $this->_active_player) ? 120 : 40;
                } else {
                    $score = ($player == $this->_active_player) ? 59 : 19;
                }
            }
        } else if($params['occupied'] == $this->in_a_row - 2) {
            if($params['empty'] > 2) {
                $score = ($player == $this->_active_player) ? 60 : 20;
            } else if($params['empty'] == 2) {
                $score = ($player == $this->_active_player) ? 30 : 10;
            }
        } else if($params['adjacent'] == $this->in_a_row - 3) {
            if($params['empty'] > 3) {
                $score = ($player == $this->_active_player) ? 48 : 12;
            } else if($params['empty'] == 3) {
                $score = ($player == $this->_active_player) ? 36 : 6;
            }
        } else if($params['occupied'] == $this->in_a_row - 3) {
            if($params['empty'] > 3) {
                $score = ($player == $this->_active_player) ? 6 : 2;
            } else if($params['empty'] == 3) {
                $score = ($player == $this->_active_player) ? 3 : 1;
            }
        }
        return isset($score) ? $score : 0;
    }
        
    private function _checkForWinner() {
        // Voor elk veld op het bord met coördinaten ($x, $y):
        for($y = 1; $y <= $this->board_size; $y++) {
            for($x = 1; $x <= $this->board_size; $x++) {
                // Als het veld niet leeg is:
                if(!empty($this->_board[$y][$x])) {
                    switch(true) {
                        // Check horizontaal.
                        case $this->_checkForWinningRow($y, $x, 0, 1):
                        // Check verticaal.
                        case $this->_checkForWinningRow($y, $x, 1, 0):
                        // Check diagonaal van linksboven naar rechtsonder.
                        case $this->_checkForWinningRow($y, $x, 1, 1):
                        // Check diagonaal van rechtsboven naar linksonder.
                        case $this->_checkForWinningRow($y, $x, 1, -1):
                            $this->_winner = $this->_board[$y][$x];
                            return;
                    }
                }
            }
        }
    }
    
    private function _checkForWinningRow($y, $x, $y_direction, $x_direction) {
        for($i = 1; $i < $this->in_a_row; $i++) {
            $y_next = $y + $y_direction * $i;
            $x_next = $x + $x_direction * $i;
            
            switch(true) {
                case $this->_fieldIsNotOnBoard($y_next, $x_next):
                case $this->_fieldIsEmpty($y_next, $x_next):
                case $this->_fieldDoesNotMatch($y, $x, $y_next, $x_next):
                    return false;
            }
        }
        return true;
    }
    
    private function _fieldIsNotOnBoard($y, $x) {
        return ($y < 1 || $x < 1 || $y > $this->board_size || $x > $this->board_size) ? true : false;
    }
    
    private function _fieldIsEmpty($y, $x) {
        return (!isset($this->_board[$y][$x])) ? true : false;
    }
    
    private function _fieldDoesNotMatch($y, $x, $y_next, $x_next) {
        return (!empty($this->_board[$y_next][$x_next]) && $this->_board[$y][$x] !== $this->_board[$y_next][$x_next]) ? true : false;
    }
    
    private function _changeTurn() {
        $this->_active_player = ($this->_active_player == '&times;') ? '&cir;' : '&times;';
    }
    
    public function getResult() {
        if($this->_winner) {
            return $this->_winner . "-je wint!";
        }
        if($this->_fields_left === 0) {
            return "Gelijkspel.";
        }
    }
}
