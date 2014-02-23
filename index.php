<?php

error_reporting(E_ALL);

require('lib/lil_scraper.php');

$title = 'Simple App Version Tracker';
$data_file = 'lib/data.json';
$json = json_decode(file_get_contents($data_file), true);

if ( ! $json ) {
	$json = array();
}

echo "
<!DOCTYPE html>
<head>
	<title>Simple App Version Tracker</title>
	<meta author='Chris Hart' />
	<link href='http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/css/bootstrap-combined.min.css' rel='stylesheet'>
	<link href='style/style.css' rel='stylesheet'>
	<script type='text/javascript' src='http://code.jquery.com/jquery-latest.min.js' /></script>
	<script type='text/javascript' src='js/main.js' /></script>
</head>
<body>

	<div id='title'><h2><a href='./'>$title</a></h2></div>

    <div class='navbar navbar-inverse'>
      <div class='navbar-inner nav-collapse' style='height: auto;'>
        <ul class='nav'>
          <li class='active'><a href='./'>./</a></li>
          <li><a id='add_button' href='#'>Add</a></li>
        </ul>
      </div>
    </div>
";

if ( isset($_POST['uri']) ) {

	$uri = $_POST['uri'];

	if ( isset($json[$uri]) ) {
		echo "<div id='alert'>That URI is already listed.</div>";
	} else {
		// Add it

		echo "<div id='alert'>";
		echo "<table class='table'>";
		echo "<tr><th>URI</th><th>App</th><th>Version</th></tr>";
		echo "<tr>";
		echo "<td>$uri</td>";

		$matches = scrape($uri);

		if ( $matches && $matches[1] ) {
			$data = explode( ' ', $matches[1] );
			echo "<td>" . $data[0] . "</td><td>" . $data[1] . "</td>";
			$array = array( 'app' => strtolower($data[0]), 'uri' => $uri, 'ver' => $data[1] );
			$json[$uri] = $array;
			file_put_contents($data_file, json_encode($json));
		} else {
			echo "<td colspan=2>Not able to determine App version.<br/>Are you sure it's installed?<td>";
		}

		echo "</tr>";
		echo "</table>";
		echo "</div>";

	}

}

echo "<div id='wrap' class='row-fluid'>
  <div id='content' class='span9 main offset1'>
	<table id='mainlist'>
		<thead>
			<tr>
				<th><span>URI</span></th>
				<th><span>App</span></th>
				<th><span>Version</span></th>
			</tr>
		</thead>
		<tbody>
	";

	foreach ( $json as $key => $arr ) {
		echo "<tr class=$key><td class=uri>
				<a id='$key' href='#' onClick='removeURI(\"$key\")'>X</a>" . $key . "</td>
				<td class=app>" . $arr['app'] . "</td>
				<td class=ver>" . $arr['ver'] . "</td>
			</tr>";
		echo "
			<script type='text/javascript'>
			$('#$key').on( 'click', function() {
				removeURI('$key');
			});
		</script>";
	}

	echo "
		</tbody>
	</table>
  </div>
</div>

	<div id='add_box'>
		<form action='' method=POST>
			<h3>Enter a Blog URI</h3>
			<input type='text' name='uri' id='uri' />
			<!-- <input type='hidden' name='submit_check' value=1 /> -->
			<input type='submit' name='check_submit' value='Add' />
		</form>
	</div>

</body>

<script type='text/javascript' src='js/jquery.tablesorter.min.js' /></script>
</html>
";

?>
