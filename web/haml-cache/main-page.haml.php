<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php
 echo htmlentities($title, ENT_QUOTES, 'utf-8')?></title>

<meta http-equiv='content-type' content='text/html; charset=UTF-8' />
<?php
  $lang_meta = strtolower(LANG_META)?>
<meta name='language' <?php
 echo HamlUtilities::renderAttribute('content',($lang_meta),'\'','utf-8',false)?> />

<meta name='description' <?php
 echo HamlUtilities::renderAttribute('content','nifyt expressive template engine for PHP: '.(($title)),'\'','utf-8',false)?> />

<meta http-equiv='content-language' <?php
 echo HamlUtilities::renderAttribute('content',(($lang_meta)),'\'','utf-8',false)?> />
<?php
 header('Content-language: '.$lang_meta)?></head>

<body>
<div>
<h1><?php
 echo htmlentities($title, ENT_QUOTES, 'utf-8')?></h1>

<p>This benchmark only takes timings of
</p>

<ul>
<li> starting up a template engine (load all necesseary .php files)</li>

<li> putting (quoted/verbatim) text into HTML like text with placeholders</li>

<li> assemlbing final page (inserting 3 $html strings into a &lt;html&gt;..&lt;/html&gt; template)</li>
</ul>

<p>Details see bottom. It does not care about caching which eg Smarty also provides. This can
be trivially be implemented for any template engine. The HTML Link
proofs that all runs produce the same result.


</p>

<div>
<h2> timing values:</h2>

<table>
<tr>
<th> chart nr</th>

<th> Engine</th>

<th> time_setup [ms]</th>

<th> time_case_1 [ms]</th>

<th> time_case_2 [ms]</th>

<th> proof</th>
</tr>
<?php
  $i = count($werte)-1?><?php
 foreach($werte as $wert){?>
<tr>
<td><?php
 echo htmlentities($i--, ENT_QUOTES, 'utf-8')?></td>

<td>
<a <?php
 echo HamlUtilities::renderAttribute('href',(($wert['url'])),'\'','utf-8',false)?>><?php
 echo htmlentities($wert['template_engine_name_version'], ENT_QUOTES, 'utf-8')?></a>
</td>

<td><?php
 echo htmlentities($wert['time_setup']*1000, ENT_QUOTES, 'utf-8')?></td>

<td><?php
 echo htmlentities($wert['time_case_1']*1000, ENT_QUOTES, 'utf-8')?></td>

<td><?php
 echo htmlentities($wert['time_case_2']*1000, ENT_QUOTES, 'utf-8')?></td>

<td>
<a <?php
 echo HamlUtilities::renderAttribute('href',(('?html_engine='.$wert['id'])),'\'','utf-8',false)?>> HTML</a>
</td>
</tr>
<?php
 }?></table>

<img <?php
 echo HamlUtilities::renderAttribute('src',(($graph_url)),'\'','utf-8',false)?> />
</div>

<h2> Details about the test</h2>

<p>4 timings are measured in multiple test runs. Each time a new PHP
executable process is launched so that GC timing etc affects all runs
in a similar fair way.
</p>

<ul>
<li> 1. php startup time</li>

<li>2. setup_time of the engine. The main file is required and all .php
files typicall used. strace is used to reveal which files PHP opens.
</li>

<li>3. time_case_1: 100 rows having 3 fields Surname, Lastname, age are rendered as table.
</li>

<li>4. time_case_2: the result of 3 (content), title, header,
description, navigation, fields are rendered simulating a simple typical page
</li>
</ul>
Want more details ? Read the

<a href='https://github.com/MarcWeber/benchmark-php-template-engines'> code on github.</a>

<br />
Eg start with the plain old

<a href='https://github.com/MarcWeber/benchmark-php-template-engines/blob/master/implementations/php/run.php'> PHP implementation</a>
</div>
</body>
</html>
