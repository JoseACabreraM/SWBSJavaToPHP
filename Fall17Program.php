<?php 

include 'Lexer.php';

    $letters = "abcdefghijklmnopqrstuvwxyz";
    $digits = "0123456789";
    $oneIndent = "   ";
    $values = array();
    $currentToken;
    $lex;
    mainExecution();

    function mainExecution(){    
      
      global $letters, $digits, $oneIndent, $values, $currentToken, $lex;
        
        
      $header = "<html>\n  <head>\n    <title>Program Evaluator</title>\n  </head>\n  <body>\n  <pre>";
      print $header."\n";
      $programsUrl = "http://cs5339.cs.utep.edu/longpre/assignment2/programs.txt";
      try {
        $inp = fopen($programsUrl, 'r');
        $programsInputLine;
        while ( ($programsInputLine = fgets($inp) ) != false){
          print $programsInputLine."\n";
          $inputUrl = $programsInputLine;
          $inputUrl = trim ($inputUrl);
          $program = "";
          try {
            $in = fopen($inputUrl, "r");
            $inputLine;
            while ( ($inputLine = fgets($in) )  != false){
              print $inputLine;
              $program .= "\n".$inputLine;
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
            print "<br/>Program parsing aborted\n";
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
      global $letters, $digits, $oneIndent, $values, $currentToken, $lex;
      while ($currentToken->type == Token::ID || $currentToken->type == Token::__IF){
        execStatement($indent, true);
      }
      print "\n";
      execResults($indent);
    }

    function execStatement($indent, $executing){
      global $letters, $digits, $oneIndent, $values, $currentToken, $lex;
      if($currentToken->type == Token::ID){
        execAssign($indent, $executing);
      } else {
        $this->execConditional($indent, $executing);
      }
    }

    function execAssign($indent, $executing){
      global $letters, $digits, $oneIndent, $values, $currentToken, $lex;
      $c = $currentToken->str{0};
      $currentToken = $lex->nextToken();
      if ($currentToken->type != Token::EQUAL){
        print "/n equal sign expected";
        throw new Exception();
      }
      $currentToken = $lex->nextToken();
      print $indent.$c." = ";
      $value = execExpr(indent);
      print "\n";
      if ($executing){
        $values[] = array($c => $value);
      }
    }

    function execConditional($indent, $executing){
      global $letters, $digits, $oneIndent, $values, $currentToken, $lex;
      print $indent."if ";
      $currentToken = $lex->nextToken();
      $condResult = execCond(indent);
      print " {\n";
      
      if ($currentToken::type != Token::LBRACKET){
        print "Left bracket expected\n";
        throw new Exception();
      }
      
      $currentToken = $lex->nextToken();
      while ($currentToken->type == Token::ID || $currentToken->type == Token::__IF){
        execStatement($indent.$oneIndent, condResult);
      }
      
      if ($currentToken->type != Token::RBRACKET){
        print "Right bracket or statement expected\n";
        throw new Exception();
      }
      
      print indent."}";
      $currentToken = $lex->nextToken();
      
      if ($currentToken->type == Token::__ELSE){
        $currentToken = $lex->nextToken();
        if ($currentToken->type != Token::LBRACKET){
          print "Left bracket expected\n";
          throw new Exception();
        }
        $currentToken = $lex->nextToken();
        print " else {\n";
        while ($currentToken->type == Token::ID || $currentToken->type == Token::IF){
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
      global $letters, $digits, $oneIndent, $values, $currentToken, $lex;
      if ($currentToken->type == Token::LPAREN){
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
      global $letters, $digits, $oneIndent, $values, $currentToken, $lex;
      if ($currentToken->type == TokenList::VALUE){
        $val = $currentToken->val;
        print $val;
        $currentToken = $lex.nextToken();
        return $val;
      }
      if ($currentToken->type == TokenList::ID){
        $c = $currentToken->str{0};
        print $c;
        if (array_key_exists($values, $c)){
          $currentToken = $lex->nextToken();
          return $values[$c];
        } else {
          print "Reference to an undefined variable\n";
          throw new Exception();
        }
      }      
      print "An expression should be either a digit or a letter\n";
      throw new Exception();
    }

    function execResults($indent){
      global $letters, $digits, $oneIndent, $values, $currentToken, $lex;
      if ($currentToken->type != Token::COLON){
        print "COLON or statement expected\n";
        throw new Exception();
      }
      $currentToken = $lex->nextToken();
      while ($currentToken->type == Token::ID){
        $c = $currentToken->str{0};
        $currentToken = $lex->nextToken();
        if (array_key_exists($values, $c)){
          print "The value of ".$c." is ".$values[$c];
        } else {
          print "The value of ".$c." is undefined";
        }
      }
      
    }


?>