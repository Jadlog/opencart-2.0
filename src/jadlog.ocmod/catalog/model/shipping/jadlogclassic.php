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

class ModelShippingJadlogclassic extends Model {
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
        $url = $serviceurlfrete.'&vModalidade=9&Password='.$passwordfrete.'&vSeguro=N&vVlDec=' . $vlDec . '&vVlColeta=&vCepOrig=' . str_replace('-', '', $zipcodeFrom) . '&vCepDest=' . str_replace('-', '', $zipcodeTo) . '&vPeso=' . $peso . '&vFrap=N&vEntrega=D&vCnpj=' . $usuariofrete;
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

    public function getRespError($msgerror) {
        //$this->log->write($msgerror);
        $method_data = array(
            'code'       => 'jadlogclassic_error',
            'title'      => 'Jadlog - Entrega no endereço informado',
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

    public function getQuote($address)
    {
        $this->install();
        //$this->log->write("-- entrega no endereco informado -----------------------------");
        $this->language->load('shipping/jadlogclassic');

         //$this->log->write("address: ".print_r($address, true));

        $zipcodeFrom = str_replace('-','',$this->config->get('jadloglista_cep'));
        //$this->log->write("zipcodeFrom: $zipcodeFrom");
        if ($zipcodeFrom == null || strlen($zipcodeFrom) == 0) {
            return $this->getRespError("Por favor, ajuste o atributo cep nas configurações da Jadlog - Retire em um ponto Pickup.");
        }

        //////////////////////////
        $customer_id = $this->cart->customer->getId();
        //$this->log->write("customer_id: $customer_id");
        $customer_info = $this->getCustomer($customer_id);
        $cpfOrCnpj = $customer_info['cpf_or_cnpj'];
        //$this->log->write("cpfOrCnpj: " . print_r($cpfOrCnpj, true));


        if (null == $cpfOrCnpj || strlen($cpfOrCnpj) == 0) {
            return $this->getRespError("Atributo cpf_or_cnpj inválido.");
        }
        //////////////////////////

        $quote_data = array();
        $weight = $this->cart->getWeight();
        //$this->log->write("weight: $weight");

        $vlDec = $this->cart->getTotal();
        //$this->log->write("vlDec: $vlDec");

        $zipcodeTo = str_replace('-', '', $address['postcode']);
        //$this->log->write("zipcodeTo: $zipcodeTo");
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
        $delivery_cost_str =$this->getPrecoFrete($zipcodeFrom, $zipcodeTo, $vlDec, $pesoTaxado);
        if ($delivery_cost_str == false){
            return $this->getRespError("Resposta inválida do servidor de frete!");
        }
        $delivery_cost_str = str_replace(".", "", $delivery_cost_str);
        $delivery_cost_str = str_replace(",", ".", $delivery_cost_str);
                             //$this->log->write("antes: " . $delivery_cost_str);
        $delivery_cost = floatval($delivery_cost_str);
                             //$this->log->write("depois: " . $delivery_cost);

        $quote_data['jadlogclassic_1'] = array(
            'code' => 'jadlogclassic.jadlogclassic_1',
            'title' => $this->language->get('text_dpdblock'),// . ' (' . $address['country'] . ')',
            'cost' => $delivery_cost,
            'tax_class_id' => $this->config->get('jadlogclassic_tax_class_id'),
            'text' => $this->currency->format($this->tax->calculate($delivery_cost, $this->config->get('jadlogclassic_tax_class_id'), $this->config->get('config_tax')))
        );
        $method_data = array();

        if ($quote_data) {
            $method_data = array(
                'code' => 'jadlogclassic',
                'title' => '<img src="image/data/dpdfrance/front/world/carrier_logo.jpg"/> ' . $this->language->get('text_subtitledpd'),
                'quote' => $quote_data,
                'sort_order' => $this->config->get('jadlogclassic_sort_order'),
                'error' => false
            );
        }

        return $method_data;
    }
}
?>