<?php 

include 'Token.php';




class Lexer {
  //static String letters = "abcdefghijklmnopqrstuvwxyz";
  //static String digits = "0123456789";
  
  static $letters = "abcdefghijklmnopqrstuvwxyz";
  static $digits = "0123456789";
  public $prog = array();
  public $i;
  //private char[] prog; //char array containing the characters of the program
  //private int i; //index of the current character
 
  function __construct($s){
	  $this->prog = str_split($s);
	  $this->i = 0;
  }
  
  /*
  public Lexer(String s){
  // constructor: convert the program from String to Array of Char
    prog = s.toCharArray();
    i=0;
  }
  */
  
    public function nextToken(){
      //skip blanks and new lines
      while ( $this->i < sizeof($this->prog) && ($this->prog[$this->i]==' ' || $this->prog[$this->i]== PHP_EOL)){
        $this->i++;
      }
      
      if ($this->i >= sizeof($this->prog)){
        return new Token(TokenList::EOF);
      }
		
      switch ($this->prog[$this->i]) {
        case '(':
          $this->i++;
          return new Token(Token::LPAREN, "(");
        case ')':
          $this->i++;
          return new Token(Token::RPAREN,")");
        case '{':
          $this->i++;
          return new Token(Token::LBRACKET, "{");
        case '}':
          $this->i++;
          return new Token(Token::RBRACKET,"}");            
        case '<':
          $this->i++;
          return new Token(Token::LESS,"<");
        case '=':
          $this->i++;
          return new Token(Token::EQUAL,"=");            
        case ':':
          $this->i++;
          return new Token(Token::COLON,":");         
      }
      
      if (strpos(Lexer::$digits, $this->prog[$this->i]) != -1){ 
        // prog[i] is a digit. We only allow one digit
        $digit = $this->prog[$this->i];
        $this->i++;
        return new Token(Token::VALUE, "".$digit , intval($digit));
      }
      
      if (strpos(Lexer::$letters, $this->prog[$this->i]) != -1){
        $id = "";
        while ($this->i < sizeof($this->prog) && strpos(Lexer::$letters, $this->prog[$this->i]) != -1){
          $id += $this->prog[i];
          $this->i++;
        }
        // check against reserved words
        if ($id == "if"){
          return new Token(Token::__IF,$id);
        }
        if ($id == "else"){
          return new Token(Token::__ELSE,$id);
        } 
        if (sizeof($id) == 1) {
          // We only allow one lower case letter as identifier
          return new Token(Token::ID, $id);
        }
        return new Token(Token::INVALID, "");
      }
      return new Token(Token::INVALID, "");
  }
  /*
  public Token next(){
    //skip blanks and new lines
    while ( i<prog.length && (prog[i]==' ' || prog[i]=='\n')){
      i++;
    }
    if (i>=prog.length){
      return new Token(TokenList.EOF);
    }
    switch (prog[i]) {
        case '(':
            i++;
            return new Token(TokenList.LPAREN, "(");
        case ')':
            i++;
            return new Token(TokenList.RPAREN,")");
        case '{':
            i++;
            return new Token(TokenList.LBRACKET, "{");
        case '}':
            i++;
            return new Token(TokenList.RBRACKET,"}");            
        case '<':
            i++;
            return new Token(TokenList.LESS,"<");
        case '=':
            i++;
            return new Token(TokenList.EQUAL,"=");            
        case ':':
            i++;
            return new Token(TokenList.COLON,":");         
    }
    if (digits.indexOf(prog[i]) != -1){ 
        // prog[i] is a digit. We only allow one digit
        char digit = prog[i];
        i++;
        return new Token(TokenList.VALUE,""+digit,Character.getNumericValue(digit));
    }
    if (letters.indexOf(prog[i]) != -1){
        String id = "";
        while (i<prog.length && letters.indexOf(prog[i])!=-1){
            id+=prog[i];
            i++;
        }
        // check against reserved words
        if ("if".equals(id)){
            return new Token(TokenList.IF,id);
        }
        if ("else".equals(id)){
            return new Token(TokenList.ELSE,id);
        } 
        if (id.length() == 1) {
            // We only allow one lower case letter as identifier
            return new Token(TokenList.ID, id);
        }
        return new Token(TokenList.INVALID,"");
    }
    return new Token(TokenList.INVALID,"");
  }
  */
}
?>