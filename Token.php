<?php 
public class Token extends SplEnum {
    // Token objects are returned by the Lexer
    
	/*
	public enum TokenList {
        LPAREN, RPAREN, LBRACKET, RBRACKET, LESS, EQUAL, COLON, ID, VALUE, IF, ELSE, EOF, INVALID
    }
	*/
	
	const __default = self::INVALID;
	const LPAREN = "LPAREN";
	const RPAREN = "RPAREN";
	const LBRACKET = "LBRACKET";
	const LESS = "LESS";
	const EQUAL = "EQUAL";
	const COLON = "COLON";
	const ID = "ID";
	const VALUE = "VALUE";
	const __IF = "__IF";
	const __ELSE = "__ELSE";
	const EOF = "EOF";	
	const INVALID = "INVALID";	
	
    //TokenList type;
	//String str;
    //int val;
	
	$type;
	$str;
	$val;
	
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
		$type = $theType;
    }
 
    public function __construct2($theType, $theString) {
		$type = $theType;
		$str = $theString;
    }
 
    public function __construct3($theType, $theString, $theVal) {
		$type = $theType;
		$str = $theString;
		$val = $theVal;
    }
	
	//$test = new Token('EOF');
	//echo $test;
}
?>