<!DOCTYPE html>
<html>
 <head>
  <title>Add a street</title>
  <script src="js/jquery.min.js"></script>
  <link rel="stylesheet" href="css/leaflet.css" />
  <script src="js/leaflet.js"></script>
 </head>
 <body>
  <div id="map" style="width: 600px; height: 400px"></div><br />
  <input type="button" onclick="drawStreet();" value="Draw a street" /> <input type="button" onclick="resetStreet();" value="Clear map" /><br />
  <p>To add a street point click on the map. To remove a street point click on it again.</p>
  <form action="addstreetdb.php" method="post">
   <h1>Add a new street</h1>
   <table cellpadding="5" cellspacing="0" border="0">
    <tbody>
     <tr align="left" valign="top">
      <td align="left" valign="top">Street name</td>
      <td align="left" valign="top"><input type="text" name="street" /></td>
     </tr>
     <tr align="left" valign="top">
      <td align="left" valign="top">Geographic locations</td>
      <td align="left" valign="top">
       <textarea id="geo" name="geo"></textarea>
       <br /><input type="button" onclick="getGeoPoints();" value="Collect points" />
      </td>
     </tr>
	<tr align="left" valign="top">
	  <td align="left" valign="top">Keywords</td>
	  <td align="left" valign="top"><textarea name="keywords"></textarea></td>
	</tr>
     <tr align="left" valign="top">
      <td align="left" valign="top"></td>
      <td align="left" valign="top"><input type="submit" value="Save"></td>
     </tr>
    </tbody>
   </table>
  </form>
  <script>
   var map = L.map( 'map' ).setView( [51.505, -0.09], 13 );
   var polyLine;
   var draggableStreetMarkers = new Array();

  L.tileLayer( 'https://api.mapbox.com/styles/v1/mapbox/streets-v10/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWVnYTYzODIiLCJhIjoiY2ozbXpsZHgxMDAzNjJxbndweDQ4am5mZyJ9.uHEjtQhnIuva7f6pAfrdTw',, {
    maxZoom: 18,
    attribution: 'Map data &copy; <a href="http://openstreetmap.org/"> OpenStreetMap </a> contributors, ' +
    '<a href="http://creativecommons.org/"> CC-BY-SA </a>, ' +
    'Imagery � <a href="http://mapbox.com"> Mapbox </a>',
    id: 'examples.map-i875mjb7'
   }).addTo(map);
   
   function resetStreet() {
    if(polyLine != null) {
     map.removeLayer( polyLine );
    }
    for(i=0; i< draggableStreetMarkers.length; i++) {
     map.removeLayer( draggableStreetMarkers[i] );
    }
    draggableStreetMarkers = new Array();
   }
   
   function addMarkerStreetPoint( latLng ) {
    var streetMarker = L.marker( [latLng.lat, latLng.lng], { draggable: true, zIndexOffset: 900}).addTo(map);

    streetMarker.arrayId = draggableStreetMarkers.length;

    streetMarker.on('click', function() {
     map.removeLayer( draggableStreetMarkers[ this.arrayId ]);
     draggableStreetMarkers[ this.arrayId ] = "";
    });

    draggableStreetMarkers.push( streetMarker );
   }
   
   function drawStreet() {
    if(polyLine != null) {
     map.removeLayer( polyLine );
    }

    var latLngStreets = new Array();

    for(i=0; i < draggableStreetMarkers.length; i++) {
     if(draggableStreetMarkers[i]!="") {
      latLngStreets.push( L.latLng( draggableStreetMarkers[ i ].getLatLng().lat, draggableStreetMarkers[ i ].getLatLng().lng));
     }
    }

    if(latLngStreets.length > 1) {
     // create a red polyline from an array of LatLng points
     polyLine = L.polyline( latLngStreets, {color: 'red'} ).addTo(map);
    }

    if(polyLine != null) {
     // zoom the map to the polyline
     map.fitBounds( polyLine.getBounds() );
    }
   }
   
   function getGeoPoints() {
    var points = new Array();
    for(var i=0; i < draggableStreetMarkers.length; i++) {
     if(draggableStreetMarkers[i] != "") {
      points[i] =  draggableStreetMarkers[ i ].getLatLng().lng + "," + draggableStreetMarkers[ i ].getLatLng().lat;
     }
    }
    $('#geo').val(points.join(','));
   }
   
   $( document ).ready(function() {
    map.on('click', function(e) {
     addMarkerStreetPoint( e.latlng );
    });
   });
  </script>
 </body>
</html>