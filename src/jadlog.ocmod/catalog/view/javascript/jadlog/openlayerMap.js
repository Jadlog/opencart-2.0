//var lonLatPadrao = [-48.4888055, -1.4580137]; //Belém
var lonLatPadrao = [-46.6329590, -23.5507309]; //JAD MATRIZ
var map;
var geolocation;
var map_popup_container_element;
var map_popup_content_element;
var map_popup_closer_element;
var map_popup_overlay;
var youAreHereLayer;
var distance;
var clusterSource;
var uniqPoints = {};
var iconeLojasPickup = 'image/data/jadlog/caixa1-small.png';
var escalaIconeLojasPickup = 1;
var iconeLojasFranquia = 'image/data/jadlog/caixa1-small.png';
var escalaIconeLojasFranquia = 1;
var corCluster = '#FF0E32'; //laranja = #ff7812
var icon_feature = [];

function initialize() {

    distance = document.getElementById('cluster_distance');

    map = new ol.Map({
        target: 'form_detalhe_mapa:mapa_detalhe_openlayer',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM({crossOrigin: null}),
            })
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat(lonLatPadrao),
            zoom: 4
        })
    });

    geolocation = new ol.Geolocation({
        // take the projection to use from the map's view
        projection: map.getView().getProjection(),
        tracking: true,
        trackingOptions: {
            enableHighAccuracy: true,
            maximumAge: 2000
        }
    });

    //icone - posicão usuário
    var iconStyle = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 12,
            snapToPixel: true,
            fill: new ol.style.Fill({
                color: '#FF7812'
            }),
            stroke: new ol.style.Stroke({
                color: '#F00',
                width: 3
            })
        }),
        text: new ol.style.Text({
            text: 'X',
            fill: new ol.style.Fill({
                color: '#F00'
            }),
            stroke: new ol.style.Stroke({
                color: '#000',
                width: 2
            })
        })
    });
    var iconFundo = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 16,
            snapToPixel: true,
            fill: new ol.style.Fill({
                color: '#000'
            }),
        })
    });
    var iconFrente = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 11,
            snapToPixel: true,
            fill: new ol.style.Fill({
                color: 'rgba(0,0,0,0)', // transparente
            }),
            stroke: new ol.style.Stroke({
                color: '#000',
                width: 2
            })

        })
    });
    var iconFeature = new ol.Feature({
        geometry: new ol.geom.Point(ol.proj.fromLonLat(lonLatPadrao)), //new ol.geom.Point(ol.proj.transform(lonLatPadrao, 'EPSG:4326', 'EPSG:3857'))
        description: 'Você está aqui.'
    });

    var iconSource = new ol.source.Vector({
        features: [iconFeature]
    });

    youAreHereLayer = new ol.layer.Vector({
        source: iconSource,
        style: [iconFundo, iconStyle, iconFrente],
        visible: false
    });
    map.addLayer(youAreHereLayer);

    // listen to changes in position
    geolocation.on('change', function (evt) {
        var pos = geolocation.getPosition();
        //map.getView().setCenter(pos);
        //map.getView().setZoom(12);
        iconFeature.getGeometry().setCoordinates(pos);
        youAreHereLayer.setVisible(true);
    });

    geolocation.on('error', function (evt) {
        youAreHereLayer.setVisible(false);
    });

    //map.getView().setCenter(ol.proj.fromLonLat([-46.65198269999996, -23.565004]));
    //geolocation.setTracking(true);

    //popup com informações
    /**
     * Elements that make up the popup.
     */
    map_popup_container_element = document.getElementById('map_popup');
    map_popup_content_element = document.getElementById('map_popup_content');
    map_popup_closer_element = document.getElementById('map_popup_closer');

    /**
     * Create an overlay to anchor the popup to the map.
     */
    map_popup_overlay = new ol.Overlay(/** @type {olx.OverlayOptions} */ ({
        element: map_popup_container_element,
        autoPan: true,
        autoPanAnimation: {
            duration: 250
        }
    }));

    /**
     * Add a click handler to hide the popup.
     * @return {boolean} Don't follow the href.
     */
    map_popup_closer_element.onclick = function () {
        hidePopup();
    };
    map.addOverlay(map_popup_overlay);

    // handle pointermove for popup
    map.on('pointermove', function (e) {
        //showPopup(e);
        map.getTargetElement().style.cursor = hit(e) ? 'pointer' : '';
    });

    // display popup on click too
    map.on('click', function (e) {
        //hidePopup();
        showPopup(e);
    });

    // display popup of first pudo after map loads
    map.once('postcompose', function(e) {
        showPopupFeature(icon_feature[0], icon_feature[0]);
    });
}

function marcaPontos(pontos, tipo, icon) {
    for (var i in pontos) {
        var lonLat = [parseFloat(pontos[i].LONGITUDE), parseFloat(pontos[i].LATITUDE)];
        while (uniqPoints[lonLat.toString()]) {
            lonLat = [lonLat[0], lonLat[1] + 0.0001]
        }
        uniqPoints[lonLat.toString()] = true;
        // se order = 1 centraliza o mapa
        if (parseInt(pontos[i].ORDER) === 1) {
            map.getView().setCenter(ol.proj.fromLonLat(lonLat));
            map.getView().setZoom(12);
        }
        var description;
        if (tipo === 'pudo') {
            description = "<b>" + pontos[i].NAME + "</b> (" + pontos[i].PUDO_ID + ")";
            description += "<br>" + pontos[i].ADDRESS1 + " " + pontos[i].STREETNUM;
            var complemento = pontos[i].ADDRESS2 + " " + pontos[i].ADDRESS3;
            complemento = jQuery.trim(complemento);
            if (complemento) {
                description += "<br>" + complemento
            }
            complemento = pontos[i].CITY + " " + pontos[i].ZIPCODE;
            complemento = jQuery.trim(complemento);
            if (complemento) {
                description += "<br>" + complemento
            }
            description += "<br>Horário de Funcionamento:<code>";
            var horarios = pontos[i].OPENING_HOURS;
            for (var j in horarios) {
                description += "<br>" + horarios[j];
            }
            description += "</code>";
        } else {
            description = pontos[i].DESCRIPTION;
        }
        icon_feature[i] = new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat(lonLat)), //new ol.geom.Point(ol.proj.transform(lonLatPadrao, 'EPSG:4326', 'EPSG:3857'))
            description: description
        });

        var icon_source = new ol.source.Vector({
            features: [icon_feature[i]]
        });

        var icon_layer = new ol.layer.Vector({
            source: icon_source,
            style: icon
        });
        //map.addLayer(icon_layer);
    }
    //cluster
    var distancia = 0;
    if (distance) {
        distancia = parseInt(distance.value, 10);
    }
    var source = new ol.source.Vector({
        features: icon_feature
    });
    clusterSource = new ol.source.Cluster({
        distance: distancia,
        source: source
    });
    var styleCache = {};
    var clusters = new ol.layer.Vector({
        source: clusterSource,
        style: function (feature) {
            var size = feature.get('features').length;
            var style = styleCache[size];
            if (!style) {
                if (size > 1) {
                    style = new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 15,
                            stroke: new ol.style.Stroke({
                                color: '#000'
                            }),
                            fill: new ol.style.Fill({
                                color: corCluster
                            })
                        }),
                        text: new ol.style.Text({
                            text: size.toString(),
                            fill: new ol.style.Fill({
                                color: '#fff'
                            }),
                            font: '18px sans-serif'
                        })
                    });
                } else {
                    style = icon
                }
                styleCache[size] = style;
            }
            return style;
        }
    });
    map.addLayer(clusters);
    if (distance) {
        distance.addEventListener('input', function () {
            clusterSource.setDistance(parseInt(distance.value, 10));
        });
    }
}

function marcaFranquias(franquias) {
    //icones das franquias
    var icon = new ol.style.Style({
        image: new ol.style.Icon({
            anchor: [0.5, 0],
            anchorXUnits: 'fraction',
            anchorYUnits: 'pixels',
            opacity: 1.0,
            scale: escalaIconeLojasFranquia,
            src: iconeLojasFranquia
        })
    });
    var icon_fundo = new ol.style.Style({
        image: new ol.style.Icon({
            anchor: [0.5, 0],
            anchorXUnits: 'fraction',
            anchorYUnits: 'pixels',
            opacity: 0.8,
            scale: escalaIconeLojasFranquia,
            src: iconeLojasFranquia
        })
    });

    var pontos = JSON.parse(franquias);
    marcaPontos(pontos, 'franquia', [icon]);
}

function marcaPudos() {
    //icones dos pudos
    var icon = new ol.style.Style({
        image: new ol.style.Icon({
            anchor: [0.5, 0],
            anchorXUnits: 'fraction',
            anchorYUnits: 'pixels',
            opacity: 1.0,
            scale: escalaIconeLojasPickup,
            src: iconeLojasPickup
        })
    });
    var icon_fundo = new ol.style.Style({
        image: new ol.style.Icon({
            anchor: [0.5, 0],
            anchorXUnits: 'fraction',
            anchorYUnits: 'pixels',
            opacity: 0.8,
            scale: escalaIconeLojasPickup,
            src: iconeLojasPickup
        })
    });

    var pontos = JSON.parse(document.getElementById("form_detalhe_mapa:hid_pudos").value);
    marcaPontos(pontos, 'pudo', [icon]);
}

function showPopupFeature(f, feature) {
    if (f) {
        var geometry = feature.getGeometry();
        var coord = geometry.getCoordinates();
        map_popup_overlay.setPosition(coord);
        map_popup_content_element.innerHTML = f.get("description");
    }
}

function showPopup(e) {
    var pixel = map.getEventPixel(e.originalEvent);
    if (hit(e)) {
        var feature = map.forEachFeatureAtPixel(pixel,
                function (feature, layer) {
                    return feature;
                });
        var f = feature;
        if (feature && feature.get("features")) {
            if (feature.get("features").length == 1) {
                f = feature.get("features")[0];
            } else {
                f = undefined;
            }
        }
        showPopupFeature(f, feature);
    }
}

function hidePopup() {
    map_popup_overlay.setPosition(undefined);
    map_popup_closer_element.blur();
    return false;

}

function hit(e) {
    var pixel = map.getEventPixel(e.originalEvent);
    return map.hasFeatureAtPixel(pixel);
}

