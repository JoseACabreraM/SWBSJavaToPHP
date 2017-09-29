package fall17program;
/**
 * @author Luc Longpre for Secure Web-Based Systems, Fall 2017
 */
public class Token {
    // Token objects are returned by the Lexer
    public enum TokenList {
        LPAREN, RPAREN, LBRACKET, RBRACKET, LESS, EQUAL, COLON, ID, VALUE, IF, ELSE, EOF, INVALID
    }

    TokenList type;
    String str;
    int val;

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
}
