<?php
/**
 * DPD France v5.2.0 shipping module for OpenCart 2.0
 *
 * @category   DPDFrance
 * @package    DPDFrance_Shipping
 * @author     DPD France S.A.S. <support.ecommerce@dpd.fr>
 * @copyright  2016 DPD France S.A.S., société par actions simplifiée, au capital de 18.500.000 euros, dont le siège social est situé 9 Rue Maurice Mallet - 92130 ISSY LES MOULINEAUX, immatriculée au registre du commerce et des sociétés de Paris sous le numéro 444 420 830
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ModelShippingJadloglista extends Model {
    public function getProductDimensions($product_id) {
        $query = $this->db->query("SELECT length, width, height, weight FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
        return $query->rows[0];
    }
    public function getCustomer($customer_id){
        $query = $this->db->query("SELECT cpf_or_cnpj FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
        return $query->rows[0];
    }
    public function getPrecoFrete($zipcodeFrom, $zipcodeTo, $vlDec, $peso)
    {
        $serviceurlfrete = $this->config->get('jadloglista_frete_service_url');
        $usuariofrete = $this->config->get('jadloglista_frete_user');
        $passwordfrete = $this->config->get('jadloglista_frete_password');
        $url = $serviceurlfrete.'&vModalidade=40&Password='.$passwordfrete.'&vSeguro=N&vVlDec=' . $vlDec . '&vVlColeta=&vCepOrig=' . str_replace('-', '', $zipcodeFrom) . '&vCepDest=' . str_replace('-', '', $zipcodeTo) . '&vPeso=' . $peso . '&vFrap=N&vEntrega=D&vCnpj=' . $usuariofrete;
        $this->log->write("frete url: ".$url);
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $str = curl_exec($curl);

        curl_close($curl);

        $this->log->write("frete resposta: " . $str);
        $sl1 = '&lt;Retorno&gt;';
        $sl1l = strlen($sl1);
        $pos1 = strrpos($str, $sl1);
        $pos2 = strrpos($str, '&lt;/Retorno&gt;');
        if ($pos1 == false || $pos2 == false) {
            return false;
        }
        $preco = substr($str, $pos1 + $sl1l, $pos2 - $pos1 - $sl1l);
        return $preco;
    }
    public function getMYPUDOListTest() {
        return '<RESPONSE quality="1">
  <REQUEST_ID>1234</REQUEST_ID>
  <PUDO_ITEMS>
    <PUDO_ITEM active="true" overloaded="false">
      <PUDO_ID>BR10355</PUDO_ID>
      <ORDER>1</ORDER>
      <DISTANCE>997</DISTANCE>
      <PUDO_TYPE>100</PUDO_TYPE>
      <PUDO_TYPE_INFOS />
      <NAME>POWER GAMES</NAME>
      <LANGUAGE>PT</LANGUAGE>
      <STREETNUM>2021</STREETNUM>
      <ADDRESS1>AV SILVA BUENO</ADDRESS1>
      <ADDRESS2>
      </ADDRESS2>
      <ADDRESS3>IPIRANGA</ADDRESS3>
      <LOCATION_HINT>
      </LOCATION_HINT>
      <ZIPCODE>04208-052</ZIPCODE>
      <CITY>SÃO PAULO</CITY>
      <COUNTRY>BRA</COUNTRY>
      <LONGITUDE>-46.60075320</LONGITUDE>
      <LATITUDE>-23.59634680</LATITUDE>
      <HANDICAPES>False</HANDICAPES>
      <PARKING>False</PARKING>
      <MAP_URL>http://www.chronopost.fr/transport-express/webdav/site/chronov4/groups/administrators/public/Chronomaps/print-result.html?request=print&amp;adresse1=2021+AV+SILVA+BUENO&amp;codePostal=04208-052&amp;localite=S%c3%83O+PAULO&amp;nomEnseigne=POWER+GAMES&amp;horairesOuvertureLundi=09%3a30-12%3a0012%3a00-18%3a30&amp;horairesOuvertureMardi=09%3a30-12%3a0012%3a00-18%3a30&amp;horairesOuvertureMercredi=09%3a30-12%3a0012%3a00-18%3a30&amp;horairesOuvertureJeudi=09%3a30-12%3a0012%3a00-18%3a30&amp;horairesOuvertureVendredi=09%3a30-12%3a0012%3a00-18%3a30&amp;horairesOuvertureSamedi=09%3a30-12%3a0012%3a00-18%3a30&amp;horairesOuvertureDimanche=&amp;identifiantChronopostPointA2PAS=113307&amp;rtype=chronorelais&amp;icnname=ac&amp;lat=-23.59634680&amp;lng=-46.60075320&amp;sw-form-type-point=opt_chrlas&amp;is_print_direction=false&amp;from_addr=&amp;to_addr</MAP_URL>
      <AVAILABLE>full</AVAILABLE>
      <OPENING_HOURS_ITEMS>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>09:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>09:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>09:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>09:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>09:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>09:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:30</END_TM>
        </OPENING_HOURS_ITEM>
      </OPENING_HOURS_ITEMS>
      <HOLIDAY_ITEMS />
    </PUDO_ITEM>
    <PUDO_ITEM active="true" overloaded="false">
      <PUDO_ID>BR10404</PUDO_ID>
      <ORDER>2</ORDER>
      <DISTANCE>4208</DISTANCE>
      <PUDO_TYPE>100</PUDO_TYPE>
      <PUDO_TYPE_INFOS />
      <NAME>OFICINA DE COSTURA</NAME>
      <LANGUAGE>PT</LANGUAGE>
      <STREETNUM>1023</STREETNUM>
      <ADDRESS1>RUA NATAL </ADDRESS1>
      <ADDRESS2>LOJA 16</ADDRESS2>
      <ADDRESS3>VILA BERTIOGA</ADDRESS3>
      <LOCATION_HINT>
      </LOCATION_HINT>
      <ZIPCODE>03186-030</ZIPCODE>
      <CITY>SÃO PAULO</CITY>
      <COUNTRY>BRA</COUNTRY>
      <LONGITUDE>-46.58239420</LONGITUDE>
      <LATITUDE>-23.56822860</LATITUDE>
      <HANDICAPES>False</HANDICAPES>
      <PARKING>False</PARKING>
      <MAP_URL>http://www.chronopost.fr/transport-express/webdav/site/chronov4/groups/administrators/public/Chronomaps/print-result.html?request=print&amp;adresse1=1023+RUA+NATAL&amp;codePostal=03186-030&amp;localite=S%c3%83O+PAULO&amp;nomEnseigne=OFICINA+DE+COSTURA+&amp;horairesOuvertureLundi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureMardi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureMercredi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureJeudi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureVendredi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureSamedi=08%3a00-12%3a0012%3a00-15%3a00&amp;horairesOuvertureDimanche=&amp;identifiantChronopostPointA2PAS=113557&amp;rtype=chronorelais&amp;icnname=ac&amp;lat=-23.56822860&amp;lng=-46.58239420&amp;sw-form-type-point=opt_chrlas&amp;is_print_direction=false&amp;from_addr=&amp;to_addr</MAP_URL>
      <AVAILABLE>full</AVAILABLE>
      <OPENING_HOURS_ITEMS>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>15:00</END_TM>
        </OPENING_HOURS_ITEM>
      </OPENING_HOURS_ITEMS>
      <HOLIDAY_ITEMS />
    </PUDO_ITEM>
    <PUDO_ITEM active="true" overloaded="false">
      <PUDO_ID>BR10400</PUDO_ID>
      <ORDER>3</ORDER>
      <DISTANCE>4967</DISTANCE>
      <PUDO_TYPE>100</PUDO_TYPE>
      <PUDO_TYPE_INFOS />
      <NAME>ITAFRAN SOUND</NAME>
      <LANGUAGE>PT</LANGUAGE>
      <STREETNUM>539</STREETNUM>
      <ADDRESS1>AV, DOUTOR HUGO BEOLCHI</ADDRESS1>
      <ADDRESS2>
      </ADDRESS2>
      <ADDRESS3>VILA GUARANI</ADDRESS3>
      <LOCATION_HINT>
      </LOCATION_HINT>
      <ZIPCODE>04310-030</ZIPCODE>
      <CITY>SÃO PAULO</CITY>
      <COUNTRY>BRA</COUNTRY>
      <LONGITUDE>-46.64116370</LONGITUDE>
      <LATITUDE>-23.63068430</LATITUDE>
      <HANDICAPES>True</HANDICAPES>
      <PARKING>False</PARKING>
      <MAP_URL>http://www.chronopost.fr/transport-express/webdav/site/chronov4/groups/administrators/public/Chronomaps/print-result.html?request=print&amp;adresse1=539+AV%2c+DOUTOR+HUGO+BEOLCHI&amp;codePostal=04310-030&amp;localite=S%c3%83O+PAULO&amp;nomEnseigne=ITAFRAN+SOUND&amp;horairesOuvertureLundi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureMardi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureMercredi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureJeudi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureVendredi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureSamedi=08%3a00-12%3a00&amp;horairesOuvertureDimanche=&amp;identifiantChronopostPointA2PAS=113553&amp;rtype=chronorelais&amp;icnname=ac&amp;lat=-23.63068430&amp;lng=-46.64116370&amp;sw-form-type-point=opt_chrlas&amp;is_print_direction=false&amp;from_addr=&amp;to_addr</MAP_URL>
      <AVAILABLE>full</AVAILABLE>
      <OPENING_HOURS_ITEMS>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
      </OPENING_HOURS_ITEMS>
      <HOLIDAY_ITEMS />
    </PUDO_ITEM>
    <PUDO_ITEM active="true" overloaded="false">
      <PUDO_ID>BR10200</PUDO_ID>
      <ORDER>4</ORDER>
      <DISTANCE>9937</DISTANCE>
      <PUDO_TYPE>100</PUDO_TYPE>
      <PUDO_TYPE_INFOS />
      <NAME>SANCOMP</NAME>
      <LANGUAGE>PT</LANGUAGE>
      <STREETNUM>223</STREETNUM>
      <ADDRESS1>AV.  GENERAL PEDRO LEON SCHNEIDER</ADDRESS1>
      <ADDRESS2>
      </ADDRESS2>
      <ADDRESS3>SANTANA</ADDRESS3>
      <LOCATION_HINT>
      </LOCATION_HINT>
      <ZIPCODE>02012-100</ZIPCODE>
      <CITY>SÃO PAULO</CITY>
      <COUNTRY>BRA</COUNTRY>
      <LONGITUDE>-46.62860720</LONGITUDE>
      <LATITUDE>-23.50811010</LATITUDE>
      <HANDICAPES>False</HANDICAPES>
      <PARKING>False</PARKING>
      <MAP_URL>http://www.chronopost.fr/transport-express/webdav/site/chronov4/groups/administrators/public/Chronomaps/print-result.html?request=print&amp;adresse1=223+AV.++GENERAL+PEDRO+LEON+SCHNEIDER&amp;codePostal=02012-100&amp;localite=S%c3%83O+PAULO&amp;nomEnseigne=SANCOMP&amp;horairesOuvertureLundi=08%3a30-12%3a0012%3a00-17%3a30&amp;horairesOuvertureMardi=08%3a30-12%3a0012%3a00-17%3a30&amp;horairesOuvertureMercredi=08%3a30-12%3a0012%3a00-17%3a30&amp;horairesOuvertureJeudi=08%3a30-12%3a0012%3a00-17%3a30&amp;horairesOuvertureVendredi=08%3a30-12%3a0012%3a00-17%3a30&amp;horairesOuvertureSamedi=08%3a30-12%3a30&amp;horairesOuvertureDimanche=&amp;identifiantChronopostPointA2PAS=112153&amp;rtype=chronorelais&amp;icnname=ac&amp;lat=-23.50811010&amp;lng=-46.62860720&amp;sw-form-type-point=opt_chrlas&amp;is_print_direction=false&amp;from_addr=&amp;to_addr</MAP_URL>
      <AVAILABLE>full</AVAILABLE>
      <OPENING_HOURS_ITEMS>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>08:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>17:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>08:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>17:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>08:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>17:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>08:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>17:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>08:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>17:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>08:30</START_TM>
          <END_TM>12:30</END_TM>
        </OPENING_HOURS_ITEM>
      </OPENING_HOURS_ITEMS>
      <HOLIDAY_ITEMS />
    </PUDO_ITEM>
    <PUDO_ITEM active="true" overloaded="false">
      <PUDO_ID>BR10263</PUDO_ID>
      <ORDER>5</ORDER>
      <DISTANCE>10767</DISTANCE>
      <PUDO_TYPE>100</PUDO_TYPE>
      <PUDO_TYPE_INFOS />
      <NAME>ALLPE</NAME>
      <LANGUAGE>PT</LANGUAGE>
      <STREETNUM>102</STREETNUM>
      <ADDRESS1>RUA ALFREDO PUJOL</ADDRESS1>
      <ADDRESS2>
      </ADDRESS2>
      <ADDRESS3>SANTANA</ADDRESS3>
      <LOCATION_HINT>
      </LOCATION_HINT>
      <ZIPCODE>02017-000</ZIPCODE>
      <CITY>SÃO PAULO</CITY>
      <COUNTRY>BRA</COUNTRY>
      <LONGITUDE>-46.62680320</LONGITUDE>
      <LATITUDE>-23.500259</LATITUDE>
      <HANDICAPES>False</HANDICAPES>
      <PARKING>False</PARKING>
      <MAP_URL>http://www.chronopost.fr/transport-express/webdav/site/chronov4/groups/administrators/public/Chronomaps/print-result.html?request=print&amp;adresse1=102+RUA+ALFREDO+PUJOL&amp;codePostal=02017-000&amp;localite=S%c3%83O+PAULO&amp;nomEnseigne=ALLPE&amp;horairesOuvertureLundi=10%3a00-12%3a0012%3a00-18%3a00&amp;horairesOuvertureMardi=10%3a00-12%3a0012%3a00-18%3a00&amp;horairesOuvertureMercredi=10%3a00-12%3a0012%3a00-18%3a00&amp;horairesOuvertureJeudi=10%3a00-12%3a0012%3a00-18%3a00&amp;horairesOuvertureVendredi=10%3a00-12%3a0012%3a00-18%3a00&amp;horairesOuvertureSamedi=10%3a00-13%3a00&amp;horairesOuvertureDimanche=&amp;identifiantChronopostPointA2PAS=112701&amp;rtype=chronorelais&amp;icnname=ac&amp;lat=-23.500259&amp;lng=-46.62680320&amp;sw-form-type-point=opt_chrlas&amp;is_print_direction=false&amp;from_addr=&amp;to_addr</MAP_URL>
      <AVAILABLE>full</AVAILABLE>
      <OPENING_HOURS_ITEMS>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>10:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>10:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>10:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>10:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>10:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>10:00</START_TM>
          <END_TM>13:00</END_TM>
        </OPENING_HOURS_ITEM>
      </OPENING_HOURS_ITEMS>
      <HOLIDAY_ITEMS />
    </PUDO_ITEM>
    <PUDO_ITEM active="true" overloaded="false">
      <PUDO_ID>BR10199</PUDO_ID>
      <ORDER>6</ORDER>
      <DISTANCE>11282</DISTANCE>
      <PUDO_TYPE>100</PUDO_TYPE>
      <PUDO_TYPE_INFOS />
      <NAME>NOSSA CARA KIDS</NAME>
      <LANGUAGE>PT</LANGUAGE>
      <STREETNUM>259</STREETNUM>
      <ADDRESS1>AVENIDA BARUEL</ADDRESS1>
      <ADDRESS2>LETRA A</ADDRESS2>
      <ADDRESS3>VILA BARUEL</ADDRESS3>
      <LOCATION_HINT>
      </LOCATION_HINT>
      <ZIPCODE>02522-000</ZIPCODE>
      <CITY>SÃO PAULO</CITY>
      <COUNTRY>BRA</COUNTRY>
      <LONGITUDE>-46.65697280</LONGITUDE>
      <LATITUDE>-23.50382830</LATITUDE>
      <HANDICAPES>True</HANDICAPES>
      <PARKING>False</PARKING>
      <MAP_URL>http://www.chronopost.fr/transport-express/webdav/site/chronov4/groups/administrators/public/Chronomaps/print-result.html?request=print&amp;adresse1=259+AVENIDA+BARUEL&amp;codePostal=02522-000&amp;localite=S%c3%83O+PAULO&amp;nomEnseigne=NOSSA+CARA+KIDS&amp;horairesOuvertureLundi=09%3a30-12%3a0012%3a00-19%3a00&amp;horairesOuvertureMardi=09%3a30-12%3a0012%3a00-19%3a00&amp;horairesOuvertureMercredi=09%3a30-12%3a0012%3a00-19%3a00&amp;horairesOuvertureJeudi=09%3a30-12%3a0012%3a00-19%3a00&amp;horairesOuvertureVendredi=09%3a30-12%3a0012%3a00-19%3a00&amp;horairesOuvertureSamedi=09%3a30-12%3a0012%3a00-19%3a00&amp;horairesOuvertureDimanche=&amp;identifiantChronopostPointA2PAS=112152&amp;rtype=chronorelais&amp;icnname=ac&amp;lat=-23.50382830&amp;lng=-46.65697280&amp;sw-form-type-point=opt_chrlas&amp;is_print_direction=false&amp;from_addr=&amp;to_addr</MAP_URL>
      <AVAILABLE>full</AVAILABLE>
      <OPENING_HOURS_ITEMS>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>09:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>09:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>09:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>09:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>09:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>09:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
      </OPENING_HOURS_ITEMS>
      <HOLIDAY_ITEMS />
    </PUDO_ITEM>
    <PUDO_ITEM active="true" overloaded="false">
      <PUDO_ID>BR10124</PUDO_ID>
      <ORDER>7</ORDER>
      <DISTANCE>11370</DISTANCE>
      <PUDO_TYPE>100</PUDO_TYPE>
      <PUDO_TYPE_INFOS />
      <NAME>Ciclo Vila Isa</NAME>
      <LANGUAGE>PT</LANGUAGE>
      <STREETNUM>1645</STREETNUM>
      <ADDRESS1>Avenida Nossa Senhora do Sabara</ADDRESS1>
      <ADDRESS2>
      </ADDRESS2>
      <ADDRESS3>Vila Isa</ADDRESS3>
      <LOCATION_HINT>
      </LOCATION_HINT>
      <ZIPCODE>04685-004</ZIPCODE>
      <CITY>SÃO PAULO</CITY>
      <COUNTRY>BRA</COUNTRY>
      <LONGITUDE>-46.68973050</LONGITUDE>
      <LATITUDE>-23.66797440</LATITUDE>
      <HANDICAPES>True</HANDICAPES>
      <PARKING>False</PARKING>
      <MAP_URL>http://www.chronopost.fr/transport-express/webdav/site/chronov4/groups/administrators/public/Chronomaps/print-result.html?request=print&amp;adresse1=1645+Avenida+Nossa+Senhora+do+Sabara&amp;codePostal=04685-004&amp;localite=S%c3%83O+PAULO&amp;nomEnseigne=Ciclo+Vila+Isa&amp;horairesOuvertureLundi=10%3a00-12%3a0012%3a00-17%3a00&amp;horairesOuvertureMardi=10%3a00-12%3a0012%3a00-17%3a00&amp;horairesOuvertureMercredi=10%3a00-12%3a0012%3a00-17%3a00&amp;horairesOuvertureJeudi=10%3a00-12%3a0012%3a00-17%3a00&amp;horairesOuvertureVendredi=10%3a00-12%3a0012%3a00-17%3a00&amp;horairesOuvertureSamedi=&amp;horairesOuvertureDimanche=&amp;identifiantChronopostPointA2PAS=111645&amp;rtype=chronorelais&amp;icnname=ac&amp;lat=-23.66797440&amp;lng=-46.68973050&amp;sw-form-type-point=opt_chrlas&amp;is_print_direction=false&amp;from_addr=&amp;to_addr</MAP_URL>
      <AVAILABLE>full</AVAILABLE>
      <OPENING_HOURS_ITEMS>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>10:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>17:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>10:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>17:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>10:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>17:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>10:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>17:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>10:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>17:00</END_TM>
        </OPENING_HOURS_ITEM>
      </OPENING_HOURS_ITEMS>
      <HOLIDAY_ITEMS />
    </PUDO_ITEM>
    <PUDO_ITEM active="true" overloaded="false">
      <PUDO_ID>BR10045</PUDO_ID>
      <ORDER>8</ORDER>
      <DISTANCE>11383</DISTANCE>
      <PUDO_TYPE>100</PUDO_TYPE>
      <PUDO_TYPE_INFOS />
      <NAME>PLANET ÁGUAS</NAME>
      <LANGUAGE>PT</LANGUAGE>
      <STREETNUM>373</STREETNUM>
      <ADDRESS1>RUA MARCELINA</ADDRESS1>
      <ADDRESS2>
      </ADDRESS2>
      <ADDRESS3>VILA ROMANA</ADDRESS3>
      <LOCATION_HINT>
      </LOCATION_HINT>
      <ZIPCODE>05044-010</ZIPCODE>
      <CITY>SÃO PAULO</CITY>
      <COUNTRY>BRA</COUNTRY>
      <LONGITUDE>-46.69633880</LONGITUDE>
      <LATITUDE>-23.53040420</LATITUDE>
      <HANDICAPES>False</HANDICAPES>
      <PARKING>False</PARKING>
      <MAP_URL>http://www.chronopost.fr/transport-express/webdav/site/chronov4/groups/administrators/public/Chronomaps/print-result.html?request=print&amp;adresse1=373+RUA+MARCELINA&amp;codePostal=05044-010&amp;localite=S%c3%83O+PAULO&amp;nomEnseigne=PLANET+%c3%81GUAS&amp;horairesOuvertureLundi=09%3a00-12%3a0012%3a00-18%3a00&amp;horairesOuvertureMardi=09%3a00-12%3a0012%3a00-18%3a00&amp;horairesOuvertureMercredi=09%3a00-12%3a0012%3a00-18%3a00&amp;horairesOuvertureJeudi=09%3a00-12%3a0012%3a00-18%3a00&amp;horairesOuvertureVendredi=09%3a00-12%3a0012%3a00-18%3a00&amp;horairesOuvertureSamedi=09%3a00-12%3a0012%3a00-15%3a00&amp;horairesOuvertureDimanche=&amp;identifiantChronopostPointA2PAS=111500&amp;rtype=chronorelais&amp;icnname=ac&amp;lat=-23.53040420&amp;lng=-46.69633880&amp;sw-form-type-point=opt_chrlas&amp;is_print_direction=false&amp;from_addr=&amp;to_addr</MAP_URL>
      <AVAILABLE>full</AVAILABLE>
      <OPENING_HOURS_ITEMS>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>09:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>09:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>09:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>09:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>09:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>09:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>15:00</END_TM>
        </OPENING_HOURS_ITEM>
      </OPENING_HOURS_ITEMS>
      <HOLIDAY_ITEMS />
    </PUDO_ITEM>
    <PUDO_ITEM active="true" overloaded="false">
      <PUDO_ID>BR10197</PUDO_ID>
      <ORDER>9</ORDER>
      <DISTANCE>12009</DISTANCE>
      <PUDO_TYPE>100</PUDO_TYPE>
      <PUDO_TYPE_INFOS />
      <NAME>E LINK</NAME>
      <LANGUAGE>PT</LANGUAGE>
      <STREETNUM>1947</STREETNUM>
      <ADDRESS1>RUA DOUTOR ZUQUIM</ADDRESS1>
      <ADDRESS2>
      </ADDRESS2>
      <ADDRESS3>SANTANA</ADDRESS3>
      <LOCATION_HINT>
      </LOCATION_HINT>
      <ZIPCODE>02035-012</ZIPCODE>
      <CITY>SÃO PAULO</CITY>
      <COUNTRY>BRA</COUNTRY>
      <LONGITUDE>-46.62647760</LONGITUDE>
      <LATITUDE>-23.48892330</LATITUDE>
      <HANDICAPES>True</HANDICAPES>
      <PARKING>False</PARKING>
      <MAP_URL>http://www.chronopost.fr/transport-express/webdav/site/chronov4/groups/administrators/public/Chronomaps/print-result.html?request=print&amp;adresse1=1947+RUA+DOUTOR+ZUQUIM&amp;codePostal=02035-012&amp;localite=S%c3%83O+PAULO&amp;nomEnseigne=E+LINK&amp;horairesOuvertureLundi=08%3a30-12%3a0012%3a00-18%3a30&amp;horairesOuvertureMardi=08%3a30-12%3a0012%3a00-18%3a30&amp;horairesOuvertureMercredi=08%3a30-12%3a0012%3a00-18%3a30&amp;horairesOuvertureJeudi=08%3a30-12%3a0012%3a00-18%3a30&amp;horairesOuvertureVendredi=08%3a30-12%3a0012%3a00-18%3a30&amp;horairesOuvertureSamedi=09%3a00-13%3a00&amp;horairesOuvertureDimanche=&amp;identifiantChronopostPointA2PAS=112149&amp;rtype=chronorelais&amp;icnname=ac&amp;lat=-23.48892330&amp;lng=-46.62647760&amp;sw-form-type-point=opt_chrlas&amp;is_print_direction=false&amp;from_addr=&amp;to_addr</MAP_URL>
      <AVAILABLE>full</AVAILABLE>
      <OPENING_HOURS_ITEMS>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>08:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>08:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>08:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>08:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>08:30</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>18:30</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>09:00</START_TM>
          <END_TM>13:00</END_TM>
        </OPENING_HOURS_ITEM>
      </OPENING_HOURS_ITEMS>
      <HOLIDAY_ITEMS />
    </PUDO_ITEM>
    <PUDO_ITEM active="true" overloaded="false">
      <PUDO_ID>BR10077</PUDO_ID>
      <ORDER>10</ORDER>
      <DISTANCE>14931</DISTANCE>
      <PUDO_TYPE>100</PUDO_TYPE>
      <PUDO_TYPE_INFOS />
      <NAME>OESTE FARMA</NAME>
      <LANGUAGE>PT</LANGUAGE>
      <STREETNUM>528</STREETNUM>
      <ADDRESS1>AVENIDA PRESIDENTE ALTINO</ADDRESS1>
      <ADDRESS2>
      </ADDRESS2>
      <ADDRESS3>JAGUARE</ADDRESS3>
      <LOCATION_HINT>
      </LOCATION_HINT>
      <ZIPCODE>05323-001</ZIPCODE>
      <CITY>SÃO PAULO</CITY>
      <COUNTRY>BRA</COUNTRY>
      <LONGITUDE>-46.74858530</LONGITUDE>
      <LATITUDE>-23.55100110</LATITUDE>
      <HANDICAPES>True</HANDICAPES>
      <PARKING>False</PARKING>
      <MAP_URL>http://www.chronopost.fr/transport-express/webdav/site/chronov4/groups/administrators/public/Chronomaps/print-result.html?request=print&amp;adresse1=528+AVENIDA+PRESIDENTE+ALTINO&amp;codePostal=05323-001&amp;localite=S%c3%83O+PAULO&amp;nomEnseigne=OESTE+FARMA&amp;horairesOuvertureLundi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureMardi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureMercredi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureJeudi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureVendredi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureSamedi=08%3a00-12%3a0012%3a00-19%3a00&amp;horairesOuvertureDimanche=08%3a00-13%3a00&amp;identifiantChronopostPointA2PAS=111552&amp;rtype=chronorelais&amp;icnname=ac&amp;lat=-23.55100110&amp;lng=-46.74858530&amp;sw-form-type-point=opt_chrlas&amp;is_print_direction=false&amp;from_addr=&amp;to_addr</MAP_URL>
      <AVAILABLE>full</AVAILABLE>
      <OPENING_HOURS_ITEMS>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>1</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>2</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>3</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>4</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>5</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>12:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>6</DAY_ID>
          <START_TM>12:00</START_TM>
          <END_TM>19:00</END_TM>
        </OPENING_HOURS_ITEM>
        <OPENING_HOURS_ITEM>
          <DAY_ID>7</DAY_ID>
          <START_TM>08:00</START_TM>
          <END_TM>13:00</END_TM>
        </OPENING_HOURS_ITEM>
      </OPENING_HOURS_ITEMS>
      <HOLIDAY_ITEMS />
    </PUDO_ITEM>
  </PUDO_ITEMS>
</RESPONSE>';
    }
    public function getMYPUDOList($params) {
        $ch = curl_init();

        $query_params = 'carrier=' . $params['carrier'] . '&key=' . $params['key'] . '&zipcode=' . $params['zipCode'] . '&city=' . str_replace(' ', '', strtoupper($params['city'])) . '&countrycode=' . $params['countrycode'] . '&requestID=' . $params['requestID'] . '&address=' . $params['address'] . '&date_from=' . $params['date_from'] . '&max_pudo_number=' . $params['max_pudo_number'] . '&max_distance_search=' . $params['max_distance_search'] . '&weight=' . $params['weight'] . '&category=' . $params['category'] . '&holiday_tolerant=' . $params['holiday_tolerant'];
        $request_url = $params['serviceurl'] . '?' . $query_params;

        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //$this->log->write("getMYPUDOList url: $request_url");

        $result = curl_exec($ch);
        //$this->log->write("mypudo resposta: $result");
        $pos = strpos($result, "PUDO_ITEMS");
        curl_close($ch);
        if ($pos == false){
            return false;
        }
        return $result;
    }
    function stripaccents($str){
        $str = preg_replace('/[\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}]/u','A', $str);
        $str = preg_replace('/[\x{0105}\x{0104}\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}]/u','a', $str);
        $str = preg_replace('/[\x{00C7}\x{0106}\x{0108}\x{010A}\x{010C}]/u','C', $str);
        $str = preg_replace('/[\x{00E7}\x{0107}\x{0109}\x{010B}\x{010D}}]/u','c', $str);
        $str = preg_replace('/[\x{010E}\x{0110}]/u','D', $str);
        $str = preg_replace('/[\x{010F}\x{0111}]/u','d', $str);
        $str = preg_replace('/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{0112}\x{0114}\x{0116}\x{0118}\x{011A}]/u','E', $str);
        $str = preg_replace('/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{0113}\x{0115}\x{0117}\x{0119}\x{011B}]/u','e', $str);
        $str = preg_replace('/[\x{00CC}\x{00CD}\x{00CE}\x{00CF}\x{0128}\x{012A}\x{012C}\x{012E}\x{0130}]/u','I', $str);
        $str = preg_replace('/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}\x{0129}\x{012B}\x{012D}\x{012F}\x{0131}]/u','i', $str);
        $str = preg_replace('/[\x{0142}\x{0141}\x{013E}\x{013A}]/u','l', $str);
        $str = preg_replace('/[\x{00F1}\x{0148}]/u','n', $str);
        $str = preg_replace('/[\x{00D2}\x{00D3}\x{00D4}\x{00D5}\x{00D6}\x{00D8}]/u','O', $str);
        $str = preg_replace('/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}]/u','o', $str);
        $str = preg_replace('/[\x{0159}\x{0155}]/u','r', $str);
        $str = preg_replace('/[\x{015B}\x{015A}\x{0161}]/u','s', $str);
        $str = preg_replace('/[\x{00DF}]/u','ss', $str);
        $str = preg_replace('/[\x{0165}]/u','t', $str);
        $str = preg_replace('/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{016E}\x{0170}\x{0172}]/u','U', $str);
        $str = preg_replace('/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{016F}\x{0171}\x{0173}]/u','u', $str);
        $str = preg_replace('/[\x{00FD}\x{00FF}]/u','y', $str);
        $str = preg_replace('/[\x{017C}\x{017A}\x{017B}\x{0179}\x{017E}]/u','z', $str);
        $str = preg_replace('/[\x{00C6}]/u','AE', $str);
        $str = preg_replace('/[\x{00E6}]/u','ae', $str);
        $str = preg_replace('/[\x{0152}]/u','OE', $str);
        $str = preg_replace('/[\x{0153}]/u','oe', $str);
        $str = preg_replace('/[\x{0022}\x{0025}\x{0026}\x{0027}\x{00A1}\x{00A2}\x{00A3}\x{00A4}\x{00A5}\x{00A6}\x{00A7}\x{00A8}\x{00AA}\x{00AB}\x{00AC}\x{00AD}\x{00AE}\x{00AF}\x{00B0}\x{00B1}\x{00B2}\x{00B3}\x{00B4}\x{00B5}\x{00B6}\x{00B7}\x{00B8}\x{00BA}\x{00BB}\x{00BC}\x{00BD}\x{00BE}\x{00BF}]/u',' ', $str);
        $str = strtoupper($str);
        return $str;
    }

    public function getRespError($msgerror) {
        //$this->log->write($msgerror);
        $method_data = array(
            'code'       => 'Jadloglista_error',
            'title'      => 'Jadlog - Retire em um ponto Pickup',
            'quote'      => '',
            'sort_order' => '',
            'error'      => $msgerror
        );
        return $method_data;
    }
    private function install() {
        $query = $this->db->query("DESC ".DB_PREFIX."customer cpf_or_cnpj");
        if (!$query->num_rows) {
            //$this->log->write("coluna cpf_or_cnpj nao encontrada em customer, criando");
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD `cpf_or_cnpj` varchar(20) default ''");
        }
    }

    private $dias_da_semana = array(
      "1" => ["segunda-feira", "seg.", "às segundas"],
      "2" => ["terça-feira", "ter.", "às terças"],
      "3" => ["quarta-feira", "qua.", "às quartas"],
      "4" => ["quinta-feira", "qui.", "às quintas"],
      "5" => ["sexta-feira", "sex.", "às sextas"],
      "6" => ["sábado", "sáb.", "aos sábados"],
      "7" => ["domingo", "dom.", "aos domingos"]
    );

    private function openingHoursXmlToString($o) {
      //$this->log->write("o: " . print_r($o, true));
      $horario = array();
      foreach ($o->OPENING_HOURS_ITEM as $i) {
        $day_id = (string)$i->DAY_ID;
        $value = isset($horario[$day_id]) ? $horario[$day_id] : array();
        array_push($value, "das " . $i->START_TM . " às " . $i->END_TM);
        $horario[$day_id] = $value;
      }
      $opening_hours_strings = array();
      foreach ($this->dias_da_semana as $key => $value) {
        $horario_string = isset($horario[$key]) ? join(" e ", $horario[$key]) : "fechado";
        array_push($opening_hours_strings, '"' . $value[1] . ": " . $horario_string . '"');
      }
      return join(",", $opening_hours_strings);
    }

    private function pudoXmlToString($p) {
      $opening_hours = $this->openingHoursXmlToString($p->OPENING_HOURS_ITEMS);
      $ret = '[{
        "PUDO_ID":"' . trim($p->PUDO_ID) . '",
        "ORDER":"1",
        "PUDO_TYPE":"' . trim($p->PUDO_TYPE) . '",
        "LATITUDE":"' . trim($p->LATITUDE) .'",
        "LONGITUDE":"' . trim($p->LONGITUDE) .'",
        "DISTANCE":"' . trim($p->DISTANCE) . '",
        "NAME":"' . trim($p->NAME) .'",
        "ADDRESS1":"' . trim($p->ADDRESS1) . '",
        "ADDRESS2":"' . trim($p->ADDRESS2) . '",
        "ADDRESS3":"' . trim($p->ADDRESS3) . '",
        "STREETNUM":"' . trim($p->STREETNUM) . '",
        "ZIPCODE":"' . trim($p->ZIPCODE) . '",
        "CITY":"' . trim($p->CITY) . '",
        "COUNTRY":"' . trim($p->COUNTRY) . '",
        "HANDICAPES":"' . trim($p->HANDICAPES) . '",
        "PARKING":"' . trim($p->PARKING) .'",
        "AVAILABLE":"' . trim($p->AVAILABLE) . '",
        "OPENING_HOURS":[' . $opening_hours . ']
      }]';
      //$json = json_encode($p)
      //$assoc_arrray = json_decode($json);
      //$urlEncodedString = http_build_query($assoc_array);
      //$this->log->write($urlEncodedString);
      return urlencode($ret);
    }

    public function getQuote($address)
    {
        $this->install();
        //$this->log->write("-- retire em um ponto Pickup -----------------------------");
        //$this->log->write("address: ".print_r($address, true));
        $this->language->load('shipping/jadloglista');
        //$this->log->write("this->cart: ".print_r($this->cart, true));

        $customer_id = $this->cart->customer->getId();
        //$this->log->write("customer_id: $customer_id");
        $customer_info = $this->getCustomer($customer_id);
        $cpfOrCnpj = $customer_info['cpf_or_cnpj'];
        //$this->log->write("cpfOrCnpj: " . print_r($cpfOrCnpj, true));

        //cidade
        //$this->log->write('$adress');
        //$this->log->write($address);

        $city_address = self::stripaccents($address['city']);
        if (empty($city_address)) {
          $address_id = $this->cart->customer->getAddressId();
           //$this->log->write("address_id: ".print_r($address_id, true));
          $this->load->model('account/address');
          //$this->log->write("module loaded");
          $customer_address = $this->model_account_address->getAddress($address_id);
          //$this->log->write("customer_address: ".print_r($customer_address, true));
          $city_address = self::stripaccents($customer_address['city']);
        }

        if (null == $cpfOrCnpj || strlen($cpfOrCnpj) == 0) {
            return $this->getRespError("Atributo cpf_or_cnpj inválido.");
        }
        $zipcodeFrom = str_replace('-','',$this->config->get('jadloglista_cep'));
        //$this->log->write("zipcodeFrom: $zipcodeFrom");
        if ($zipcodeFrom == null || strlen($zipcodeFrom) == 0) {
            return $this->getRespError("Por favor, ajuste o atributo cep nas configurações da Jadlog - Retire em um ponto Pickup.");
        }

        $quote_data = array();

        $weight = $this->cart->getWeight();
        //$this->log->write("weight: $weight");
        $vlDec = $this->cart->getTotal();
        //$this->log->write("vlDec: $vlDec");
        $pesoCubadoTotal = 0;
        foreach ($this->cart->getProducts() as $product_info) {
            $product_id = $product_info['product_id'];
            //$this->log->write("product_id: $product_id");

            $product_dim = $this->getProductDimensions($product_id);

            $height = $product_dim['height'];
            $length = $product_dim['length'];
            $width = $product_dim['width'];

            //$this->log->write("height: $height");
            //$this->log->write("length: $length");
            //$this->log->write("width: $width");

            if ($height == null || $height <= 0 || $length == null || $length <= 0 || $width == null || $width <= 0) {
                return $this->getRespError("Atributos height, length ou width são inválidos para o produto de id '" . $product_id . "'.");
            }
            $pesoCubado = ($height * $length * $width / 6000);
            //$this->log->write("peso cubado: $pesoCubado");
            if ($pesoCubado >= 36) {
                return $this->getRespError("As dimensões do produto de id '" . $product_id . "' ultrapassam a capacidade.");
            }
            $pesoCubadoTotal += $pesoCubado;
        }
        $pesoTaxado = $weight > $pesoCubadoTotal ? $weight : $pesoCubadoTotal;
        //$this->log->write("pesoReal: $weight, pesoCubado: $pesoCubadoTotal, pesoTaxado: $pesoTaxado");
        $requestId = rand(1000,100000000);
        $variables = array(
            'serviceurl' => $this->config->get('jadloglista_mypudo_service_url'),
            'carrier' => $this->config->get('jadloglista_mypudo_firmid'),
            'key' => $this->config->get('jadloglista_mypudo_key'),
            'address' => '', //self::stripaccents($address['address_1']),
            'zipCode' => str_replace('-', '', $address['postcode']),
            'city' => $city_address,
            'countrycode' => 'BRA',
            'requestID' => $requestId,
            'request_id' => $requestId,
            'date_from' => date('d/m/Y'),
            'max_pudo_number' => '',
            'max_distance_search' => '',
            'weight' => '',
            'category' => '',
            'holiday_tolerant' => ''
        );
        try {
            ini_set("default_socket_timeout", 5);

            $pointsRelais = $this->getMYPUDOList($variables);
//             $pointsRelais = $this->getMYPUDOListTest();

            if ($pointsRelais == false){
                return $this->getRespError("Resposta inválida do servidor de pontos de entrega!");
            }

            $xml = new SimpleXMLElement($pointsRelais);
            $quality = $xml["quality"];
            $relais_items = $xml->PUDO_ITEMS;
            if ($quality != 0) {
                $cpt = 0;
                foreach ($relais_items->PUDO_ITEM as $pointRelais) {
                    //$link = 'http://www.dpd.fr/dpdrelais/id_' . $pointRelais->PUDO_ID . '';
                    $link = $this->url->link('jadlog/pudomap') . '&pudo=' . $this->pudoXmlToString($pointRelais);
                    $delivery_cost_str = $this->getPrecoFrete($zipcodeFrom, str_replace('-', '', $pointRelais->ZIPCODE), $vlDec, $pesoTaxado);
                    if ($delivery_cost_str == false){
                        return $this->getRespError("Resposta inválida do servidor de frete!");
                    }
                    $delivery_cost_str = str_replace(".", "", $delivery_cost_str);
                    $delivery_cost_str = str_replace(",", ".", $delivery_cost_str);
                     //$this->log->write("antes: " . $delivery_cost_str);
                    $delivery_cost = floatval($delivery_cost_str);
                     //$this->log->write("depois: " . $delivery_cost);
                    if (! $delivery_cost)
                        continue;
                    $quote_data['jadloglista_' . $pointRelais->PUDO_ID] = array(
                        'code' => 'jadloglista.jadloglista_' . $pointRelais->PUDO_ID,
                        'title' => "<div class=\"lignepr\"><b>" . self::stripaccents($pointRelais->NAME) . ' (' . $pointRelais->PUDO_ID . ') ' . "</b><br/>" . self::stripaccents($pointRelais->ADDRESS1) . " " . self::stripaccents($pointRelais->STREETNUM) . " <br/>" . $pointRelais->ZIPCODE . " " . self::stripaccents($pointRelais->CITY) . " <a href=\"javascript:void(0);\" target=\"_blank\" onclick=\"window.open(&quot;" . $link . "&quot;,&quot;Ponto de Retirada&quot;,&quot;menubar=no, status=no, scrollbars=no, location=no, toolbar=no, width=1024, height=640&quot;);return false;\">          (" . number_format($pointRelais->DISTANCE / 1000, 2) . "km - " . $this->language->get('text_details') . ")</a></div>",
                        'cost' => $delivery_cost,
                        'tax_class_id' => $this->config->get('jadloglista_tax_class_id'),
                        'text' => $this->currency->format($this->tax->calculate($delivery_cost, $this->config->get('jadloglista_tax_class_id'), $this->config->get('config_tax')))
                    );
                    $cpt ++;
                    if ($cpt == 5)
                        break;
                }
            }
        } catch (Exception $e) {
            // Ne pas afficher de méthode de livraison si le WS ne répond pas de liste correcte de PR
        }
        $method_data = array();
         //$this->log->write("jadloglista_sort_order: ".$this->config->get('jadloglista_sort_order'));

        if ($quote_data) {
            $method_data = array(
                'code' => 'jadloglista',
                'title' => '<img src="image/data/dpdfrance/front/relais/carrier_logo.jpg"/> ' . $this->language->get('text_subtitle') . '<div class="dpdfrance_header">' . $this->language->get('text_header') . '</div>',
                'quote' => $quote_data,
                'sort_order' => $this->config->get('jadloglista_sort_order'),
                'error' => false
            );
        }
        //$this->log->write("getQuote()-end");

        return $method_data;
    }
}
?>