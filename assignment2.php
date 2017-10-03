<?php 

include 'Lexer.php';

  $oneIndent = "   ";
  $values = array();
  $currentToken;
  $lex;
  
  mainExecution();

  function mainExecution(){    
    
    global $oneIndent, $currentToken, $lex;
      
    $header = "<html>\n  <head>\n    <title>Program Evaluator</title>\n  </head>\n  <body>\n  <pre>";
    print $header."\n";
    $programsUrl = "http://cs5339.cs.utep.edu/longpre/assignment2/programs.txt";
    try {
      $inp = fopen($programsUrl, 'r');
      $programsInputLine;
      while ( ($programsInputLine = fgets($inp) ) != false){
        $inputUrl = $programsInputLine;
        $inputUrl = trim ($inputUrl);
        print $inputUrl."\n";
        $program = "";
        try {
          $in = fopen($inputUrl, "r");
          $inputLine;
          while ( ($inputLine = fgets($in) )  != false){
            $inputLine = trim ($inputLine);
            $program .= " ".$inputLine;
          }
        }catch (Exception $e) {
          print "Caught exception! \n";
        }
        $lex = new Lexer($program);
        $currentToken = $lex->nextToken();
        try {
          execProg($oneIndent);
          if ($currentToken->type != Token::EOF){
            print "Unexpected characters at the end of the program\n";
            throw new Exception();
          }
        } catch (Exception $e) {
          print "<br/>Program parsing aborted";
        }
        print "\n";
      }
    } catch (Exception $e) {
        print "Caught exception! \n";
    }
    $footer = "  </pre>\n  </body>\n</html>";
    print $footer;
  }

  function execProg($indent){
    global $currentToken;
    while ($currentToken->type == Token::ID || $currentToken->type == Token::__IF){
      execStatement($indent, true);
    }
    print "\n";
    execResults($indent);
  }

  function execStatement($indent, $executing){
    global $currentToken;
    if($currentToken->type == Token::ID){
      execAssign($indent, $executing);
    } else {
      execConditional($indent, $executing);
    }
  }

  function execAssign($indent, $executing){
    global $values, $currentToken, $lex;
    $c = $currentToken->str{0};
    $currentToken = $lex->nextToken();
    if ($currentToken->type != Token::EQUAL){
      print "/n equal sign expected\n";
      throw new Exception();
    }
    $currentToken = $lex->nextToken();

    print $indent.$c." = ";
    $value = execExpr($indent);
    print "\n";
    if ($executing){
      $values[$c] = $value;
    }
  }

  function execConditional($indent, $executing){
    global $oneIndent, $currentToken, $lex;
    print $indent."if ";
    $currentToken = $lex->nextToken();
    $condResult = execCond($indent);
    print " {\n";
    
    if ($currentToken->type != Token::LBRACKET){
      print "Left bracket expected\n";
      throw new Exception();
    }
    
    $currentToken = $lex->nextToken();
    
    while ($currentToken->type == Token::ID || $currentToken->type == Token::__IF){
      execStatement($indent.$oneIndent, $condResult);
    }
    
    if ($currentToken->type != Token::RBRACKET){
      print "Right bracket or statement expected\n";
      throw new Exception();
    }
    
    print $indent."}";
    $currentToken = $lex->nextToken();
    
    if ($currentToken->type == Token::__ELSE){
      $currentToken = $lex->nextToken();
      if ($currentToken->type != Token::LBRACKET){
        print "Left bracket expected\n";
        throw new Exception();
      }
      $currentToken = $lex->nextToken();
      print " else {\n";
      while ($currentToken->type == Token::ID || $currentToken->type == Token::__IF){
        execStatement($indent.$oneIndent, !$condResult);
      }
      if ($currentToken->type != Token::RBRACKET){

        print "Right bracket or statement expected\n";
        throw new Exception();
      }
      print $indent."}";
      $currentToken = $lex->nextToken();
    }
    print "\n";      
  }

  function execCond($indent){
    global $currentToken, $lex;
    if ($currentToken->type != Token::LPAREN){
      print "Left parenthesis expected\n";
      throw new Exception();
    }
    print "(";
    $currentToken = $lex->nextToken();
    $v1 = execExpr($indent);
    if ($currentToken->type != Token::LESS){
      print "LESS THAN expected\n";
      throw new Exception();
    }
    print "&lt;";
    $currentToken = $lex->nextToken();
    $v2 = execExpr($indent);
    if ($currentToken->type != Token::RPAREN){
      print "Right parenthesis expected\n";
      throw new Exception();
    }
    print ")";
    $currentToken = $lex->nextToken();
    return $v1 < $v2;
  }

  function execExpr($indent){
    global $values, $currentToken, $lex;
    if ($currentToken->type == Token::VALUE){
      $val = (int) $currentToken->val;
      print $val;
      $currentToken = $lex->nextToken();
      return $val;
    }
    if ($currentToken->type == Token::ID){
      $c = $currentToken->str{0};
      if (array_key_exists($c, $values)){
        print $c;
        $currentToken = $lex->nextToken();
        return (int) $values[$c];
      } else {
        print "Reference to an undefined variable\n";
        throw new Exception();
      }
    }      
    print "An expression should be either a digit or a letter\n";
    throw new Exception();
  }

  function execResults($indent){
    global $values, $currentToken, $lex;
    if ($currentToken->type != Token::COLON){
      print "COLON or statement expected\n";
      throw new Exception();
    }
    $currentToken = $lex->nextToken();
    while ($currentToken->type == Token::ID){
      $c = $currentToken->str{0};
      $currentToken = $lex->nextToken();
      if (array_key_exists($c, $values)){
        print "The value of ".$c." is ".$values[$c]."\n";
      } else {
        print "The value of ".$c." is undefined\n";
      }
    }
  }
  
?>