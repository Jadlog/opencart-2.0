<html>
    <script type="text/javascript" src="catalog/view/javascript/jquery/jquery-2.1.1.min.js"></script>
    <link rel="stylesheet" href="https://openlayers.org/en/v4.4.1/css/ol.css" type="text/css"/>
    <script src="https://openlayers.org/en/v4.4.1/build/ol.js" type="text/javascript"></script>
    <script src="catalog/view/javascript/jadlog/openlayerMap.js" type="text/javascript"></script>
    <link type="text/css" href="catalog/view/javascript/jadlog/jadlog.css" rel="stylesheet" media="screen" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <div id="map_popup" class="ol-popup">
        <a href="#" id="map_popup_closer" class="ol-popup-closer fa fa-close"></a>
        <div id="map_popup_content"></div>
    </div>
    <div class="container">
        <div id="form_detalhe_mapa" name="form_detalhe_mapa">
            <input type="hidden" name="form_detalhe_mapa" value="form_detalhe_mapa" />
            <input id="form_detalhe_mapa:hid_pudos"
                   type="hidden"
                   name="form_detalhe_mapa:hid_pudos"
                   value="<?php echo(htmlentities($data['pudo'])); ?>"
            />
            <div id="form_detalhe_mapa:imprimir_mapa_detalhe_openlayer">
                <div id="form_detalhe_mapa:mapa_detalhe_openlayer" data-widget="mapa_detalhe_openlayer">
                    <div id="form_detalhe_mapa:mapa_detalhe_openlayer_content">
                    </div>
                </div>
            </div>
            <!--div style="margin-top: 15px;">
                <label>Distância agregamento</label>
                <input id="cluster_distance" type="range" min="0" max="100" step="1" value="40" />
            </div-->
        </div>
    </div>
    <script>
        function chamada() {
            initialize();
            marcaPudos();
        }
        chamada();
    </script>
</html>