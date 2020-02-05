<?php
$f = $_GET[ 'f' ];
if ( !$f ) {
	header( "Location: /" );
}
$file = __DIR__ . "/../storage/public/gcode/{$f}";
if ( !is_file( $file ) ) {
	header( "Location: /" );
}

$preview_file = __DIR__ . "/../storage/public/gcode/{$f}.js";
if ( !is_file( $preview_file ) ) {
	touch( $preview_file );
	$r = fopen( $file, "r" );
	$w = fopen( $preview_file, "w" );
	fwrite( $w, "var sample = `\n" );
	while ( ( $line = fgets( $r ) ) !== false ) {
		fwrite( $w, $line );
	}
	fwrite( $w, "`\n" );
	fclose( $r );
	fclose( $w );
}

$preview_file_url = "/storage/public/gcode/{$f}.js";
?>

<title>GCode Previewer</title>
<link rel="stylesheet" href="/gcode-preview/style.css" />

<header class="controls">
	<label>Render layers:&nbsp;<input type="range" min="0" value="0" id="layers" /></label>
	<label>Scale:&nbsp;<input type="range" min="1" value="3" max="10" step="0.1" id="scale" /></label>
	<label>Rotate:&nbsp;<input type="range" min="0" value="0" max="360"  id="rotation" /></label>
	<button id="toggle-animation">toggle animation</button>
	<label>Color zones:&nbsp;<input type="checkbox" id="zone-colors" value="test" /></label>
</header>
<div id="renderer" class="gcode-previewer"></div>
<script src="/gcode-preview/gcode-preview.js"></script>

<script src="<?php echo $preview_file_url ?>"></script>
<script src="/gcode-preview/demo.js"></script>
<script>
	const gcodeDemo = initDemo();
	gcodeDemo.processGCode(sample);
	updateUI();
</script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
