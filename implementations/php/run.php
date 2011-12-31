<?php

function TEMPLATE_ENGINE_PHP_info(){
  return array(
    "template_engine_name" =>  'php',
    "template_engine_version" => phpversion(),
    "template_engine_settings" => "",
    "template_engine_url" => "http://php.net"
  );
}

# this is part of the benchmark
function TEMPLATE_ENGINE_PHP_setup($tmp_dir){
  // nothing to do
}

// this func is used for the benchmark:
// data for case 1 (dump 100 rows), see CASE 1 above
// data for case 2 rendered html result of case 1 thus the result is a complete HTML page.
function TEMPLATE_ENGINE_PHP_run($case, $data){

  ob_start();
  // PHP reference implementation
  switch ($case) {
    // TEMPLATE CASE 2
    case 'case_1_rows':
      foreach($data as $row){
        echo "<table>\n";
        foreach($row as $key => $value){
          echo "<tr>\n";
          // quoting!
          echo "<td>".htmlentities($key  , ENT_QUOTES, 'utf-8').'</td>';
          echo "<td>".htmlentities($value, ENT_QUOTES, 'utf-8').'</td>';
          echo "</tr>\n";
        }
        echo "</table>";
      }
      break;

    // TEMPLATE CASE 1
    case 'case_2_page':
      echo  "<html>\n";
      echo  "<head>\n";
      // quoting!
      echo  "  <title>".htmlentities($data['title'], ENT_QUOTES, 'utf-8').'</title>';
      echo  "  <meta name=\"description\" value=\"".htmlentities($data['description'], ENT_QUOTES, 'utf-8')."\"></meta>";
      echo  "</head>\n";
      echo  "<body>\n";
      // no quoting!
      echo  "  <div id=\"header\">".$data['header']."</div>\n";
      echo  "  <div id=\"navigation\">".$data['navigation']."</div>\n";
      echo  "  <div id=\"content\">".$data['content']."</div>\n";
      echo  "</body>\n";
      echo  "</html>\n";
      break;

    default:
	  throw new Exception("unexpected ".$case);
  }

  return ob_get_clean();
}
