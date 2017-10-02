<?php 

	$type;
	$str;
	$val;
class Token {
    // Token objects are returned by the Lexer
    
	/*
	public enum TokenList {
        LPAREN, RPAREN, LBRACKET, RBRACKET, LESS, EQUAL, COLON, ID, VALUE, IF, ELSE, EOF, INVALID
    }
	*/
	
	const __default = self::INVALID;
	const LPAREN = 0;
	const RPAREN = 1;
	const LBRACKET = 2;
	const LESS = 3;
	const EQUAL = 4;
	const COLON = 5;
	const ID = 6;
	const VALUE = 7;
	const __IF = 8;
	const __ELSE = 9;
	const EOF = 10;	
	const INVALID = 11;	
	
    //TokenList type;
	//String str;
    //int val;
	

	
	function __construct() {
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 1:
                self::__construct1($argv[0]);
                break;
            case 2:
                self::__construct2($argv[0], $argv[1]);
                break;
            case 3:
                self::__construct2($argv[0], $argv[1], $argv[2]);
         }
    }
 
	/*
    public Token(TokenList theType) {
        type = theType;
    }

    public Token(TokenList theType, String theString) {
        type = theType;
        str = theString;
    }

    public Token(TokenList theType, String theString, int theVal) {
        type = theType;
        str = theString;
        val = theVal;
    }
	*/
	
	public function __construct1($theType) {
		$this->type = $theType;
    }
 
  public function __construct2($theType, $theString) {
    $this->type = $theType;
    $this->str = $theString;
  }
 
  public function __construct3($theType, $theString, $theVal) {
    $this->type = $theType;
    $this->str = $theString;
    $this->val = $theVal;
  }
	
	//$test = new Token('EOF');
	//echo $test;
}
?>