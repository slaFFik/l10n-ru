// Adjust body position to Admin Bar
window.onload = window.onresize = function() {
	var wpabar = document.getElementById('wpabar');
	var bodyPaddingTop = (wpabar ? wpabar.clientHeight : 0) + 'px';
	if ( document.createStyleSheet ) {
		var styleSheet = document.styleSheets[document.styleSheets.length - 1];
		styleSheet.addRule('body', 'padding-top: ' + bodyPaddingTop + ' !important;');
	} else {
		document.body.style.setProperty('padding-top', bodyPaddingTop, 'important');
	}
}
