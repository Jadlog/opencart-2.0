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
class ControllerShippingJadloglista extends Controller
{

    private $error = array();

    public function index()
    {
        $this->load->language('shipping/jadloglista');
        
        $heading_title = $this->language->get('heading_title');
        $this->document->setTitle($heading_title);
        
        $this->load->model('setting/setting');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('jadloglista', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
        }
        
        $data['heading_title'] = $this->language->get('heading_title');
        
//         $data['text_none'] = $this->language->get('text_none');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_activate'] = $this->language->get('text_activate');
//         $data['text_delivery'] = $this->language->get('text_delivery');
//         $data['text_agence'] = $this->language->get('text_agence');
//         $data['text_cargo'] = $this->language->get('text_cargo');
//         $data['text_advalorem'] = $this->language->get('text_advalorem');
//         $data['text_retour'] = $this->language->get('text_retour');
//         $data['text_retour_off'] = $this->language->get('text_retour_off');
//         $data['text_retour_ondemand'] = $this->language->get('text_retour_ondemand');
//         $data['text_retour_prepared'] = $this->language->get('text_retour_prepared');
//         $data['text_suppiles'] = $this->language->get('text_suppiles');
//         $data['text_suppmontagne'] = $this->language->get('text_suppmontagne');
//         $data['text_sort_order'] = $this->language->get('text_sort_order');
//         $data['text_franco'] = $this->language->get('text_franco');
//         $data['text_mypudo'] = $this->language->get('text_mypudo');
        
//         $data['entry_rate'] = $this->language->get('entry_rate');
//         $data['entry_tax_class'] = $this->language->get('entry_tax_class');
        $data['entry_status'] = $this->language->get('entry_status');
//         $data['entry_franco'] = $this->language->get('entry_franco');
//         $data['entry_activate'] = $this->language->get('entry_activate');
//         $data['entry_delivery'] = $this->language->get('entry_delivery');
//         $data['entry_agence'] = $this->language->get('entry_agence');
//         $data['entry_cargo'] = $this->language->get('entry_cargo');
//         $data['entry_advalorem'] = $this->language->get('entry_advalorem');
//         $data['entry_retour'] = $this->language->get('entry_retour');
//         $data['entry_suppiles'] = $this->language->get('entry_suppiles');
//         $data['entry_suppmontagne'] = $this->language->get('entry_suppmontagne');
//         $data['entry_sort_order'] = $this->language->get('entry_sort_order');
//         $data['entry_mypudo'] = $this->language->get('entry_mypudo');
        
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        
        $data['tab_general'] = $this->language->get('tab_general');
        
        if (isset($this->error['warning']))
            $data['error_warning'] = $this->error['warning'];
        else
            $data['error_warning'] = '';
        
        $data['breadcrumbs'] = array();
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_shipping'),
            'href' => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('shipping/jadloglista', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        
        $data['action'] = $this->url->link('shipping/jadloglista', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');
        
//         $this->load->model('localisation/geo_zone');
//         $geo_zones = $this->model_localisation_geo_zone->getGeoZones();
        
//         foreach ($geo_zones as $geo_zone) {
//             if (isset($this->request->post['jadloglista_' . $geo_zone['geo_zone_id'] . '_rate']))
//                 $data['jadloglista_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->request->post['jadloglista_' . $geo_zone['geo_zone_id'] . '_rate'];
//             else
//                 $data['jadloglista_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->config->get('jadloglista_' . $geo_zone['geo_zone_id'] . '_rate');
            
//             if (isset($this->request->post['jadloglista_' . $geo_zone['geo_zone_id'] . '_status']))
//                 $data['jadloglista_' . $geo_zone['geo_zone_id'] . '_status'] = $this->request->post['jadloglista_' . $geo_zone['geo_zone_id'] . '_status'];
//             else
//                 $data['jadloglista_' . $geo_zone['geo_zone_id'] . '_status'] = $this->config->get('jadloglista_' . $geo_zone['geo_zone_id'] . '_status');
            
//             if (isset($this->request->post['jadloglista_' . $geo_zone['geo_zone_id'] . '_franco']))
//                 $data['jadloglista_' . $geo_zone['geo_zone_id'] . '_franco'] = $this->request->post['jadloglista_' . $geo_zone['geo_zone_id'] . '_franco'];
//             else
//                 $data['jadloglista_' . $geo_zone['geo_zone_id'] . '_franco'] = $this->config->get('jadloglista_' . $geo_zone['geo_zone_id'] . '_franco');
//         }
        
//         $data['geo_zones'] = $geo_zones;
        
//         if (isset($this->request->post['jadloglista_tax_class_id']))
//             $data['jadloglista_tax_class_id'] = $this->request->post['jadloglista_tax_class_id'];
//         else
//             $data['jadloglista_tax_class_id'] = $this->config->get('jadloglista_tax_class_id');
        
//         $this->load->model('localisation/tax_class');
//         $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
        
        if (isset($this->request->post['jadloglista_status']))
            $data['jadloglista_status'] = $this->request->post['jadloglista_status'];
        else
            $data['jadloglista_status'] = $this->config->get('jadloglista_status');
        
//         if (isset($this->request->post['jadloglista_mypudo']))
//             $data['jadloglista_mypudo'] = $this->request->post['jadloglista_mypudo'];
//         else
//             $data['jadloglista_mypudo'] = $this->config->get('jadloglista_mypudo');
        
        // ///////
        if (isset($this->request->post['jadloglista_nome']))
            $data['jadloglista_nome'] = $this->request->post['jadloglista_nome'];
        else
            $data['jadloglista_nome'] = $this->config->get('jadloglista_nome');
        
        if (isset($this->request->post['jadloglista_cnpjCpf']))
            $data['jadloglista_cnpjCpf'] = $this->request->post['jadloglista_cnpjCpf'];
        else
            $data['jadloglista_cnpjCpf'] = $this->config->get('jadloglista_cnpjCpf');
        
        if (isset($this->request->post['jadloglista_endereco']))
            $data['jadloglista_endereco'] = $this->request->post['jadloglista_endereco'];
        else
            $data['jadloglista_endereco'] = $this->config->get('jadloglista_endereco');
        
        if (isset($this->request->post['jadloglista_numero']))
            $data['jadloglista_numero'] = $this->request->post['jadloglista_numero'];
        else
            $data['jadloglista_numero'] = $this->config->get('jadloglista_numero');
        
        if (isset($this->request->post['jadloglista_compl']))
            $data['jadloglista_compl'] = $this->request->post['jadloglista_compl'];
        else
            $data['jadloglista_compl'] = $this->config->get('jadloglista_compl');
        
        if (isset($this->request->post['jadloglista_bairro']))
            $data['jadloglista_bairro'] = $this->request->post['jadloglista_bairro'];
        else
            $data['jadloglista_bairro'] = $this->config->get('jadloglista_bairro');
        
        if (isset($this->request->post['jadloglista_cidade']))
            $data['jadloglista_cidade'] = $this->request->post['jadloglista_cidade'];
        else
            $data['jadloglista_cidade'] = $this->config->get('jadloglista_cidade');
        
        if (isset($this->request->post['jadloglista_uf']))
            $data['jadloglista_uf'] = $this->request->post['jadloglista_uf'];
        else
            $data['jadloglista_uf'] = $this->config->get('jadloglista_uf');
        
        if (isset($this->request->post['jadloglista_cep']))
            $data['jadloglista_cep'] = $this->request->post['jadloglista_cep'];
        else
            $data['jadloglista_cep'] = $this->config->get('jadloglista_cep');
        
        if (isset($this->request->post['jadloglista_fone']))
            $data['jadloglista_fone'] = $this->request->post['jadloglista_fone'];
        else
            $data['jadloglista_fone'] = $this->config->get('jadloglista_fone');
        
        if (isset($this->request->post['jadloglista_cel']))
            $data['jadloglista_cel'] = $this->request->post['jadloglista_cel'];
        else
            $data['jadloglista_cel'] = $this->config->get('jadloglista_cel');
        
        if (isset($this->request->post['jadloglista_email']))
            $data['jadloglista_email'] = $this->request->post['jadloglista_email'];
        else
            $data['jadloglista_email'] = $this->config->get('jadloglista_email');
        
        if (isset($this->request->post['jadloglista_contato']))
            $data['jadloglista_contato'] = $this->request->post['jadloglista_contato'];
        else
            $data['jadloglista_contato'] = $this->config->get('jadloglista_contato');
        
        if (isset($this->request->post['jadloglista_embarcador_service_url']))
            $data['jadloglista_embarcador_service_url'] = $this->request->post['jadloglista_embarcador_service_url'];
        elseif (null != $this->config->get('jadloglista_embarcador_service_url'))
            $data['jadloglista_embarcador_service_url'] = $this->config->get('jadloglista_embarcador_service_url');
        else
            $data['jadloglista_embarcador_service_url'] = 'http://www.jadlog.com.br/embarcador/api/pedido/incluir';
        
        if (isset($this->request->post['jadloglista_embarcador_authorization']))
            $data['jadloglista_embarcador_authorization'] = $this->request->post['jadloglista_embarcador_authorization'];
        else
            $data['jadloglista_embarcador_authorization'] = $this->config->get('jadloglista_embarcador_authorization');
        
        if (isset($this->request->post['jadloglista_embarcador_clientid']))
            $data['jadloglista_embarcador_clientid'] = $this->request->post['jadloglista_embarcador_clientid'];
        else
            $data['jadloglista_embarcador_clientid'] = $this->config->get('jadloglista_embarcador_clientid');
        
        if (isset($this->request->post['jadloglista_embarcador_contacorrente']))
            $data['jadloglista_embarcador_contacorrente'] = $this->request->post['jadloglista_embarcador_contacorrente'];
        else
            $data['jadloglista_embarcador_contacorrente'] = $this->config->get('jadloglista_embarcador_contacorrente');
        
        if (isset($this->request->post['jadloglista_embarcador_numerocontrato']))
            $data['jadloglista_embarcador_numerocontrato'] = $this->request->post['jadloglista_embarcador_numerocontrato'];
        else
            $data['jadloglista_embarcador_numerocontrato'] = $this->config->get('jadloglista_embarcador_numerocontrato');
        
        if (isset($this->request->post['jadloglista_mypudo_service_url']))
            $data['jadloglista_mypudo_service_url'] = $this->request->post['jadloglista_mypudo_service_url'];
        elseif (null != $this->config->get('jadloglista_mypudo_service_url'))
            $data['jadloglista_mypudo_service_url'] = $this->config->get('jadloglista_mypudo_service_url');
        else
            $data['jadloglista_mypudo_service_url'] = 'http://mypudo.pickup-services.com/mypudo/mypudo.asmx/GetPudoList';
        
        if (isset($this->request->post['jadloglista_mypudo_firmid']))
            $data['jadloglista_mypudo_firmid'] = $this->request->post['jadloglista_mypudo_firmid'];
        elseif (null != $this->config->get('jadloglista_mypudo_firmid'))
            $data['jadloglista_mypudo_firmid'] = $this->config->get('jadloglista_mypudo_firmid');
        else
            $data['jadloglista_mypudo_firmid'] = 'JAD';
        
        if (isset($this->request->post['jadloglista_mypudo_key']))
            $data['jadloglista_mypudo_key'] = $this->request->post['jadloglista_mypudo_key'];
        else
            $data['jadloglista_mypudo_key'] = $this->config->get('jadloglista_mypudo_key');
        
        if (isset($this->request->post['jadloglista_jadlog_tracking_url']))
            $data['jadloglista_jadlog_tracking_url'] = $this->request->post['jadloglista_jadlog_tracking_url'];
        elseif (null != $this->config->get('jadloglista_jadlog_tracking_url'))
            $data['jadloglista_jadlog_tracking_url'] = $this->config->get('jadloglista_jadlog_tracking_url');
        else
            $data['jadloglista_jadlog_tracking_url'] = 'http://www.jadlog.com.br/sitejadlog/tracking.jad?cte={TRACKING_ID}';
        
        if (isset($this->request->post['jadloglista_frete_service_url']))
            $data['jadloglista_frete_service_url'] = $this->request->post['jadloglista_frete_service_url'];
        elseif (null != $this->config->get('jadloglista_frete_service_url'))
            $data['jadloglista_frete_service_url'] = $this->config->get('jadloglista_frete_service_url');
        else
            $data['jadloglista_frete_service_url'] = 'http://www.jadlog.com.br/JadlogEdiWs/services/ValorFreteBean?method=valorar';
        
        if (isset($this->request->post['jadloglista_frete_user']))
            $data['jadloglista_frete_user'] = $this->request->post['jadloglista_frete_user'];
        else
            $data['jadloglista_frete_user'] = $this->config->get('jadloglista_frete_user');
        
        if (isset($this->request->post['jadloglista_frete_password']))
            $data['jadloglista_frete_password'] = $this->request->post['jadloglista_frete_password'];
        else
            $data['jadloglista_frete_password'] = $this->config->get('jadloglista_frete_password');
        
        if (isset($this->request->post['jadloglista_sort_order']))
            $data['jadloglista_sort_order'] = $this->request->post['jadloglista_sort_order'];
        elseif (null != $this->config->get('jadloglista_sort_order'))
            $data['jadloglista_sort_order'] = $this->config->get('jadloglista_sort_order');
        else
            $data['jadloglista_sort_order'] = '2';
        
        // ///////
        
//         if (isset($this->request->post['jadloglista_agence']))
//             $data['jadloglista_agence'] = $this->request->post['jadloglista_agence'];
//         else
//             $data['jadloglista_agence'] = $this->config->get('jadloglista_agence');
        
//         if (isset($this->request->post['jadloglista_cargo']))
//             $data['jadloglista_cargo'] = $this->request->post['jadloglista_cargo'];
//         else
//             $data['jadloglista_cargo'] = $this->config->get('jadloglista_cargo');
        
//         if (isset($this->request->post['jadloglista_advalorem']))
//             $data['jadloglista_advalorem'] = $this->request->post['jadloglista_advalorem'];
//         else
//             $data['jadloglista_advalorem'] = $this->config->get('jadloglista_advalorem');
        
//         if (isset($this->request->post['jadloglista_retour']))
//             $data['jadloglista_retour'] = $this->request->post['jadloglista_retour'];
//         else
//             $data['jadloglista_retour'] = $this->config->get('jadloglista_retour');
        
//         if (isset($this->request->post['jadloglista_suppiles']))
//             $data['jadloglista_suppiles'] = $this->request->post['jadloglista_suppiles'];
//         else
//             $data['jadloglista_suppiles'] = $this->config->get('jadloglista_suppiles');
        
//         if (isset($this->request->post['jadloglista_suppmontagne']))
//             $data['jadloglista_suppmontagne'] = $this->request->post['jadloglista_suppmontagne'];
//         else
//             $data['jadloglista_suppmontagne'] = $this->config->get('jadloglista_suppmontagne');
        
//         if (isset($this->request->post['jadloglista_sort_order']))
//             $data['jadloglista_sort_order'] = $this->request->post['jadloglista_sort_order'];
//         else
//             $data['jadloglista_sort_order'] = $this->config->get('jadloglista_sort_order');
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('shipping/jadloglista.tpl', $data));
    }

    protected function validate()
    {
        if (! $this->user->hasPermission('modify', 'shipping/jadloglista'))
            $this->error['warning'] = $this->language->get('error_permission');
        
        if (! $this->error)
            return true;
        else
            return false;
    }
}
?>