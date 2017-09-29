package fall17program;

import fall17program.Token.TokenList;
import java.io.*;
import java.net.URL;
import java.util.*;
/**
 * @author Luc Longpre for Secure Web-Based Systems, Fall 2017
 *
 * This program parses and interprets a simple programming language that has
 * only single digit assignments to variables and nested if or if-else
 * statements.
 *
 * <program> ::= <statement>* <results>
 * <statement> ::= <assignment> | <conditional>
 * <assignment> ::= ID '=' <expression>
 * <expression> ::= ID | VALUE
 * <conditional> ::= 'if' <condition> '{' <statement>* '}' [ 'else' '{'
 * <statement>* '}' ]
 * <condition> ::= '(' <expression> '<' <expression> ')' <
 * results> ::= ':' ID* ID is [a-z] (one lower case letter) VALUE is [0-9] (one
 * digit)
 *
 */
public class Fall17Program {

    static String letters = "abcdefghijklmnopqrstuvwxyz";
    static String digits = "0123456789";
    static HashMap values = new HashMap();
    static Token currentToken;
    static Lexer lex;
    static String oneIndent = "   "; //amount of space for one level of indentation

    public static void main(String[] args) throws Exception {
        // open the URL into a buffered reader and read into a string
        // print the header,
        // read the program one token at a time, interpreting and printing a formatted version,
        // execute the results part of the program
        // print the footer.
        String header = "<html>\n"
                + "  <head>\n"
                + "    <title>Program Evaluator</title>\n"
                + "  </head>\n"
                + "  <body>\n"
                + "  <pre>";
        System.out.println(header);

        URL programsUrl = new URL("http://cs5339.cs.utep.edu/longpre/assignment2/programs.txt");
        try ( // Java try-with-resource statement
            BufferedReader inp = new BufferedReader(new InputStreamReader(programsUrl.openStream()))) {
            String programsInputLine;
            while ((programsInputLine = inp.readLine()) != null) {
                System.out.println(programsInputLine);
                URL inputUrl = new URL(programsInputLine);
                String program = "";
                // fetch file from URL into the program string
                try ( // Java try-with-resource statement
                    BufferedReader in = new BufferedReader(new InputStreamReader(inputUrl.openStream()))) {
                    String inputLine;
                    while ((inputLine = in.readLine()) != null) {
                        program += '\n' + inputLine;
                    }
                }

                lex = new Lexer(program);
                currentToken = lex.next();
                try {
                    execProg(oneIndent);
                    if (currentToken.type != TokenList.EOF) {
                        System.out.println("Unexpected characters at the end of the program");
                        throw new Exception();
                    }
                } catch (Exception ex) {
                    System.out.print("<br/>Program parsing aborted");
                }
                System.out.println();
            }
        }
        String footer = "  </pre>\n  </body>\n" + "</html>";
        System.out.println(footer);
    }

    public static void execProg(String indent) throws Exception {
        // <program>     ::= <statement>* <results>        
        // A statement starts with either ID or IF.
        // A result starts with COLON
        while (currentToken.type == TokenList.ID || currentToken.type == TokenList.IF) {
            execStatement(indent, true);
        }
        System.out.println();
        execResults(indent);
    }

    public static void execStatement(String indent, boolean executing) throws Exception {
        // <statement>   ::= <assignment> | <conditional>
        // An assignment starts with ID and a conditional starts with IF
        if (currentToken.type == TokenList.ID) {
            execAssign(indent, executing);
        } else { //we know current token is IF
            execConditional(indent, executing);
        }
    }

    public static void execAssign(String indent, boolean executing) throws Exception {
        // <assignment>  ::= ID '=' <expression>
        // We know the current token is ID
        char c = currentToken.str.charAt(0);
        currentToken = lex.next();
        if (currentToken.type != TokenList.EQUAL) {
            System.out.println("/n equal sign expected");
            throw new Exception();
        }
        currentToken = lex.next();
        System.out.print(indent + c + " = ");
        int value = execExpr(indent);
        System.out.println();
        if (executing) {
            values.put(c, value);
        }
    }

    public static void execConditional(String indent, boolean executing) throws Exception {
        // <conditional> ::= 'if' <condition> '{' <statement>* '}'
        //                   [ 'else' '{' <statement>* '}' ]
        System.out.print(indent + "if ");
        currentToken = lex.next(); // skip to token following IF
        boolean condResult = execCond(indent);
        System.out.print(" {\n");
        if (currentToken.type != TokenList.LBRACKET) {
            System.out.println("Left bracket expected");
            throw new Exception();
        }
        currentToken = lex.next();
        while (currentToken.type == TokenList.ID || currentToken.type == TokenList.IF) {
            execStatement(indent + oneIndent, condResult);
        }
        if (currentToken.type != TokenList.RBRACKET) {
            System.out.println("Right bracket or statement expected");
            throw new Exception();
        }
        System.out.print(indent + "}");
        currentToken = lex.next();
        if (currentToken.type == TokenList.ELSE) {
            currentToken = lex.next();
            if (currentToken.type != TokenList.LBRACKET) {
                System.out.println("Left bracket expected");
                throw new Exception();
            }
            currentToken = lex.next();
            System.out.println(" else {");
            while (currentToken.type == TokenList.ID || currentToken.type == TokenList.IF) {
                execStatement(indent + oneIndent, !condResult);
            }
            if (currentToken.type != TokenList.RBRACKET) {
                System.out.println("Right bracket or statement expected");
                throw new Exception();
            }
            System.out.print(indent + "}");
            currentToken = lex.next();
        }
        System.out.println();
    }

    public static boolean execCond(String indent) throws Exception {
        // <condition>   ::= '(' <expression> '<' <expression> ')'        
        if (currentToken.type != TokenList.LPAREN) {
            System.out.println("Left parenthesis expected");
            throw new Exception();
        }
        System.out.print("(");
        currentToken = lex.next();
        int v1 = execExpr(indent);
        if (currentToken.type != TokenList.LESS) {
            System.out.println("LESS THAN expected");
            throw new Exception();
        }
        System.out.print("&lt;");
        currentToken = lex.next();
        int v2 = execExpr(indent);
        if (currentToken.type != TokenList.RPAREN) {
            System.out.println("Right parenthesis expected");
            throw new Exception();
        }
        System.out.print(")");
        currentToken = lex.next();
        return v1 < v2;
    }

    public static int execExpr(String indent) throws Exception {
        // <expression>  ::= ID | VALUE
        if (currentToken.type == TokenList.VALUE) {
            int val = currentToken.val;
            System.out.print(val);
            currentToken = lex.next();
            return val;
        }
        if (currentToken.type == TokenList.ID) {
            char c = currentToken.str.charAt(0);
            System.out.print(c);
            if (values.containsKey(c)) {
                currentToken = lex.next();
                return (int) values.get(c);
            } else {
                System.out.println("Reference to an undefined variable");
                throw new Exception();
            }
        }
        System.out.println("An expression should be either a digit or a letter");
        throw new Exception();
    }

    public static void execResults(String indent) throws Exception {
        // <results>     ::= ':' ID*
        if (currentToken.type != TokenList.COLON) {
            System.out.println("COLON or statement expected");
            throw new Exception();
        }
        currentToken = lex.next();
        while (currentToken.type == TokenList.ID) {
            char c = currentToken.str.charAt(0);
            currentToken = lex.next();
            if (values.containsKey(c)) {
                System.out.println("The value of " + c + " is " + values.get(c));
            } else {
                System.out.println("The value of " + c + " is undefined");
            }
        }
    }
}
