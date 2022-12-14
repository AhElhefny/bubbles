<script src="https://maps.google.com/maps/api/js?sensor=false&amp;libraries=drawing" type="text/javascript"></script>
<script src="{{asset('/assets/wicket/wicket.js')}}" type="text/javascript"></script>
<script src="{{asset('/assets/wicket/wicket-gmap3.js')}}" type="text/javascript"></script>
<script type="text/javascript">
    var app = (function () {
        return {
            features: [],
            /**
             * Clears the map contents.
             */
            clearMap: function () {
                var i;

                // Reset the remembered last string (so that we can clear the map,
                //  paste the same string, and see it again)
                document.getElementById('wkt').last = '';

                for (i in this.features) {
                    if (this.features.hasOwnProperty(i)) {
                        this.features[i].setMap(null);
                    }
                }
                this.features.length = 0;
            },
            /**
             * Clears the current contents of the textarea.
             */
            clearText: function () {
                document.getElementById('wkt').value = '';
            },
            /**
             * Maps the current contents of the textarea.
             * @return  {Object}    Some sort of geometry object
             */
            mapIt: function () {
                var el, obj, wkt;

                el = document.getElementById('wkt');
                wkt = new Wkt.Wkt();

                if (el.last === el.value) { // Remember the last string
                    return; // Do nothing if the WKT string hasn't changed
                } else {
                    el.last = el.value;
                }

                try { // Catch any malformed WKT strings
                    wkt.read(el.value);
                } catch (e1) {
                    try {
                        wkt.read(el.value.replace('\n', '').replace('\r', '').replace('\t', ''));
                    } catch (e2) {
                        if (e2.name === 'WKTError') {
                            alert('Wicket could not understand the WKT string you entered. Check that you have parentheses balanced, and try removing tabs and newline characters.');
                            return;
                        }
                    }
                }

                obj = wkt.toObject(this.gmap.defaults); // Make an object

                // Add listeners for overlay editing events
                if (!Wkt.isArray(obj) && wkt.type !== 'point') {
                    // New vertex is inserted
                    google.maps.event.addListener(obj.getPath(), 'insert_at', function (n) {
                        app.updateText();
                    });
                    // Existing vertex is removed (insertion is undone)
                    google.maps.event.addListener(obj.getPath(), 'remove_at', function (n) {
                        app.updateText();
                    });
                    // Existing vertex is moved (set elsewhere)
                    google.maps.event.addListener(obj.getPath(), 'set_at', function (n) {
                        app.updateText();
                    });
                } else {
                    if (obj.setEditable) {obj.setEditable(false);}
                }

                if (Wkt.isArray(obj)) { // Distinguish multigeometries (Arrays) from objects
                    for (i in obj) {
                        if (obj.hasOwnProperty(i) && !Wkt.isArray(obj[i])) {
                            obj[i].setMap(this.gmap);
                        }

                        if (wkt.type !== 'point') {
                            // New vertex is inserted
                            google.maps.event.addListener(obj[i].getPath(), 'insert_at', function (n) {
                                app.updateTextPart();
                            });
                            // Existing vertex is removed (insertion is undone)
                            google.maps.event.addListener(obj[i].getPath(), 'remove_at', function (n) {
                                app.updateTextPart();
                            });
                            // Existing vertex is moved (set elsewhere)
                            google.maps.event.addListener(obj[i].getPath(), 'set_at', function (n) {
                                app.updateTextPart();
                            });
                        }
                    }

                    this.features = this.features.concat(obj);
                } else {
                    obj.setMap(this.gmap); // Add it to the map
                    this.features.push(obj);
                }

                // Pan the map to the feature
                if (obj.getBounds !== undefined && typeof obj.getBounds === 'function') {
                    // For objects that have defined bounds or a way to get them
                    this.gmap.fitBounds(obj.getBounds());
                } else {
                    if (obj.getPath !== undefined && typeof obj.getPath === 'function') {
                        // For Polygons and Polylines
                        this.gmap.panTo(obj.getPath().getAt(0));
                    } else { // But points (Markers) are different
                        if (obj.getPosition !== undefined && typeof obj.getPosition === 'function') {
                            this.gmap.panTo(obj.getPosition());
                        }
                    }
                }

                return obj;
            },
            /**
             * Updates the textarea based on the first available feature.
             */
            updateText: function () {
                var wkt = new Wkt.Wkt();
                wkt.fromObject(this.features[0]);
                document.getElementById('wkt').value = wkt.write();
            },
            updateTextPart: function () {
                var i, w, v;

                w = new Wkt.Wkt(this.features[0]);

                i = 1;
                while (i < this.features.length) {
                    v = new Wkt.Wkt(this.features[i]);
                    w.merge(v);
                    i += 1;
                }

                document.getElementById('wkt').value = w.write();
            },
            /**
             * Formats the textarea contents for a URL.
             * @param   checked {Boolean}   The check state of the associated checkbox
             */
            urlify: function (checked) {
                var wkt = new Wkt.Wkt();
                wkt.read(document.getElementById('wkt').value);
                wkt.delimiter = (checked) ? '+' : ' ';
                document.getElementById('wkt').value = wkt.write();
                return wkt;
            },
            /**
             * Application entry point.
             * @return  {<google.maps.Map>} The Google Maps API instance
             */
            init: function () {
                var gmap;

                gmap = new google.maps.Map(document.getElementById('canvas'), {
                    center: new google.maps.LatLng(24.716096504469377, 46.601217550562104),
                    defaults: {
                        icon: 'red_dot.png',
                        shadow: 'dot_shadow.png',
                        editable: true,
                        strokeColor: '#990000',
                        fillColor: '#EEFFCC',
                        fillOpacity: 0.6
                    },
                    disableDefaultUI: true,
                    mapTypeControl: true,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapTypeControlOptions: {
                        position: google.maps.ControlPosition.TOP_LEFT,
                        style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                    },
                    panControl: false,
                    streetViewControl: false,
                    zoom: 5,
                    zoomControl: true,
                    zoomControlOptions: {
                        position: google.maps.ControlPosition.LEFT_TOP,
                        style: google.maps.ZoomControlStyle.SMALL
                    }
                });

                google.maps.event.addListener(gmap, 'tilesloaded', function () {
                    if (!this.loaded) {
                        this.loaded = true;
                        // NOTE: We start with a MULTIPOLYGON; these aren't easily deconstructed, so we won't set this object to be editable in this example
                        document.getElementById('wkt').value = '{{$item->polygon?\App\Classes\Polygon::arrayToWKT(json_decode($item->polygon)):'MULTIPOLYGON ()'}}';
                        app.mapIt();
                        // Define the LatLng coordinates for the polygon's path.

//Start custom
                        const triangleCoords = [
                            { lat: 25.774, lng: -80.19 },
                            { lat: 18.466, lng: -66.118 },
                            { lat: 32.321, lng: -64.757 },
                            { lat: 25.774, lng: -80.19 },
                        ];
                        // Construct the polygon.
                        const bermudaTriangle = new google.maps.Polygon({
                            paths: triangleCoords,
                            strokeColor: "#FF0000",
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: "blue",
                            fillOpacity: 0.35,
                        });

                        bermudaTriangle.setMap(gmap);

                        ///End custom
                    }
                });

                gmap.drawingManager = new google.maps.drawing.DrawingManager({
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER,
                        drawingModes: [
                            // google.maps.drawing.OverlayType.MARKER, //fixme::custom
                            // google.maps.drawing.OverlayType.POLYLINE, //fixme::custom
                            google.maps.drawing.OverlayType.POLYGON,
                            // google.maps.drawing.OverlayType.RECTANGLE //fixme::custom
                        ]
                    },
                    markerOptions: gmap.defaults,
                    polygonOptions: gmap.defaults,
                    polylineOptions: gmap.defaults,
                    rectangleOptions: gmap.defaults
                });
                gmap.drawingManager.setMap(gmap);

                google.maps.event.addListener(gmap.drawingManager, 'overlaycomplete', function (event) {
                    var wkt;

                    app.clearText();
                    app.clearMap();

                    // Set the drawing mode to "pan" (the hand) so users can immediately edit
                    this.setDrawingMode(null);

                    // Polygon drawn
                    if (event.type === google.maps.drawing.OverlayType.POLYGON || event.type === google.maps.drawing.OverlayType.POLYLINE) {
                        // New vertex is inserted
                        google.maps.event.addListener(event.overlay.getPath(), 'insert_at', function (n) {
                            app.updateText();
                        });

                        // Existing vertex is removed (insertion is undone)
                        google.maps.event.addListener(event.overlay.getPath(), 'remove_at', function (n) {
                            app.updateText();
                        });

                        // Existing vertex is moved (set elsewhere)
                        google.maps.event.addListener(event.overlay.getPath(), 'set_at', function (n) {
                            app.updateText();
                        });
                    } else if (event.type === google.maps.drawing.OverlayType.RECTANGLE) { // Rectangle drawn
                        // Listen for the 'bounds_changed' event and update the geometry
                        google.maps.event.addListener(event.overlay, 'bounds_changed', function () {
                            app.updateText();
                        });
                    }

                    app.features.push(event.overlay);
                    wkt = new Wkt.Wkt();
                    wkt.fromObject(event.overlay);
                    document.getElementById('wkt').value = wkt.write();
                });

                return gmap;
            }
        };
    }()); // Execute immediately

</script>
<style type="text/css">
    #canvas {width:100%;min-height: 500px;background:#AAA;border-left:1px solid #999;border-right:1px solid #999;}
    #canvas,
    #controls {float:left;}
    #controls {width:290px;height:100%;padding:0 0 0 10px;text-align:justify;background:transparent;position:relative;top:-80px;}
    #controls .text {margin:90px 0 0;}
    #controls a:hover,
    #controls a:active {text-decoration:underline;}
</style>
<body onload="app.gmap=app.init();">
<div id="ribbon">
        <div id="canvas">
        </div>
    <input type="hidden" name="polygons" id="wkt" value="{{}}">

{{--    <div id="controls">--}}
{{--            <div id="form">--}}
{{--                @php--}}
{{--                    $polygons = '';--}}
{{--                @endphp--}}
{{--                <label><input type="checkbox" name="urlify" id="urlify" onchange="app.urlify(this.checked);" />Format for URLs</label>--}}
{{--                <input type="submit" id="submit" value="Map It!" onclick="app.clearMap();app.mapIt();" />--}}
{{--                <input type="reset" id="reset" value="Clear Map" onclick="app.clearText();app.clearMap();" />--}}
{{--            </div>--}}
{{--        </div>--}}
</div>
</body>


