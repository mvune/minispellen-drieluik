<?php
class Hanoi {
	public $discs,
			$sticks = array(array(), array(), array()),
			$from,
			$message,
			$finished;
	
	public function __construct($discs = 6) {
		$this->discs = (int) $discs;
		
		$start_stick = array();
		for($i = $this->discs; $i >= 1; $i--) {
			$start_stick[] = $i;
		}
		$this->sticks[0] = $start_stick;
	}
	
	public function display() {
		$html = "";
		
		for($x = 0; $x < 3; $x++) {
			$rows = "";
			$button = "";
			$button_width = $this->discs * 42;
			$stick = $this->sticks[$x];
			
			for($y = 0; $y < $this->discs; $y++) {
				
				if(isset($stick[$y])) {
					if(is_numeric($stick[$y])) {
						$width = $stick[$y] * 42;
					}
					$rows = "<tr><td><span class='discs' style='width:{$width}px'>&nbsp;</span></td></tr>" . $rows;
				} else {
					$rows = "<tr><td><span class='sticks'>&nbsp;</span></td></tr>" . $rows;
				}
			}
			
			if(!$this->finished) {
				$button .= "\n\t<tr><td><form action='' method='post'><button style='width:{$button_width}px' ";
				$button .= "type='submit' ";
				if(isset($this->from)) {
					if($x != $this->from) {
						$button .= "name='to' value='{$x}'>Naar hier</button></form></td></tr>";
					} else {
						$button .= "name='cancel'>Annuleren</button></form></td></tr>";
					}
				} else {
					$button .= "name='from' value='{$x}'>Van hier</button></form></td></tr>";
				}
			} else {
				$button .= "<tr><td><form><button style='width:{$button_width}px;cursor:default' ";
				$button .= "disabled='disabled'>&nbsp;</button></form></td></tr>";
			}
			
			$html .= "<table>" . $rows . $button . "\n</table>";
		}
		return $html;
	}
	
	public function executeTurn($post = array()) {
		if(isset($post['from'])) {
			if(!empty($this->sticks[$post['from']])) {
				$this->from = $post['from'];
				return;
			} else {
				$this->message = "Kan niet hè.";
				return;
			}
		}
		if(isset($post['to']) && isset($this->from)) {
			$from_stick = array_values($this->sticks[$this->from]);
			$disc_to_move = end($from_stick);
			
			if(!empty($this->sticks[$post['to']])) {
				$to_stick = array_values($this->sticks[$post['to']]);
				$on_disc = end($to_stick);
			} else {
				$on_disc = 100;
			}
			
			if($disc_to_move > $on_disc) {
				$this->message = "Sorry, kan niet. Lees de spelregels anders nog 'ns.";
				return;
			} else {
				$moving_disc = array_pop($this->sticks[$this->from]);
				$this->sticks[$post['to']][] = $moving_disc;
				$this->from = null;
				$this->message = "";
			}
		}
		if(isset($post['cancel'])) {
			$this->from = null;
			return;
		}
		$this->checkForWin();
	}
	
	private function checkForWin() {
		if(isset($this->sticks[1][$this->discs - 1]) || isset($this->sticks[2][$this->discs - 1])) {
			$this->finished = true;
			$this->message = "Jep, dat was 'm. Goed man!";
		}
	}
	
	public function getMessage() {
		$message = $this->message;
		$this->message = "";
		return $message;
	}
	
	public function numberOfDiscs() {
		return $this->discs;
	}
}
