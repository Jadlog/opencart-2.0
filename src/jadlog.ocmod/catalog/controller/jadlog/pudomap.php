<?php
class ControllerJadlogPudomap extends Controller {
  private $error = array();

  public function index() {
    $data['get'] = $_GET;
    $data['pudo'] = $data['get']['pudo'];
    //'[{"PUDO_ID":"BR12043","ORDER":"1","PUDO_TYPE":"200","LATITUDE":"-23.50609260","LONGITUDE":"-46.68865850","DISTANCE":"158","NAME":"LJ JADLOG - SÃO PAULO 10","ADDRESS1":"RUA DOUTOR FREIRE CISNEIRO","ADDRESS2":"","ADDRESS3":"FREGUESIA DO O","STREETNUM":"97","ZIPCODE":"02714-020","CITY":"SÃO PAULO","COUNTRY":"BRA","HANDICAPES":"False","PARKING":"False","AVAILABLE":"full", "OPENING_HOURS":["seg.: das 08:00 às 12:30 e das 13:30 às 18:00","ter.: das 08:00 às 12:30 e das 13:30 às 18:00","qua.: das 08:00 às 12:30 e das 13:30 às 18:00","qui.: das 08:00 às 12:30 e das 13:30 às 18:00","sex.: das 08:00 às 12:30 e das 13:30 às 17:14","sáb.: fechado","dom.: fechado"]},{"PUDO_ID":"BR12043","ORDER":"1","PUDO_TYPE":"200","LATITUDE":"-23.50609260","LONGITUDE":"-46.68864750","DISTANCE":"158","NAME":"LJ JADLOG - SÃO PAULO 11","ADDRESS1":"RUA DOUTOR FREIRE CISNEIRO","ADDRESS2":"","ADDRESS3":"FREGUESIA DO O","STREETNUM":"97","ZIPCODE":"02714-020","CITY":"SÃO PAULO","COUNTRY":"BRA","HANDICAPES":"False","PARKING":"False","AVAILABLE":"full", "OPENING_HOURS":["seg.: das 08:00 às 12:30 e das 13:30 às 18:00","ter.: das 08:00 às 12:30 e das 13:30 às 18:00","qua.: das 08:00 às 12:30 e das 13:30 às 18:00","qui.: das 08:00 às 12:30 e das 13:30 às 18:00","sex.: das 08:00 às 12:30 e das 13:30 às 17:14","sáb.: fechado","dom.: fechado"]}]';
    // OpenCart 2.1 and below CHOOSE ACCORDINGLY
    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/jadlog/pudomap.tpl')) { //if file exists in your current template folder
      $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/jadlog/pudomap.tpl', $data)); //get it
    } else {
      $this->response->setOutput($this->load->view('default/template/jadlog/pudomap.tpl', $data)); //or get the file from the default folder
    }

    // OpenCart 2.2 and above CHOOSE ACCORDINGLY
    //$this->response->setOutput($this->load->view('jadlog/pudomap', $data));
  }
}
