package fall17program;

import fall17program.Token.TokenList;
/**
 * @author Luc Longpre for Secure Web-Based Systems, Fall 2017
 */
public class Lexer {

  static String letters = "abcdefghijklmnopqrstuvwxyz";
  static String digits = "0123456789";

  private char[] prog; //char array containing the characters of the program
  private int i; //index of the current character
  
  public Lexer(String s){
  // constructor: convert the program from String to Array of Char
    prog = s.toCharArray();
    i=0;
  }

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
}
