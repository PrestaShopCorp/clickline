<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    alabazweb.com <tecnico@alabazweb.com>
 *  @copyright 2007-2015 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class ClickLineApi
{

	private $url;
	private $headers;

	public function __construct()
	{
		if (!defined('HOST'))
			define('HOST', Configuration::get('CLICKLINE_URL'));

		if (!defined('PORT'))
			define('PORT', 80);

		if (!defined('PATH'))
			define('PATH', 'includes/api.php');

		$this->url = 'http://'.HOST.'/'.PATH.'?v=3';
		$this->headers = 'HTTP_AUTH_LOGIN: '.Configuration::get('CLICKLINE_ACCOUNT')."\r\n".
				'HTTP_AUTH_PASSWD: '.Configuration::get('CLICKLINE_PASSWORD')."\r\n".
				'Content-Type: text/xml';
	}

	/**
	 * 
	 * Send the request to the WS
	 * @param $xml String XML WS
	 */
	private function sendXml($xml)
	{
		$opts = array(
			'http' => array(
				'method' => 'POST',
				'content' => $xml,
				'timeout' => 30,
				'header' => $this->headers
			)
		);
		$ctx = stream_context_create($opts);
		return Tools::file_get_contents($this->url, true, $ctx);
	}

	/**
	 * 
	 * Balance inquiry available to the client that queries the API.
	 */
	public function getBalanceInquiry()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<packet version="1.0.0.0">
					<action>get_balance_inquiry</action>
				</packet>';

		$document = simplexml_load_string($this->sendXml($xml));
		// Get balance
		return $this->validateAndGetXpath($document, '//balance');
	}

	/**
	 * 
	 * Bring a list of valid countries in the system.
	 */
	public function getCountryList()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<packet version="1.0.0.0">
					<action>get_country_list</action>
				</packet>';

		$document = simplexml_load_string($this->sendXml($xml));
		// Get array of countries
		return $this->validateAndGetXpath($document, '//country');
	}

	/**
	 * 	 
	 * List carriers available in the system	 
	 */
	public function getCarriersList()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<packet version="1.0.0.0">
					<action>get_carriers_list</action>
				</packet>';

		$document = simplexml_load_string($this->sendXml($xml));
		if (empty($document))
			return array();
		// Get array of carriers
		return $this->validateAndGetXpath($document, '//carriers');
	}

	/**
	 * 
	 * List carriers associated with the user account that query.
	 */
	public function getCarriersListCustomer()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<packet version="1.0.0.0">
					<action>get_carriers_list_customer</action>
				</packet>';

		$document = simplexml_load_string($this->sendXml($xml));
		// Get array of carriers
		return $this->validateAndGetXpath($document, '//carriers');
	}

	/**
	 * 
	 * Bring a list of orders placed by the customer.
	 * @param array() $params Array of input parameters order_status, start_date(yyyy-MM-dd), end_date(yyyy-MM-dd)
	 */
	public function getOrders($params)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<packet version="1.0.0.0">
					<action>get_orders</action>
					<order_status>'.$params['order_status'].'</order_status>
					<start_date>'.$params['start_date'].'</start_date>
					<end_date>'.$params['end_date'].'</end_date>
				</packet>';

		$document = simplexml_load_string($this->sendXml($xml));
		// Get array with the customer orders
		return $this->validateAndGetXpath($document, '//orders');
	}

	/**
	 * 
	 * Bring a list of the different status of orders.
	 */
	public function getOrderStatus()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<packet version="1.0.0.0">
					<action>get_order_status</action>
				</packet>';

		$document = simplexml_load_string($this->sendXml($xml));
		// Get array with the status of orders
		return $this->validateAndGetXpath($document, '//orders_status');
	}

	/**
	 * 
	 * Bring the status of an order id.
	 * @param array() $params Array with the input parameter order_id
	 */
	public function getOrdersStatusId($params)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<packet version="1.0.0.0">
					<action>get_orders_status_id</action>
					<order_id>'.$params['order_id'].'</order_id>
				</packet>';

		$document = simplexml_load_string($this->sendXml($xml));
		// Get array with the status of orders
		return $this->validateAndGetXpath($document, '//orders');
	}

	/**
	 * 
	 * Record carriers will use the client.	 
	 * @param array() $params Array with carrier_id of carriers that the client will use.	 
	 */
	public function putCarriers($params)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<packet version="1.0.0.0">
					<action>put_carriers</action>';
		$params_count = count($params);
		for ($i = 0; $i < $params_count; $i++)
		{
			$xml .= '<carriers>
						<carrier_id>'.$params[$i].'</carrier_id>
					 </carriers>';
		}
		$xml .= '</packet>';

		$document = simplexml_load_string($this->sendXml($xml));
		// Get total transporters associated
		return $this->validateAndGetXpath($document, '//total_carriers');
	}

	/**
	 * 	 
	 * Quote transport weights and measures.	 
	 * @param array() $params Array with the following input parameters shipping_cp_to, country_to, packages, 	 
	 * array shippings(shipping_width, shipping_height, shipping_length, shipping_weight)	 
	 */
	public function getShippingQuote($params)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<packet version="1.0.0.0">
					<action>get_shipping_quote</action>
					<shipping_cp_from>'.Configuration::get('CLICKLINE_CP_FROM').'</shipping_cp_from>
					<shipping_cp_to>'.$params['shipping_cp_to'].'</shipping_cp_to>
					<country_from>'.Configuration::get('CLICKLINE_COUNTRY_FROM').'</country_from>
					<country_to>'.$params['country_to'].'</country_to>
					<packages>'.$params['packages'].'</packages>';
		// Get array of shipment data
		$shippings = $params['shippings'];
		$shippings_count = count($shippings);
		for ($i = 0; $i < $shippings_count; $i++)
		{
			$xml .= '<shippings>
						<shipping_width>'.$shippings[$i]['shipping_width'].'</shipping_width>
						<shipping_height>'.$shippings[$i]['shipping_height'].'</shipping_height>
						<shipping_length>'.$shippings[$i]['shipping_length'].'</shipping_length>
						<shipping_weight>'.$shippings[$i]['shipping_weight'].'</shipping_weight>
					 </shippings>';
		}
		$xml .= '</packet>';

		$document = simplexml_load_string($this->sendXml($xml));
		// Get quote results
		return $this->validateAndGetXpath($document, '//shippings');
	}

	/**
	 * 
	 * Quote of free transport measures.	 
	 * @param array() $params Array with the following input parameters: shipping_cp_to, country_to, weight, packages 	 
	 */
	public function getShippingQuoteNoMesure($params)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<packet version="1.0.0.0">
					<action>get_shipping_quote_no_mesure</action>
					<shipping_cp_from>'.Configuration::get('CLICKLINE_CP_FROM').'</shipping_cp_from>
					<shipping_cp_to>'.$params['shipping_cp_to'].'</shipping_cp_to>
					<country_from>'.Configuration::get('CLICKLINE_COUNTRY_FROM').'</country_from>
					<country_to>'.$params['country_to'].'</country_to>
					<weight>'.$params['weight'].'</weight>
					<packages>'.$params['packages'].'</packages>
				</packet>';

		$document = simplexml_load_string($this->sendXml($xml));
		// Get array of shipment data
		return $this->validateAndGetXpath($document, '//shippings');
	}

	/**
	 * This methods help us to check and prevent errors gwhen we want to get a xpath array
	 * @param type $document
	 * @param type $path
	 * @return array
	 */
	private function validateAndGetXpath($document, $path)
	{
		if (!empty($document) && get_class($document) == 'SimpleXMLElement')
		{
			$result = $document->xpath($path);
			if (is_array($result))
				return $result;
		}
		return array();
	}

	/**
	 * 
	 * Record a request to the system, we must first check whether the customer has balance.
	 * @param unknown_type $params
	 */
	public function putOrder($params)
	{
		// To send a request for testing, add the tag �<testmode>1</testmode>� to XML
		// and the system will send a test response
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<packet version="1.0.0.0">';
		$xml .= '	<action>put_order</action>
					<testmode>0</testmode>
					<transporter_id>'.$params['transporter_id'].'</transporter_id>
					<service_id>'.$params['service_id'].'</service_id>
					<service_name>'.$params['service_name'].'</service_name>
			 		<hour_min_collection>'.$params['hour_min_collection'].'</hour_min_collection>
			 		<product_code>'.$params['product_code'].'</product_code>
			 		<payment_method>'.$params['payment_method'].'</payment_method>
			 		<client_ip>'.$params['client_ip'].'</client_ip>
			 		<comments>'.$params['comments'].'</comments>
			 		<code_from>'.Configuration::get('CLICKLINE_COUNTRY_FROM').'</code_from>
			 		<code_to>'.$params['code_to'].'</code_to>';
		$xml .= '<billing>
					 <firstname_billing>'.$params['billing']['firstname_billing'].'</firstname_billing>
					 <lastname_billing>'.$params['billing']['lastname_billing'].'</lastname_billing>
					 <lastname2_billing>'.$params['billing']['lastname2_billing'].'</lastname2_billing>
					 <company_billing>'.$params['billing']['company_billing'].'</company_billing>
					 <taxvat>'.$params['billing']['taxvat'].'</taxvat>
					 <road_type_billing>'.$params['billing']['road_type_billing'].'</road_type_billing>
					 <street_billing>'.$params['billing']['street_billing'].'</street_billing>
					 <road_number_billing>'.$params['billing']['road_number_billing'].'</road_number_billing>
					 <portal_billing>'.$params['billing']['portal_billing'].'</portal_billing>
					 <floor_billing>'.$params['billing']['floor_billing'].'</floor_billing>
					 <door_billing>'.$params['billing']['door_billing'].'</door_billing>
					 <postcode_billing>'.$params['billing']['postcode_billing'].'</postcode_billing>
					 <city_billing>'.$params['billing']['city_billing'].'</city_billing>
					 <country_billing>'.$params['billing']['country_billing'].'</country_billing>
					 <telephone_billing>'.$params['billing']['telephone_billing'].'</telephone_billing>
					 <fax_billing>'.$params['billing']['fax_billing'].'</fax_billing>
					 <email_billing>'.$params['billing']['email_billing'].'</email_billing>
				 </billing>';
		$xml .= '<from>
					 <firstname_from>'.$params['from']['firstname_from'].'</firstname_from>
					 <lastname_from>'.$params['from']['lastname_from'].'</lastname_from>
					 <lastname2_from>'.$params['from']['lastname2_from'].'</lastname2_from>
					 <company_from>'.$params['from']['company_from'].'</company_from>
					 <road_type_from>'.$params['from']['road_type_from'].'</road_type_from>
					 <street_from>'.$params['from']['street_from'].'</street_from>
					 <road_number_from>'.$params['from']['road_number_from'].'</road_number_from>
					 <portal_from>'.$params['from']['portal_from'].'</portal_from>
					 <floor_from>'.$params['from']['floor_from'].'</floor_from>
					 <door_from>'.$params['from']['door_from'].'</door_from>
					 <postcode_from>'.$params['from']['postcode_from'].'</postcode_from>
					 <city_from>'.$params['from']['city_from'].'</city_from>
					 <country_from>'.$params['from']['country_from'].'</country_from>
					 <telephone_from>'.$params['from']['telephone_from'].'</telephone_from>
					 <fax_from>'.$params['from']['fax_from'].'</fax_from>
					 <email_from>'.$params['from']['email_from'].'</email_from>
					 <data_collection_from>'.$params['from']['data_collection_from'].'</data_collection_from>
					 <hour_range_from>'.$params['from']['hour_range_from'].'</hour_range_from>
					 <merchandise_value>'.$params['from']['merchandise_value'].'</merchandise_value>
					 <merchandise_desc>'.$params['from']['merchandise_desc'].'</merchandise_desc>
					 <merchandise_type>'.$params['from']['merchandise_type'].'</merchandise_type>
				</from>';
		$xml .= '<to>
						 <firstname_to>'.$params['to']['firstname_to'].'</firstname_to>
						 <lastname_to>'.$params['to']['lastname_to'].'</lastname_to>
						 <lastname2_to>'.$params['to']['lastname2_to'].'</lastname2_to>
						 <company_to>'.$params['to']['company_to'].'</company_to>
						 <road_type_to>'.$params['to']['road_type_to'].'</road_type_to>
						 <street_to>'.$params['to']['street_to'].'</street_to>
						 <road_number_to>'.$params['to']['road_number_to'].'</road_number_to>
						 <portal_to>'.$params['to']['portal_to'].'</portal_to>
						 <floor_to>'.$params['to']['floor_to'].'</floor_to>
						 <door_to>'.$params['to']['door_to'].'</door_to>
						 <postcode_to>'.$params['to']['postcode_to'].'</postcode_to>
						 <city_to>'.$params['to']['city_to'].'</city_to>
						 <country_to>'.$params['to']['country_to'].'</country_to>
						 <telephone_to>'.$params['to']['telephone_to'].'</telephone_to>
						 <fax_to>'.$params['to']['fax_to'].'</fax_to>
						 <email_to>'.$params['to']['email_to'].'</email_to>
					 </to>';
		$xml .= '<packages>
						 <width>'.$params['packages']['width'].'</width>
						 <height>'.$params['packages']['height'].'</height>
						 <length>'.$params['packages']['length'].'</length>
						 <weight>'.$params['packages']['weight'].'</weight>
						 <charge>'.$params['packages']['charge'].'</charge>
						 <bultos>'.$params['packages']['bultos'].'</bultos>
						 <taxes_percentage>'.$params['packages']['taxes_percentage'].'</taxes_percentage>
					 </packages>';
		$xml .= '</packet>';

		$document = simplexml_load_string($this->sendXml($xml));
		// Get results
		return array('order' => $document->status->order,
			'proforma_pdf' => $document->status->proforma_pdf,
			'consignment_pdf' => $document->status->consignment_pdf,
			'ticket' => $this->validateAndGetXpath($document, '//ticket'));
	}

}

?>
