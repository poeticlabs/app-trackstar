// WP version tracking thingy
// by Archipoeta (a.k.a. Chris Hart
// 2014.02.21
//

// Apps to keep latest versions of:
var apps = [ 'wordpress', 'ghost' ];

var data_file = 'lib/data.json';
var vers_file = 'lib/latest.json';

$(document).ready( function() {

	//updateAppsLatest();
	checkVersions();

	$(document).mouseup(function (e) {
	    var container = $("#add_box");
	    if (!container.is(e.target) && container.has(e.target).length === 0) {
	        container.hide();
			$('#alert').hide();
	    }
	});

	$( document ).on( 'keydown', function ( e ) {
	    if ( e.keyCode === 27 ) { // ESC
	        $('#add_box').hide();
			$('#alert').hide();
	    }
	});

	$('#add_button').click( function() {
			$('#add_box').show();
			$('#alert').hide();
	});

	$(function(){
	  $('#mainlist').tablesorter(); 
	});

});

// methods

function checkVersions() {

		$.ajax({
		  type: 'POST',
		  dataType: 'json',
		  url: 'lib/lil_scraper.php',
		  data: { get_latest: 1 },
		  success: function( latest ) {

			$.ajax({
			  type: 'POST',
			  dataType: 'json',
			  url: 'lib/lil_scraper.php',
			  data: { callback: 1 },
			  success: function( data ) {

				for ( var key in data ) {
//				$.each( data, function(key) {
					var app = data[key].app;
					console.log ( latest );
					console.log ( data );

					if ( latest[ app ] != undefined ) {

					$('tr[class="' + data[key].uri + '"]').children('.app').html( data[key].app );

					if ( data[key].ver == latest[ app ].ver ) {
						$('tr[class="' + data[key].uri + '"]').children().css( 'background-color', '#eeffee' );
						$('tr[class="' + data[key].uri + '"]').children('.ver').html( data[key].ver );
					} else {
						$('tr[class="' + data[key].uri + '"]').children().css( 'background-color', '#ffeeee' );
						$('tr[class="' + data[key].uri + '"]').children('.ver').html( data[key].ver + ' => ' + latest[ data[key].app ].ver );
					}

					}

				}
//				});

			  } // success

			});

		 }

		});

}

function removeURI( uri ) {
	$.ajax({
	  type: 'POST',
	  dataType: 'json',
	  url: 'lib/lil_scraper.php',
	  data: { uri: uri, remove_uri: 1 },
	  success: function( data ) {
		location.reload();
	  }
	});
}

function extend(target) {
    var sources = [].slice.call(arguments, 1);
    sources.forEach(function (source) {
        for (var prop in source) {
            target[prop] = source[prop];
        }
    });
    return target;
}

function updateAppsLatest() {
	$.getJSON( vers_file, function(latest) {

		for ( var key in latest ) {

			$.ajax({
			  type: 'POST',
			  url: 'lib/lil_scraper.php',
			  data: { uri: latest[key].uri, get_latest: key },
			  success: function(data) {
				console.log( key, data);
				latest[key] = {};
				latest[key] = data;
			  }
			});

		}

	});

}
