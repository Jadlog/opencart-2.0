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

class ControllerSaleJadlog extends Controller {
    private $error = array();
    private $jadlog_dfes_valor_default = 'cfop,danfeCte,nrDoc,serie,tpDocumento,valor|cfop,danfeCte,nrDoc,serie,tpDocumento,valor';

    private function install() {
        $query = $this->db->query("DESC ".DB_PREFIX."order jadlog_embarcador_response_or_status");
        if (!$query->num_rows) {
            //$this->log->write("coluna jadlog_embarcador_response_or_status nao encontrada em order, criando");
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `jadlog_embarcador_response_or_status` varchar(500) default ''");
        }
        $query = $this->db->query("DESC ".DB_PREFIX."order jadlog_embarcador_dfes");
        if (!$query->num_rows) {
            //$this->log->write("coluna jadlog_embarcador_dfes nao encontrada em order, criando");
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `jadlog_embarcador_dfes` varchar(500) default ''");
        }
        $query = $this->db->query("DESC ".DB_PREFIX."order jadlog_embarcador_tracking_number");
        if (!$query->num_rows) {
            //$this->log->write("coluna jadlog_embarcador_tracking_number nao encontrada em order, criando");
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `jadlog_embarcador_tracking_number` varchar(100) default ''");
        }
    }

    public function index()
    {
        $this->install();
        $this->load->language('sale/dpdfrance');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('sale/dpdfrance');
        $this->getList();
    }

    public function saveMsgEmbarcador($order_id, $msg) {
         //$this->log->write("saveMsgEmbarcador($order_id, $msg)");
        $this->model_sale_dpdfrance->saveMsgEmbarcador($order_id, $msg);
    }

    public function export()
    {
        //$this->log->write("--------------------------");
        $this->load->language('sale/dpdfrance');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('sale/dpdfrance');

        if (isset($this->request->post['selected']))
        {
            $weights = $this->request->post['weight'];
            $dfespost = $this->request->post['dfe'];
             //$this->log->write("weights: ".print_r($weights, true));
             //$this->log->write("dfes: ".print_r($dfes, true));

            $dfeMsgErroDefault = 'O capo DFE é inválido. Separar campos por , e DFEs por |. Exemplo para 2 DFEs: "cfop,danfeCte,nrDoc,serie,tpDocumento,valor|cfop,danfeCte,nrDoc,serie,tpDocumento,valor", exemplo: "6909,null,DECLARACAO,null,2,20.2|6909,null,DECLARACAO,null,2,20.2"';
            $dfes = array();
            $msgError = '';
            foreach ($this->request->post['selected'] as $order_id) {

                $order_info = $this->model_sale_dpdfrance->getOrder($order_id);

                 //$this->log->write("order_info: ".print_r($order_info, true));

                ///////////////////
                $dfe_fields = $dfespost[$order_id];
                //$this->log->write("dfe_fields: $dfe_fields");

                if ($dfe_fields == $this->jadlog_dfes_valor_default) {
                    $msgError = $dfeMsgErroDefault;

                }elseif (strlen($dfe_fields) == 0) {
                    $msgError = $dfeMsgErroDefault;

                } else {
                    $dfesToParser = explode('|', $dfe_fields);
                    $msgError = '';
                    foreach ($dfesToParser as $dfe) {
                        if (strlen(trim($dfe)) == 0) {
                            $msgError = $dfeMsgErroDefault;
                            break;
                        }
                        $fs = explode(',', $dfe);
                        if (count($fs) != 6) {
                            $msgError = $dfeMsgErroDefault;
                            break;
                        }
                        foreach ($fs as $f) {
                            if (strlen($f) == 0) {
                                $msgError = $dfeMsgErroDefault;
                                break;
                            }
                        }
                        if (strlen($msgError) != 0) {
                            break;
                        }
                        array_push($dfes, array(
                            'cfop' => $fs[0],
                            'danfeCte' => $fs[1],
                            'nrDoc' => $fs[2],
                            'serie' => $fs[3],
                            'tpDocumento' => $fs[4],
                            'valor' => $fs[5]
                        ));
                    }
                }
                if (strlen($msgError) != 0) {
                    $this->saveMsgEmbarcador($order_id, $msgError);
                    continue;
                }
                // dfe ok, salva pra consulta futura
                //$this->log->write("saveEmbarcadorDfes($order_id, $dfe_fields)");
                $this->model_sale_dpdfrance->saveEmbarcadorDfes($order_id, $dfe_fields);
                ///////////////////

                ///////
                $products = $this->model_sale_dpdfrance->getOrderProducts($order_id);
                $volumes = array();
                $hasError = false;
                $pesoCubadoTotal = 0;

                foreach ($products as $product) {
                     //$this->log->write("product: ".print_r($product, true));
                    //$this->log->write("product_id: $product[product_id]");
                    $product_dim = $this->model_sale_dpdfrance->getProductDimensions($product['product_id'])[0];
                     //$this->log->write("dim: ".print_r($product_dim, true));

                    $height = $product_dim['height'];
                    $length = $product_dim['length'];
                    $width = $product_dim['width'];

                    //$this->log->write("height: $height");
                    //$this->log->write("length: $length");
                    //$this->log->write("width: $width");

                    if ($height == null || $height <= 0 || $length == null || $length <= 0 || $width == null || $width <= 0) {
                        $hasError = true;
                        $this->saveMsgEmbarcador($order_id, "Atributos height, length ou width são inválidos para o produto de id '".$product['product_id']."'.");
                        continue;
                    }
                    $pesoCubado = ($height * $length * $width / 6000);
                    //$this->log->write("peso cubado: $pesoCubado");
                    if ($pesoCubado >= 36) {
                        $hasError = true;
                        $this->saveMsgEmbarcador($order_id, "As dimensões do produto de id '".$product['product_id']."' ultrapassam a capacidade.");
                        continue;
                    }
                    $pesoCubadoTotal += $pesoCubado;

                    array_push($volumes, array(
                        'product_id' => $product['product_id'],
                        'altura' => $height,
                        'comprimento' => $length,
                        'identificador' => '',
                        'lacre' => 'null',
                        'largura' => $width,
                        'peso' => $product_dim['weight']
                    ));
                }
                 //$this->log->write("volumes: ".print_r($volumes, true));
                if ($hasError == true) {
                    // se tiver alguem item com problema nao chama o embarcador
                    continue;
                }
                ///////

                $weight = $weights[$order_id];
                $pesoTaxado = $weight > $pesoCubadoTotal ? $weight : $pesoCubadoTotal;
                //$this->log->write("pesoReal: $weight, pesoCubado: $pesoCubadoTotal, pesoTaxado: $pesoTaxado");

                $embarcadorServiceUrl = $this->config->get('jadloglista_embarcador_service_url');
                $embarcadorServiceAuthorization = $this->config->get('jadloglista_embarcador_authorization');
                $embarcadorclientid = $this->config->get('jadloglista_embarcador_clientid');
                $embarcadorContaCorrente = $this->config->get('jadloglista_embarcador_contacorrente');
                $embarcadorNumeroContrato = $this->config->get('jadloglista_embarcador_numerocontrato');

                $remnome = $this->config->get('jadloglista_nome');
                $remcnpjCpf = $this->config->get('jadloglista_cnpjCpf');
                $remendereco = $this->config->get('jadloglista_endereco');
                $remnumero = $this->config->get('jadloglista_numero');
                $remcompl = $this->config->get('jadloglista_compl');
                $rembairro = $this->config->get('jadloglista_bairro');
                $remcidade = $this->config->get('jadloglista_cidade');
                $remuf = $this->config->get('jadloglista_uf');
                $remcep = $this->config->get('jadloglista_cep');
                $remfone = $this->config->get('jadloglista_fone');
                $remcel = $this->config->get('jadloglista_cel');
                $rememail = $this->config->get('jadloglista_email');
                $remcontato = $this->config->get('jadloglista_contato');

                $dest_cnpjCpf = $this->model_sale_dpdfrance->getCustomer($order_info['customer_id'])['cpf_or_cnpj'];
                //$this->log->write("customer_id: ".$order_info['customer_id']);
                //$this->log->write("cpf_or_cnpj: $dest_cnpjCpf");

                $cd_pickup_des = 'null';
                $modalidade = '9';
                $value = $order_info['shipping_code'];
                $posm = strpos($value, 'jadlogclassic');
                if ($posm !== false) {
                    $cd_pickup_des =  'null';
                    $modalidade = '9';
                } else {
                    $pos = strpos($value, '_');
                    if ($pos === false) {
                        $cd_pickup_des = 'null';
                        $modalidade = '9';
                    } else {
                        $cd_pickup_des = "\"".substr($value, $pos+1, strlen($value)-$pos)."\"";
                        $modalidade = '40';
                    }
                }

                /////
                $variables = array(
                    'serviceurl' => $embarcadorServiceUrl,
                    'serviceauthorization' => $embarcadorServiceAuthorization,
                    'client_id' => $embarcadorclientid,
                    'conta_corrente' => $embarcadorContaCorrente,
                    'numero_contrato' => $embarcadorNumeroContrato,
                    'order_id' => $order_id,
                    'order_tot_valor' => $order_info['total'],
                    'conteudo' => 'PICKUP POINT',

                    'order_weight' => $pesoTaxado,
                    'cd_pickup_des' => $cd_pickup_des,
                    'modalidade' => $modalidade,

                    'rem_nome' => $remnome,
                    'rem_cnpjCpf' => $remcnpjCpf,
                    'rem_endereco' => $remendereco,
                    'rem_numero' => $remnumero,
                    'rem_compl' => $remcompl,
                    'rem_bairro' => $rembairro,
                    'rem_cidade' => $remcidade,
                    'rem_uf' => $remuf,
                    'rem_cep' => $remcep,
                    'rem_fone' => $remfone,
                    'rem_cel' => $remcel,
                    'rem_email' => $rememail,
                    'rem_contato' => $remcontato,

                    'dest_nome' => trim($order_info['shipping_firstname'].' '.$order_info['shipping_lastname']),
                    'dest_cnpjCpf' => $dest_cnpjCpf,
                    'dest_ie' => '',
                    'dest_endereco' => $order_info['shipping_address_1'],
                    'dest_numero' => '',// nao tem esse cara
                    'dest_compl' => $order_info['shipping_address_2'],
                    'dest_bairro' => '', // nao tem esse cara
                    'dest_cidade' => $order_info['shipping_zone'],
                    'dest_uf' => $order_info['shipping_zone_code'],
                    'dest_cep' => str_replace('-','', $order_info['shipping_postcode']),
                    'dest_fone' => $order_info['telephone'],
                    'dest_cel' => '',// nao tem esse cara
                    'dest_email' => $order_info['email'],
                    'dest_contato' => $order_info['customer'],
                    'volumes' => $volumes,
                    'dfes' => $dfes
                );
                $response = $this->_embarcadorIncluir($variables);
                $this->saveMsgEmbarcador($order_id, $response);
                if (strpos($response, 'sucesso') !== false) {
                    // resposta com sucesso, salva tracking
                    //$this->log->write("server success response: ".$response);
                    $trackingNumber = $this->getShippingIdByResponse($response);
                    //$this->log->write("trackingNumber: $trackingNumber");
                    $this->model_sale_dpdfrance->saveEmbarcadorTrackingNumber($order_id, $trackingNumber);
                    }
                    /////
            }
//             $record->display();

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';
            if (isset($this->request->get['filter_order_id']))
                $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            if (isset($this->request->get['filter_customer']))
                $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
            if (isset($this->request->get['filter_address']))
                $url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
            if (isset($this->request->get['filter_order_status_id']))
                $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
            if (isset($this->request->get['filter_total']))
                $url .= '&filter_total=' . $this->request->get['filter_total'];
            if (isset($this->request->get['filter_shipping_code']))
                $url .= '&filter_shipping_code=' . $this->request->get['filter_shipping_code'];
            if (isset($this->request->get['filter_date_added']))
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            if (isset($this->request->get['filter_date_modified']))
                $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            if (isset($this->request->get['sort']))
                $url .= '&sort=' . $this->request->get['sort'];
            if (isset($this->request->get['order']))
                $url .= '&order=' . $this->request->get['order'];
            if (isset($this->request->get['page']))
                $url .= '&page=' . $this->request->get['page'];

            $this->response->redirect($this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }
        $this->getList();
    }
    public function getShippingIdByResponse($response) {
        // {"codigo":"76287645","shipmentId":"06601100000137","status":"Solicitação inserida com sucesso."}
        $sl1 = '"shipmentId":"';
        $pos = strpos($response, $sl1)+strlen($sl1);
        $pos2 = strpos($response, '"', $pos);
        return substr($response, $pos, $pos2-$pos);
    }
    public function getCdPickupDes($value) {
        $posm = strpos($value, 'jadlogclassic');
        if ($posm !== false) {
            return '';
        }
        $pos = strpos($value, '_');
        if ($pos === false) {
            return "";
        }
        return substr($value, $pos+1, strlen($value)-$pos);
    }


    public function dpdfrancetracking()
    {
        $this->load->language('sale/dpdfrance');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('sale/dpdfrance');

        if (isset($this->request->post['selected']))
        {
            foreach ($this->request->post['selected'] as $order_id)
            {
                $order_info = $this->model_sale_dpdfrance->getOrder($order_id);
                $order_lang = new Language($order_info['language_directory']);
                $order_lang->load('sale/dpdfrance');
                if (strpos($order_info['shipping_code'], 'dpdfrrelais') !== false){
                    $service = 'Relais';
                    $agence = $this->config->get('jadloglista_agence');
                    $compte_chargeur = $this->config->get('jadloglista_cargo');
                }else if (strpos($order_info['shipping_code'], 'dpdfrpredict') !== false){
                    $service = 'Predict';
                    $agence = $this->config->get('dpdfrpredict_agence');
                    $compte_chargeur = $this->config->get('dpdfrpredict_cargo');
                }else{
                    $service = 'Classic';
                    $agence = $this->config->get('dpdfrclassic_agence');
                    $compte_chargeur = $this->config->get('dpdfrclassic_cargo');
                }
                $data = array(  'notify' => 1,
                                'order_status_id' => '15',
                                'comment' => $order_lang->get('text_tracking').'http://www.dpd.fr/tracer_'.$order_id.'_'.$agence.$compte_chargeur
                                );
                $this->model_sale_dpdfrance->addOrderHistory($order_id, $data);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['filter_order_id']))
                $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            if (isset($this->request->get['filter_customer']))
                $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
            if (isset($this->request->get['filter_address']))
                $url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
            if (isset($this->request->get['filter_order_status_id']))
                $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
            if (isset($this->request->get['filter_total']))
                $url .= '&filter_total=' . $this->request->get['filter_total'];
            if (isset($this->request->get['filter_shipping_code']))
                $url .= '&filter_shipping_code=' . $this->request->get['filter_shipping_code'];
            if (isset($this->request->get['filter_date_added']))
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            if (isset($this->request->get['filter_date_modified']))
                $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            if (isset($this->request->get['sort']))
                $url .= '&sort=' . $this->request->get['sort'];
            if (isset($this->request->get['order']))
                $url .= '&order=' . $this->request->get['order'];
            if (isset($this->request->get['page']))
                $url .= '&page=' . $this->request->get['page'];
            $this->response->redirect($this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    public function dpdfrancelivre()
    {
        $this->load->language('sale/dpdfrance');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('sale/dpdfrance');

        if (isset($this->request->post['selected']))
        {
            foreach ($this->request->post['selected'] as $order_id)
            {
            $data = array('notify' => 0, 'order_status_id' => '3', 'comment' => '');
            $this->model_sale_dpdfrance->addOrderHistory($order_id, $data);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['filter_order_id']))
                $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            if (isset($this->request->get['filter_customer']))
                $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
            if (isset($this->request->get['filter_address']))
                $url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
            if (isset($this->request->get['filter_order_status_id']))
                $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
            if (isset($this->request->get['filter_total']))
                $url .= '&filter_total=' . $this->request->get['filter_total'];
            if (isset($this->request->get['filter_date_added']))
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            if (isset($this->request->get['filter_shipping_code']))
                $url .= '&filter_shipping_code=' . $this->request->get['filter_shipping_code'];
            if (isset($this->request->get['filter_date_modified']))
                $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            if (isset($this->request->get['sort']))
                $url .= '&sort=' . $this->request->get['sort'];
            if (isset($this->request->get['order']))
                $url .= '&order=' . $this->request->get['order'];
            if (isset($this->request->get['page']))
                $url .= '&page=' . $this->request->get['page'];
            $this->response->redirect($this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }
    public function getAddress($from) {
        $pos = strpos($from, "<a href=");
        if ($pos) {
            return substr($from, 0, $pos-1);
        }
        return $from;
    }

    protected function getList()
    {
         //$this->log->write("------------------------");
        if (isset($this->request->get['filter_order_id']))
            $filter_order_id = $this->request->get['filter_order_id'];
        else
            $filter_order_id = null;

        if (isset($this->request->get['filter_customer']))
            $filter_customer = $this->request->get['filter_customer'];
        else
            $filter_customer = null;

        if (isset($this->request->get['filter_address']))
            $filter_address = $this->request->get['filter_address'];
        else
            $filter_address = null;

        if (isset($this->request->get['filter_order_status_id']))
            $filter_order_status_id = $this->request->get['filter_order_status_id'];
        else
            $filter_order_status_id = null;

        if (isset($this->request->get['filter_total']))
            $filter_total = $this->request->get['filter_total'];
        else
            $filter_total = null;

        if (isset($this->request->get['filter_shipping_code']))
            $filter_shipping_code = $this->request->get['filter_shipping_code'];
        else
            $filter_shipping_code = null;

        if (isset($this->request->get['filter_date_added']))
            $filter_date_added = $this->request->get['filter_date_added'];
        else
            $filter_date_added = null;

        if (isset($this->request->get['filter_date_modified']))
            $filter_date_modified = $this->request->get['filter_date_modified'];
        else
            $filter_date_modified = null;

        if (isset($this->request->get['sort']))
            $sort = $this->request->get['sort'];
        else
            $sort = 'o.order_id';

        if (isset($this->request->get['order']))
            $order = $this->request->get['order'];
        else
            $order = 'DESC';

        if (isset($this->request->get['page']))
            $page = $this->request->get['page'];
        else
            $page = 1;

        $url = '';

        if (isset($this->request->get['filter_order_id']))
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        if (isset($this->request->get['filter_customer']))
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        if (isset($this->request->get['filter_address']))
            $url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
        if (isset($this->request->get['filter_order_status_id']))
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        if (isset($this->request->get['filter_total']))
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        if (isset($this->request->get['filter_date_added']))
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        if (isset($this->request->get['filter_shipping_code']))
            $url .= '&filter_shipping_code=' . $this->request->get['filter_shipping_code'];
        if (isset($this->request->get['filter_date_modified']))
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        if (isset($this->request->get['sort']))
            $url .= '&sort=' . $this->request->get['sort'];
        if (isset($this->request->get['order']))
            $url .= '&order=' . $this->request->get['order'];
        if (isset($this->request->get['page']))
            $url .= '&page=' . $this->request->get['page'];

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['dpdfranceexport'] = $this->url->link('sale/jadlog/export', 'token=' . $this->session->data['token'] . $url, 'SSL');
//         $data['dpdfrancetracking'] = $this->url->link('sale/dpdfrance/dpdfrancetracking', 'token=' . $this->session->data['token'] . $url, 'SSL');
//         $data['dpdfrancelivre'] = $this->url->link('sale/dpdfrance/dpdfrancelivre', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['orders'] = array();

        $filter_data = array(
            'filter_order_id'        => $filter_order_id,
            'filter_customer'        => $filter_customer,
            'filter_address'         => $filter_address,
            'filter_order_status_id' => $filter_order_status_id,
            'filter_total'           => $filter_total,
            'filter_shipping_code'   => $filter_shipping_code,
            'filter_date_added'      => $filter_date_added,
            'filter_date_modified'   => $filter_date_modified,
            'sort'                   => $sort,
            'order'                  => $order,
            'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
            'limit'                  => $this->config->get('config_admin_limit')
        );

        $order_total = $this->model_sale_dpdfrance->getTotalOrders($filter_data);
        $results = $this->model_sale_dpdfrance->getOrders($filter_data);

        foreach ($results as $result)
        {
            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$result['shipping_country_id'] . "'");

            if ($country_query->num_rows)
                $shipping_iso_code_2 = $country_query->row['iso_code_2'];
            else
                $shipping_iso_code_2 = '';

            $action = array();
            if (strpos($result['shipping_code'], 'jadloglista') !== false){
                $service = 'JadlogLista';
                $icon = '<span style="font-size:0px;">Pickup (40)</span><img src="../image/data/dpdfrance/admin/service_relais.png" alt="Pickup (40)" title="Pickup (40)"/>';
                $address = $this->getAddress($result['relais_address']);
                $link = '<a href="http://www.dpd.fr/tracer_'.$result['order_id'].'_'.$this->config->get('jadloglista_agence').$this->config->get('jadloglista_cargo').'" target="_blank"><img src="../image/data/dpdfrance/admin/tracking.png"/></a>';
            }else{
                    $service = 'Classic';
                    $icon = '<span style="font-size:0px;">Jadlog (9)</span><img src="../image/data/dpdfrance/admin/service_world.png" alt="Jadlog (9)" title="Jadlog (9)"/>';
                    $address = $result['address'];
                    $link = '<a href="http://www.dpd.fr/tracer_'.$result['order_id'].'_'.$this->config->get('dpdfrclassic_agence').$this->config->get('dpdfrclassic_cargo').'" target="_blank"><img src="../image/data/dpdfrance/admin/tracking.png"/></a>';
            }
            $jadlog_embarcador_dfes = $result['jadlog_embarcador_dfes'];
            if ($jadlog_embarcador_dfes == null || strlen($jadlog_embarcador_dfes) ==0) {
                // valor default
                $jadlog_embarcador_dfes = $this->jadlog_dfes_valor_default;
            }
            $data['orders'][] = array(
                'order_id'      => $result['order_id'],
                'customer'      => $result['customer'],
                'address'       => $address,
                'status'        => $result['status'],
                'weight'        => number_format($this->model_sale_dpdfrance->getOrderWeight($result['order_id']), 2),
                'shipping_code' => $icon,
                'jadlog_embarcador_dfes' => $jadlog_embarcador_dfes,
                'parcel_trace'  => $link,
                'jadlog_embarcador_response_or_status' => htmlspecialchars($result['jadlog_embarcador_response_or_status'], ENT_QUOTES),
                'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'date_added'    => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
                'date_modified' => date($this->language->get('datetime_format'), strtotime($result['date_modified'])),
                'selected'      => isset($this->request->post['selected']) && in_array($result['order_id'], $this->request->post['selected']),
                'action'        => $action
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_export'] = $this->language->get('text_export');
        $data['text_trackings'] = $this->language->get('text_trackings');
        $data['text_livre'] = $this->language->get('text_livre');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_missing'] = $this->language->get('text_missing');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_news'] = $this->language->get('text_news');
        $data['text_search_filter'] = $this->language->get('text_search_filter');

        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_customer'] = $this->language->get('column_customer');
        $data['column_address'] = $this->language->get('column_address');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_shipping_code'] = $this->language->get('column_shipping_code');
        $data['column_total'] = $this->language->get('column_total');
        $data['column_weight'] = $this->language->get('column_weight');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_date_modified'] = $this->language->get('column_date_modified');
        $data['column_parcel_trace'] = $this->language->get('column_parcel_trace');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_invoice'] = $this->language->get('button_invoice');
        $data['button_insert'] = $this->language->get('button_insert');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning']))
            $data['error_warning'] = $this->error['warning'];
        else
            $data['error_warning'] = '';

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else
            $data['success'] = '';

        $url = '';

        if (isset($this->request->get['filter_order_id']))
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        if (isset($this->request->get['filter_customer']))
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        if (isset($this->request->get['filter_address']))
            $url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
        if (isset($this->request->get['filter_order_status_id']))
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        if (isset($this->request->get['filter_total']))
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        if (isset($this->request->get['filter_shipping_code']))
            $url .= '&filter_shipping_code=' . $this->request->get['filter_shipping_code'];
        if (isset($this->request->get['filter_date_added']))
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        if (isset($this->request->get['filter_date_modified']))
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];

        if ($order == 'ASC')
            $url .= '&order=DESC';
        else
            $url .= '&order=ASC';

        if (isset($this->request->get['page']))
            $url .= '&page=' . $this->request->get['page'];

        $data['sort_order'] = $this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, 'SSL');
        $data['sort_customer'] = $this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
        $data['sort_address'] = $this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . '&sort=address' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
        $data['sort_shipping_code'] = $this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . '&sort=shipping_code' . $url, 'SSL');
        $data['sort_total'] = $this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
        $data['sort_date_added'] = $this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
        $data['sort_date_modified'] = $this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');

        $url = '';
        if (isset($this->request->get['filter_order_id']))
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        if (isset($this->request->get['filter_customer']))
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        if (isset($this->request->get['filter_address']))
            $url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_QUOTES, 'UTF-8'));
        if (isset($this->request->get['filter_order_status_id']))
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        if (isset($this->request->get['filter_total']))
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        if (isset($this->request->get['filter_shipping_code']))
            $url .= '&filter_shipping_code=' . $this->request->get['filter_shipping_code'];
        if (isset($this->request->get['filter_date_added']))
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        if (isset($this->request->get['filter_date_modified']))
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        if (isset($this->request->get['sort']))
            $url .= '&sort=' . $this->request->get['sort'];
        if (isset($this->request->get['order']))
            $url .= '&order=' . $this->request->get['order'];
        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_admin_limit');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('sale/jadlog', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['filter_order_id'] = $filter_order_id;
        $data['filter_customer'] = $filter_customer;
        $data['filter_address'] = $filter_address;
        $data['filter_order_status_id'] = $filter_order_status_id;
        $data['filter_total'] = $filter_total;
        $data['filter_shipping_code'] = $filter_shipping_code;
        $data['filter_date_added'] = $filter_date_added;
        $data['filter_date_modified'] = $filter_date_modified;

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        // Flux RSS
        $rss = @simplexml_load_string(file_get_contents('http://www.dpd.fr/extensions/rss/flux_info_dpdfr.xml'));
        if (!empty($rss->channel->item))
        {
            $data['rss'] = '<fieldset><legend><a href="javascript:void(0)" onclick="$(&quot;#zonemarquee&quot;).toggle(&quot;fast&quot;, function() {var text = $(&quot;#showhide&quot;).text();$(&quot;#showhide&quot;).text(text == &quot;+&quot; ? &quot;-&quot; : &quot;+&quot;);});"><img src="../image/data/dpdfrance/admin/rss_icon.png" />'.$data['text_news'].'<div id="showhide">-</div></a></legend><div id="zonemarquee"><div id="marquee" class="marquee">';
            foreach ($rss->channel->item as $item)
                $data['rss'] .= '<strong>'.$item->category.' > '.$item->title.' : </strong> '.$item->description.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $data['rss'] .= '</div></div></fieldset><br/>';
        }
        // Fin RSS

        $this->response->setOutput($this->load->view('sale/dpdfrance.tpl', $data));
    }
    private function _embarcadorIncluir($params)
    {
        $ch = curl_init();

        //$this->log->write("embarcador url: ".$params['serviceurl']);
        curl_setopt($ch, CURLOPT_URL, $params['serviceurl']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "authorization: $params[serviceauthorization]",
            "cache-control: no-cache",
            "content-type: application/json"
        ));

        $dfesOut = '    "dfe": [';
        foreach ($params['dfes'] as $dfe) {
            $dfesOut .= '
        {
            "cfop": "' . $dfe['cfop'] . '",
            "danfeCte": ' . $dfe['danfeCte'] . ',
            "nrDoc": "' . $dfe['nrDoc'] . '",
            "serie": ' . $dfe['serie'] . ',
            "tpDocumento": ' . $dfe['tpDocumento'] . ',
            "valor": ' . $dfe['valor'] . '
        },';
        }
        if (count($params['dfes']) > 0) {
            $dfesOut = substr($dfesOut, 0, strlen($dfesOut) - 1);
        }
        $dfesOut .= '
    ],';
        $volumeOut = '    "volume": [';
        foreach ($params['volumes'] as $vol) {
            $volumeOut .= '
        {
            "altura": ' . $vol['altura'] . ',
            "comprimento": ' . $vol['comprimento'] . ',
            "identificador": "' . $vol['identificador'] . '",
            "lacre": ' . $vol['lacre'] . ',
            "largura": ' . $vol['largura'] . ',
            "peso": ' . $vol['peso'] . '
        },';
        }
        if (count($params['volumes']) > 0) {
            $volumeOut = substr($volumeOut, 0, strlen($volumeOut) - 1);
        }
        $volumeOut .= '
    ]';

        $body = '{
    "codCliente": "' . $params['client_id'] . '",
    "conteudo": "' . $params['conteudo'] . '",
    "pedido": "' . $params['order_id'] . '",
    "totPeso": ' . $params['order_weight'] . ',
    "totValor": ' . $params['order_tot_valor'] . ',
    "obs": "",
    "modalidade": ' . $params['modalidade'] . ',
    "contaCorrente": "' . $params['conta_corrente'] . '",
    "centroCusto": null,
    "tpColeta": "K",
    "cdPickupOri": null,
    "cdPickupDes": ' . $params['cd_pickup_des'] . ',
    "tipoFrete": 0,
    "cdUnidadeOri": "1",
    "cdUnidadeDes": null,
    "vlColeta" : null,
    "nrContrato": ' . $params['numero_contrato'] . ',
    "servico": 1,
    "shipmentId": null,
    "rem": {
        "nome": "' . $params['rem_nome'] . '",
        "cnpjCpf": "' . $params['rem_cnpjCpf'] . '",
        "ie": "",
        "endereco": "' . $params['rem_endereco'] . '",
        "numero": "' . $params['rem_numero'] . '",
        "compl": "' . $params['rem_compl'] . '",
        "bairro": "' . $params['rem_bairro'] . '",
        "cidade": "' . $params['rem_cidade'] . '",
        "uf": "' . $params['rem_uf'] . '",
        "cep": "' . $params['rem_cep'] . '",
        "fone": "' . $params['rem_fone'] . '",
        "cel": "' . $params['rem_cel'] . '",
        "email": "' . $params['rem_email'] . '",
        "contato": "' . $params['rem_contato'] . '"
    },
    "des": {
        "nome": "' . $params['dest_nome'] . '",
        "cnpjCpf": "' . $params['dest_cnpjCpf'] . '",
        "ie": "' . $params['dest_ie'] . '",
        "endereco": "' . $params['dest_endereco'] . '",
        "numero": "' . $params['dest_numero'] . '",
        "compl": "' . $params['dest_compl'] . '",
        "bairro": "' . $params['dest_bairro'] . '",
        "cidade": "' . $params['dest_cidade'] . '",
        "uf": "' . $params['dest_uf'] . '",
        "cep": "' . $params['dest_cep'] . '",
        "fone": "' . $params['dest_fone'] . '",
        "cel": "' . $params['dest_cel'] . '",
        "email": "' . $params['dest_email'] . '",
        "contato": "' . $params['dest_contato'] . '"
    },
' . $dfesOut . '
' . $volumeOut . '
}';

        //$this->log->write("embarcador body: ".$body);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

                $result = curl_exec($ch);
                //$this->log->write("embarcador resposta: ".$result);
// $result = '{"codigo":"76287645","shipmentId":"06601100000137","status":"Solicitação inserida com sucesso."}';
        curl_close($ch);

        return $result;
    }
}

class DPDStation
{

    var $line;
    var $contenu_fichier;

    function __construct()
    {
        $this->line = str_pad("", 2247);
        $this->contenu_fichier = '';
    }

    function add($txt, $position, $length)
    {
        $txt = $this->stripAccents($txt);
        $this->line = substr_replace($this->line, str_pad($txt, $length), $position, $length);
    }

    function convdate($date1)
    {
        $d1 = explode("-", $date1);
        $date2 = date("d/m/Y", mktime(0, 0, 0, (int) $d1[1], (int) $d1[2], (int) $d1[0]));
        return $date2;
    }

    function add_line()
    {
        if ($this->contenu_fichier != '') {
            $this->contenu_fichier = $this->contenu_fichier . "\r\n" . $this->line;
            $this->line = '';
            $this->line = str_pad("", 2247);
        } else {
            $this->contenu_fichier.=$this->line;
            $this->line = '';
            $this->line = str_pad("", 2247);
        }
    }

    function display()
    {
        if (ob_get_contents()) ob_end_clean();
        header('Content-type: application/dat');
        header('Content-Disposition: attachment; filename="DPDFRANCE_' . date("dmY-His") . '.dat"');
        echo '$VERSION=110' . "\r\n";
        echo $this->contenu_fichier. "\r\n";
        exit;
    }

//     public static function formatGSM($gsm_dest,$code_iso)
//     {
//         if ($code_iso == 'F') {
//             $gsm_dest = str_replace(array(' ', '.', '-', ',', ';', '/', '\\', '(', ')'),'',$gsm_dest);
//             $gsm_dest = str_replace('+33','0',$gsm_dest);
//             if (substr($gsm_dest, 0, 2) == 33) // Chrome autofill fix
//                 $gsm_dest = substr_replace($gsm_dest, '0', 0, 2);
//         }
//         return $gsm_dest;
//     }

    function stripAccents($str)
    {
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
        return $str;
    }


}