<?php 

include 'Token.php';

class Lexer {
  
  static $letters = "abcdefghijklmnopqrstuvwxyz";
  static $digits = "0123456789";
  public $prog;
  public $i;
  
  function __construct($s){
	  $this->prog = str_split($s);
	  $this->i = 0;
  }

  public function nextToken(){
   
    while ( $this->i < sizeof($this->prog) && ($this->prog[$this->i]==' ' || $this->prog[$this->i]== PHP_EOL) ){
      $this->i++;
    }
    
    if ($this->i >= sizeof($this->prog)){
      return new Token(Token::EOF);
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

    if (strpos(Lexer::$digits, $this->prog[$this->i]) !== false){ 
      $digit = $this->prog[$this->i];
      $this->i++;
      return new Token(Token::VALUE, "".$digit , intval($digit) );
    }
    
    if (strpos(Lexer::$letters, $this->prog[$this->i]) !== false){
      $id = "";
      while ($this->i < sizeof($this->prog) && strpos(Lexer::$letters, $this->prog[$this->i]) !== false){
        $id .= $this->prog[$this->i];
        $this->i++;
      }
      if ($id == "if"){
        return new Token(Token::__IF,$id);
      }
      if ($id == "else"){
        return new Token(Token::__ELSE,$id);
      } 
      if (sizeof($id) == 1) {
        return new Token(Token::ID, $id);
      }
      return new Token(Token::INVALID, "");
    }
    
    return new Token(Token::INVALID, "");
  }
}
?>