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
if (!defined('_PS_VERSION_'))
	exit;

require_once(_PS_MODULE_DIR_.'clickline/clickline_api.php');

if (!class_exists('clicklinecart.class', false))
	include(_PS_MODULE_DIR_.'clickline'.DIRECTORY_SEPARATOR.'clicklinecart.class.php');

if (!class_exists('clicklineorder.class', false))
	include(_PS_MODULE_DIR_.'clickline'.DIRECTORY_SEPARATOR.'clicklineorder.class.php');

class ClickLine extends CarrierModule
{

	public $id_carrier;
	private $html = '';
	private $post_errors = array();
	private $fields_list = array();
	private $user_data = array();
	private $clcart = null;
	private $products = null;

	/**
	 * 
	 * Construct Method
	 */
	public function __construct()
	{
		$this->name = 'clickline';
		$this->tab = 'shipping_logistics';
		$this->version = '1.3.8';
		$this->author = 'alabazweb.com';
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('ClickLine');
		$this->description = $this->l('Offer your customers, different delivery methods');
		$this->_lang = (!is_object($this->context->language)) ? (int) Configuration::get('PS_LANG_DEFAULT') : (int) $this->context->language->id;

		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);

		if (self::isInstalled($this->name))
// Loading Var
			$this->loadingVar();
	}

	/**
	 * 
	 * Load configuration vars
	 */
	public function loadingVar()
	{
// Loading Fields List
		$fields_to_load = array(
			'CLICKLINE_ACCOUNT',
			'CLICKLINE_PASSWORD',
			'CLICKLINE_URL',
			'CLICKLINE_CP_FROM',
			'CLICKLINE_COUNTRY_FROM',
			'CLICKLINE_CARRIER_DEF',
			'CLICKLINE_SHOP_NAME',
			'CLICKLINE_FIRST_NAME',
			'CLICKLINE_LAST_NAME',
			'CLICKLINE_LAST_NAME_2',
			'CLICKLINE_STREET',
			'CLICKLINE_ROAD_NUMBER',
			'CLICKLINE_PORTAL',
			'CLICKLINE_FLOOR',
			'CLICKLINE_DOOR',
			'CLICKLINE_POSTCODE',
			'CLICKLINE_CITY',
			'CLICKLINE_COUNTRY',
			'CLICKLINE_TELEPHONE',
			'CLICKLINE_FAX',
			'CLICKLINE_EMAIL',
			'CLICKLINE_APPLY_DISCOUNT'
		);

		$this->fields_list = Configuration::getMultiple($fields_to_load);
	}

	/**
	 * 
	 * Install Method
	 */
	public function install()
	{
// Register the Clickline carrier
		$carrier_config = array('name' => 'Clickline',
			'id_tax_rules_group' => 0,
			'active' => true,
			'deleted' => 0,
			'shipping_handling' => false,
			'range_behavior' => 0,
			'delay' => array(Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')) => '24/48 horas'),
			'id_zone' => 1,
			'is_module' => true,
			'shipping_external' => true,
			'external_module_name' => $this->name,
			'need_range' => true
		);

		$id_carrier = $this->installExternalCarrier($carrier_config);
		Configuration::updateValue('CLICKLINE_CARRIER_ID', (int) $id_carrier);

// Install Module
		if (!parent::install() || !CLCartClass::installDB() ||
				!$this->registerHook('updateCarrier') ||
				!$this->registerHook('adminOrder') ||
				!$this->registerHook('extraCarrier') ||
				!$this->registerHook('backOfficeHeader') ||
				!$this->registerHook('header') ||
				!Configuration::updateValue('CLICKLINE_ACCOUNT', '') ||
				!Configuration::updateValue('CLICKLINE_PASSWORD', '') ||
				!Configuration::updateValue('CLICKLINE_URL', 'www.clickline.com') ||
				!Configuration::updateValue('CLICKLINE_CP_FROM', '') ||
				!Configuration::updateValue('CLICKLINE_COUNTRY_FROM', '') ||
				!Configuration::updateValue('CLICKLINE_CARRIER_DEF', '') ||
				!Configuration::updateValue('CLICKLINE_APPLY_DISCOUNT', 0))
			return false;
		return true;
	}

	/**
	 * 
	 * Register a carrier
	 * @param array() $config
	 */
	public static function installExternalCarrier($config)
	{
		$carrier = new Carrier();
		$carrier->name = $config['name'];
		$carrier->id_tax_rules_group = $config['id_tax_rules_group'];
		$carrier->id_zone = $config['id_zone'];
		$carrier->active = $config['active'];
		$carrier->deleted = $config['deleted'];
		$carrier->delay = $config['delay'];
		$carrier->shipping_handling = $config['shipping_handling'];
		$carrier->range_behavior = $config['range_behavior'];
		$carrier->is_module = $config['is_module'];
		$carrier->shipping_external = $config['shipping_external'];
		$carrier->external_module_name = $config['external_module_name'];
		$carrier->need_range = $config['need_range'];

		$languages = Language::getLanguages(true);
		foreach ($languages as $language)
		{
			if ($language['iso_code'] == 'fr' && isset($config['delay'][$language['iso_code']]))
				$carrier->delay[(int) $language['id_lang']] = $config['delay'][$language['iso_code']];
			elseif ($language['iso_code'] == 'en' && isset($config['delay'][$language['iso_code']]))
				$carrier->delay[(int) $language['id_lang']] = $config['delay'][$language['iso_code']];
			elseif ($language['iso_code'] == Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')) && isset($config['delay'][$language['iso_code']]))
				$carrier->delay[(int) $language['id_lang']] = $config['delay'][$language['iso_code']];
			else
			{ //Default
				if (isset($config['delay']['en']))
					$delay = $config['delay']['en'];
				elseif (count($config['delay']) > 0)
				{
					$array_values = array_values($config['delay']);
					$delay = $array_values[0];
				}
				$carrier->delay[(int) $language['id_lang']] = $delay;
			}
		}

		if ($carrier->add())
		{
			$groups = Group::getGroups(true);
			foreach ($groups as $group)
			{
				Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'carrier_group(`id_carrier`,`id_group`) 
		VALUES ('.(int) $carrier->id.', '.(int) $group['id_group'].')');
			}

			$range_price = new RangePrice();
			$range_price->id_carrier = $carrier->id;
			$range_price->delimiter1 = '0';
			$range_price->delimiter2 = '1000000000';
			$range_price->add();

			$range_weight = new RangeWeight();
			$range_weight->id_carrier = $carrier->id;
			$range_weight->delimiter1 = '0';
			$range_weight->delimiter2 = '1000000000';
			$range_weight->add();

			$zones = Zone::getZones(true);
			foreach ($zones as $zone)
			{
				Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'carrier_zone(`id_carrier`,`id_zone`) 
		VALUES ('.(int) $carrier->id.', '.(int) $zone['id_zone'].')');

				Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'delivery(`id_carrier`,`id_range_price`,`id_range_weight`,`id_zone`,`price`) 
	VALUES ('.(int) $carrier->id.', '.(int) $range_price->id.', NULL, '.(int) $zone['id_zone'].', 0)');

				Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'delivery(`id_carrier`,`id_range_price`,`id_range_weight`,`id_zone`,`price`) 
	VALUES ('.(int) $carrier->id.', NULL, '.(int) $range_weight->id.', '.(int) $zone['id_zone'].', 0)');
			}

// Return ID Carrier
			return (int) $carrier->id;
		}

		return false;
	}

	/**
	 * 
	 * Uninstall Method
	 */
	public function uninstall()
	{
// Uninstall Config
		foreach ($this->fields_list as $key_configuration)
			Configuration::deleteByName($key_configuration);

// Delete External Carrier
		$carrier = new Carrier((int) Configuration::get('CLICKLINE_CARRIER_ID'));

// If external carrier is default set other one as default
		if (Configuration::get('PS_CARRIER_DEFAULT') == (int) $carrier->id)
		{

			$carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
			foreach ($carriers as $carrier_default)
				if ($carrier_default['active'] && !$carrier_default['deleted'] && ($carrier_default['name'] != Configuration::get('CLICKLINE_CARRIER_DEF')))
				{
					Configuration::updateValue('PS_CARRIER_DEFAULT', $carrier_default['id_carrier']);
					break;
				}
		}

// Then delete Carrier
		$carrier->deleted = 1;

		if (!$carrier->update())
			return false;

// Uninstall Module
		if (!parent::uninstall() ||
				!$this->unregisterHook('updateCarrier') ||
				!$this->unregisterHook('adminOrder') ||
				!$this->unregisterHook('extraCarrier') ||
				!$this->unregisterHook('header'))
			return false;

		return true;
	}

	/**
	 * 
	 * getContent Method
	 */
	public function getContent()
	{
		$this->postProcess();
		$this->displayForm();
		return $this->html;
	}

	private function renderForm()
	{
//Get all config values
		$config_values = Array(
			'clickline_account' => (isset($this->fields_list['CLICKLINE_ACCOUNT']) ? $this->fields_list['CLICKLINE_ACCOUNT'] : ''),
			'clickline_password' => (isset($this->fields_list['CLICKLINE_PASSWORD']) ? $this->fields_list['CLICKLINE_PASSWORD'] : ''),
			'clickline_cp_from' => (isset($this->fields_list['CLICKLINE_CP_FROM']) ? $this->fields_list['CLICKLINE_CP_FROM'] : ''),
			'clickline_country_from' => (isset($this->fields_list['CLICKLINE_COUNTRY_FROM']) ? $this->fields_list['CLICKLINE_COUNTRY_FROM'] : ''),
			'clickline_carrier_def' => (isset($this->fields_list['CLICKLINE_CARRIER_DEF']) ? $this->fields_list['CLICKLINE_CARRIER_DEF'] : 0),
			'apply_discount' => (int) Configuration::get('CLICKLINE_APPLY_DISCOUNT'),
			'shop_name' => (isset($this->fields_list['CLICKLINE_SHOP_NAME']) ? $this->fields_list['CLICKLINE_SHOP_NAME'] : ''),
			'first_name' => (isset($this->fields_list['CLICKLINE_FIRST_NAME']) ? $this->fields_list['CLICKLINE_FIRST_NAME'] : ''),
			'last_name' => (isset($this->fields_list['CLICKLINE_LAST_NAME']) ? $this->fields_list['CLICKLINE_LAST_NAME'] : ''),
			'last_name_2' => (isset($this->fields_list['CLICKLINE_LAST_NAME_2']) ? $this->fields_list['CLICKLINE_LAST_NAME_2'] : ''),
			'street' => (isset($this->fields_list['CLICKLINE_STREET']) ? $this->fields_list['CLICKLINE_STREET'] : ''),
			'road_number' => (isset($this->fields_list['CLICKLINE_ROAD_NUMBER']) ? $this->fields_list['CLICKLINE_ROAD_NUMBER'] : ''),
			'portal' => (isset($this->fields_list['CLICKLINE_PORTAL']) ? $this->fields_list['CLICKLINE_PORTAL'] : ''),
			'floor' => (isset($this->fields_list['CLICKLINE_FLOOR']) ? $this->fields_list['CLICKLINE_FLOOR'] : ''),
			'door' => (isset($this->fields_list['CLICKLINE_DOOR']) ? $this->fields_list['CLICKLINE_DOOR'] : ''),
			'postcode' => (isset($this->fields_list['CLICKLINE_POSTCODE']) ? $this->fields_list['CLICKLINE_POSTCODE'] : ''),
			'city' => (isset($this->fields_list['CLICKLINE_CITY']) ? $this->fields_list['CLICKLINE_CITY'] : ''),
			'country' => (isset($this->fields_list['CLICKLINE_COUNTRY']) ? $this->fields_list['CLICKLINE_COUNTRY'] : 0),
			'telephone' => (isset($this->fields_list['CLICKLINE_TELEPHONE']) ? $this->fields_list['CLICKLINE_TELEPHONE'] : ''),
			'fax' => (isset($this->fields_list['CLICKLINE_FAX']) ? $this->fields_list['CLICKLINE_FAX'] : ''),
			'email' => (isset($this->fields_list['CLICKLINE_EMAIL']) ? $this->fields_list['CLICKLINE_EMAIL'] : ''),
		);
//Prepare the carriers list
		$carrier_list = array();
		$carrier_list[] = array('carrier_id' => 0, 'carrier' => $this->l('None'));
		if (isset($this->fields_list['CLICKLINE_ACCOUNT']))
		{
// Get carrier list from WS
// Create ClickLine_api Object
			$clickline = new ClickLineApi();

// Open connection and call WS
			$carrier_list_to_add = $clickline->getCarriersList();
			$carrier_list = array_merge($carrier_list, $carrier_list_to_add);
		}

// Get countries
		$country_list = array();
		$country_list[] = array('id_country' => '0', 'name' => $this->l('Choose your country'));
		$country_list = array_merge($country_list, Country::getCountries($this->_lang));


//Lets go, gen the form
//<editor-fold defaultstate="collapsed" desc="Form's template">

		$fields_form = array();
		$fields_form[] = array('form' => array(
				'legend' => array(
					'title' => $this->l('ClickLine Module Configuration'),
					'image' => $this->_path.'logo.gif'
				),
				'input' => array(
					array(
						'type' => 'text',
						'name' => 'clickline_account',
						'required' => true,
						'label' => $this->l('User'),
						'desc' => $this->l('User of Clickline account'),
						'size' => 32
					),
					array(
						'type' => 'password',
						'name' => 'clickline_password',
						'required' => true,
						'label' => $this->l('Password'),
						'desc' => $this->l('Password of Clickline account'),
						'size' => 32
					),
					array(
						'type' => 'text',
						'name' => 'clickline_cp_from',
						'required' => true,
						'label' => $this->l('CP From'),
						'desc' => $this->l('ZIP code where the order is sent'),
						'size' => 32
					),
					array(
						'type' => 'text',
						'name' => 'clickline_country_from',
						'required' => true,
						'label' => $this->l('Country From'),
						'desc' => $this->l('Country code where the order is sent, for example: ES for Spain'),
						'size' => 32
					),
					array(
						'type' => 'select',
						'name' => 'clickline_carrier_def',
						'required' => true,
						'label' => $this->l('Default carrier'),
						'desc' => $this->l('Carrier that will be selected by default at Front Office'),
						'options' => array(
							'query' => $carrier_list,
							'id' => 'carrier_id',
							'name' => 'carrier'
						)
					),
					array(
						'type' => 'radio',
						'name' => 'apply_discount',
						'required' => true,
						'class' => 't',
						'is_bool' => true,
						'label' => $this->l('Send measures'),
						'desc' => $this->l('Send measures from the Front Office of the store (in the product must indicate their size and weight)'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						)
					)
				)
				,
				'submit' => array(
					'name' => 'submitSave',
					'title' => $this->l('Save'),
					'class' => 'button'
				)
		));

		$fields_form[] = array('form' => array(
				'legend' => array(
					'title' => $this->l('Shop Information'),
					'image' => $this->_path.'logo.gif'
				),
				'input' => array(
					array(
						'type' => 'text',
						'name' => 'shop_name',
						'label' => $this->l('Shop name'),
						'size' => 45
					),
					array(
						'type' => 'text',
						'name' => 'first_name',
						'label' => $this->l('First Name'),
						'size' => 45
					),
					array(
						'type' => 'text',
						'name' => 'last_name',
						'label' => $this->l('Last Name'),
						'size' => 45
					),
					array(
						'type' => 'text',
						'name' => 'last_name_2',
						'label' => $this->l('Last Name 2'),
						'size' => 45
					),
					array(
						'type' => 'text',
						'name' => 'street',
						'label' => $this->l('Street'),
						'size' => 45
					),
					array(
						'type' => 'text',
						'name' => 'road_number',
						'label' => $this->l('Road number'),
						'size' => 45
					),
					array(
						'type' => 'text',
						'name' => 'portal',
						'label' => $this->l('Portal'),
						'size' => 45
					),
					array(
						'type' => 'text',
						'name' => 'floor',
						'label' => $this->l('Floor'),
						'size' => 45
					),
					array(
						'type' => 'text',
						'name' => 'door',
						'label' => $this->l('Door'),
						'size' => 45
					),
					array(
						'type' => 'text',
						'name' => 'postcode',
						'label' => $this->l('Postcode'),
						'size' => 45
					),
					array(
						'type' => 'text',
						'name' => 'city',
						'label' => $this->l('City'),
						'size' => 45
					),
					array(
						'type' => 'select',
						'name' => 'country',
						'label' => $this->l('Country'),
						'options' => array(
							'query' => $country_list,
							'id' => 'id_country',
							'name' => 'name'
						)
					),
					array(
						'type' => 'text',
						'name' => 'telephone',
						'label' => $this->l('Telephone'),
						'size' => 45
					),
					array(
						'type' => 'text',
						'name' => 'fax',
						'label' => $this->l('Fax'),
						'size' => 45
					),
					array(
						'type' => 'text',
						'name' => 'email',
						'label' => $this->l('Email'),
						'size' => 45
					)
				)
				,
				'submit' => array(
					'name' => 'submitSave',
					'title' => $this->l('Save'),
					'class' => 'button'
				)
		));
//</editor-fold>

		$helper = new HelperForm();
		$helper->module = $this;
		$helper->token = Tools::getAdminTokenLite('AdminModules'); //Security Token
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name; //Current url
		$helper->fields_value = $config_values;
		$helper->show_toolbar = false; //Hide the toolbar
		$helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT'); //And set the default language
		$options = $helper->generateForm($fields_form);

		return $options;
	}

	/**
	 * 
	 * displayform Method
	 */
	private function displayForm()
	{
		$this->context->smarty->assign(array(
			'logo_src' => $this->_path,
			'apply_discount' => Configuration::get('CLICKLINE_APPLY_DISCOUNT'),
			'ps_16' => version_compare(_PS_VERSION_, '1.6', '>='),
			'main_form' => $this->renderForm()
		));
		$this->html .= ($this->display(__FILE__, '/views/templates/admin/clickline_configuration.tpl'));
	}

	public function hookupdateCarrier($params)
	{
		if ((int) $params['id_carrier'] == (int) Configuration::get('CLICKLINE_CARRIER_ID'))
			Configuration::updateValue('CLICKLINE_CARRIER_ID', (int) $params['carrier']->id);
	}

	public function validateOrder()
	{
		if (trim(Tools::getvalue('height')) == '' ||
				trim(Tools::getvalue('width')) == '' ||
				trim(Tools::getvalue('depth')) == '' ||
				trim(Tools::getvalue('data_collection_from')) == '' ||
				trim(Tools::getvalue('hour_range_from')) == '' ||
				trim(Tools::getvalue('merchandise_desc')) == '' ||
				trim(Tools::getvalue('street_to')) == '' ||
				trim(Tools::getvalue('road_number_to')) == '')
			return false;

		return true;
	}

	public function hookadminOrder($params)
	{
		$smarty = $this->context->smarty;

		$order = new Order($params['id_order']);
		$clcart = null;

		if ((int) $order->id_carrier == (int) Configuration::get('CLICKLINE_CARRIER_ID'))
			$clcart = new CLCartClass($order->id_cart);
		else
			return '';
			
		Configuration::updateValue('CLICKLINE_CONFIGURATION_OK', true);

		$this->postProcess();
// Consult to WS the Clickline's account available balance
// Create ClickLine_api Object
		$clickline = new ClickLineApi();

		$response = $clickline->getBalanceInquiry();

// Get Clickline's selected carrier
		$id_cart = Order::getCartIdStatic($params['id_order']);
		$clorder = new CLOrderClass($params['id_order']);

// Get delivery address
		$id_address_delivery = $order->id_address_delivery;
		$address_delivery = new Address($id_address_delivery);

// Get Cart Object
		$cart = new Cart($id_cart);

// Package size
		if (Configuration::get('CLICKLINE_APPLY_DISCOUNT'))
			$products = $this->validateMeasures($cart);
		else
			$products = false;

		$width = 0;
		$height = 0;
		$depth = 0;

		if (!$products)
		{
			$width = 14;
			$height = 10;
			$depth = 15;
		}
		else
		{
			foreach ($products as $product)
			{
				$width += (float) $product['shipping_width'];
				$height += (float) $product['shipping_height'];
				$depth += (float) $product['shipping_length'];
			}
		}

		$clorder_sm = null;
		if (count($clorder->config) != 0)
			$clorder_sm = $clorder->config;

		$clorderws_sm = null;
		if (count($clorder->config_ws) != 0)
			$clorderws_sm = $clorder->config_ws;

		$smarty->assign(array(
			'path' => $this->_path,
			'errors' => $this->post_errors,
			'request_uri' => Tools::safeOutput($_SERVER['REQUEST_URI']),
			'id_cart' => $id_cart,
			'clorder' => $clorder_sm,
			'clcart' => $clcart->config,
			'balance' => Tools::displayPrice($response[0]),
			'weight' => $order->getTotalWeight(),
			'height' => $height,
			'width' => $width,
			'depth' => $depth,
			'products' => $products,
			'clorderWS' => $clorderws_sm,
			'address1' => $address_delivery->address1,
			'address2' => $address_delivery->address2,
			'params' => array(
				'data_collection_from' => Tools::getValue('data_collection_from'),
				'hour_range_from' => Tools::getValue('hour_range_from'),
				'merchandise_desc' => Tools::getValue('merchandise_desc'),
				'street_to' => Tools::getValue('street_to'),
				'road_number_to' => Tools::getValue('road_number_to'),
				'portal_to' => Tools::getValue('portal_to'),
				'floor_to' => Tools::getValue('floor_to'),
				'door_to' => Tools::getValue('door_to'))
		));
		return $this->display(__FILE__, 'views/templates/hook/admin_order.tpl');
	}

	public function processCarrier($params)
	{
// Get Cart Object
		$cart_params = $params['cart'];

// Get selected carrier params
		$carrier_id = (int) Tools::getValue('clickline_id_carrier');
		$carrier_code = Tools::getValue('clickline_code_carrier');
		$carrier_name = Tools::getValue('clickline_name_carrier');
		$carrier_id_service = Tools::getValue('clickline_id_service');
		$carrier_price = Tools::getValue('clickline_price');
		$carrier_tax = Tools::getValue('clickline_shipping_tax');

// Get country, PC from customer and cart weight
		$user_data = $this->getCountryAndCP($cart_params);
		$weight = $cart_params->getTotalWeight();

// Create CLCart Object
		$clcart = new CLCartClass($cart_params->id);

// Set values
		$clcart->config['id_shipping_carrier'] = $carrier_id;
		$clcart->config['id_shipping_service'] = $carrier_id_service;
		$clcart->config['shipping_charge'] = $carrier_price;
		$clcart->config['shipping_code_carrier'] = $carrier_code;
		$clcart->config['shipping_name_carrier'] = $carrier_name;
		$clcart->config['shipping_tax'] = $carrier_tax;
		$clcart->config['weight'] = $weight;
		$clcart->config['cp'] = $user_data['cp'];
		$clcart->config['country_code'] = $user_data['country_code'];

// Add a registry on BBDD with the selected carrier information
		if (!$clcart->exist)
		{
			if ($clcart->addSql())
				$clcart->exist = true;
		}
		else
			$clcart->updateSql();
	}

	public function hookextraCarrier($params)
	{
		$smarty = $this->context->smarty;

		$cart = $params['cart'];
		$results_array = array();
		$i = 0;

// Crete CLCart Object
		if (!isset($this->clcart))
			$this->clcart = new CLCartClass($cart->id);

		if (count($this->clcart->config_ws) > 0)
		{

			$clickline_carrier = (int) Configuration::get('CLICKLINE_CARRIER_ID');
			if ((int) $cart->id_carrier == $clickline_carrier)
			{
// Get ClickLine's carriers information
				foreach ($this->clcart->config_ws as $trans)
				{
					$row = array();
					$row['price'] = number_format((float) $trans['shipping_charge'], 2, '.', '');
					$row['id_carrier'] = $trans['shipping_id_carrier'];
					$row['name'] = $trans['shipping_name_carrier'];
					$row['code_carrier'] = $trans['shipping_code_carrier'];
					$row['id_service'] = $trans['shipping_id_service'];
					$row['shipping_tax'] = $trans['shipping_tax'];
					$row['logo'] = $trans['shipping_logo'];
					$row['price_with_tax'] = number_format(
							(float) $trans['shipping_charge'] + ((float) $trans['shipping_charge'] * (float) $trans['shipping_tax'] / 100), 2, '.', ''
					);

					$results_array[$i] = $row;
					$i++;
				}

// Get cart weight
				$weight = $cart->getTotalWeight();
				$weight < 1 ? $weight = 1 : null;

// Check if a carrier was selected for this cart previously
				$carrier_selected = (
						((int) $cart->id_carrier == (int) Configuration::get('CLICKLINE_CARRIER_ID')) ? (
								isset($this->clcart->config['id_shipping_carrier']) ? $this->clcart->config['id_shipping_carrier'] : ''
								) : ''
						);
				$carrier_service_selected = (
						((int) $cart->id_carrier == (int) Configuration::get('CLICKLINE_CARRIER_ID')) ? (
								isset($this->clcart->config['id_shipping_service']) ? $this->clcart->config['id_shipping_service'] : ''
								) : ''
						);

				$carrier_selected = ((isset($this->clcart->config['id_shipping_carrier'])) ? $this->clcart->config['id_shipping_carrier'] : '');
				$carrier_service_selected = ((isset($this->clcart->config['id_shipping_service'])) ? $this->clcart->config['id_shipping_service'] : '');

// Get PC - Country from and to for Header
				$usuario_direccion_id = $cart->id_address_delivery;
				$query = 'SELECT * FROM '._DB_PREFIX_.'address where id_address = "'.(int)$usuario_direccion_id.'"';
				$usuario_datos = Db::getInstance()->ExecuteS($query);
				$country_to = CountryCore::getNameById($this->_lang, $usuario_datos[0]['id_country']);

// Asign smarty vars
				$smarty->assign(array(
					'djlCarriers' => $results_array,
					'carrier_sel' => $carrier_selected,
					'service_sel' => $carrier_service_selected,
					'cp_to' => $usuario_datos[0]['postcode'],
					'cp_from' => Configuration::get('CLICKLINE_CP_FROM'),
					'country_to' => $country_to,
					'country_from' => Configuration::get('PS_SHOP_COUNTRY'),
					'weight' => $weight,
					'clickline_module_dir' => $this->_path,
					'ps15' => (version_compare(_PS_VERSION_, '1.5', '>=') && version_compare(_PS_VERSION_, '1.6', '<'))
				));

				return $this->display(__FILE__, '/views/templates/front/clickline_carrier.tpl');
			}
		}

		return '';
	}

	private function postProcess()
	{
		try
		{
			if (Tools::isSubmit('submitSave'))
			{
				$account = trim(Tools::getvalue('clickline_account'));
				$cp_from = Tools::getvalue('clickline_cp_from');
				$country_from = trim(Tools::getvalue('clickline_country_from'));
				if (empty($account) || empty($cp_from) || empty($country_from))
				{
					throw new Exception($this->l('Please enter data for mandatory fields'));
				}
// Update configuration vars
				$clicklineURL = trim(Tools::getvalue('clickline_url'));
				Configuration::updateValue('CLICKLINE_URL', (empty($clicklineURL) ? 'www.clickline.com' : $clicklineURL));
				Configuration::updateValue('CLICKLINE_ACCOUNT', $account);
				$clicklinePassword = Tools::getvalue('clickline_password');
				if (!empty($clicklinePassword))
					Configuration::updateValue('CLICKLINE_PASSWORD', $clicklinePassword);
				Configuration::updateValue('CLICKLINE_CP_FROM', $cp_from);
				if ($country_from == 'españa' || $country_from == 'España')
					$country_from = 'es';
				Configuration::updateValue('CLICKLINE_COUNTRY_FROM', $country_from);
				Configuration::updateValue('CLICKLINE_CARRIER_DEF', Tools::getvalue('clickline_carrier_def'));
				Configuration::updateValue('CLICKLINE_APPLY_DISCOUNT', Tools::getValue('apply_discount'));

				Configuration::updateValue('CLICKLINE_SHOP_NAME', Tools::getvalue('shop_name'));
				Configuration::updateValue('CLICKLINE_FIRST_NAME', Tools::getvalue('first_name'));
				Configuration::updateValue('CLICKLINE_LAST_NAME', Tools::getvalue('last_name'));
				Configuration::updateValue('CLICKLINE_LAST_NAME_2', Tools::getvalue('last_name_2'));
				Configuration::updateValue('CLICKLINE_STREET', Tools::getvalue('street'));
				Configuration::updateValue('CLICKLINE_ROAD_NUMBER', Tools::getvalue('road_number'));
				Configuration::updateValue('CLICKLINE_PORTAL', Tools::getvalue('portal'));
				Configuration::updateValue('CLICKLINE_FLOOR', Tools::getvalue('floor'));
				Configuration::updateValue('CLICKLINE_DOOR', Tools::getvalue('door'));
				Configuration::updateValue('CLICKLINE_POSTCODE', Tools::getvalue('postcode'));
				Configuration::updateValue('CLICKLINE_CITY', Tools::getvalue('city'));
				Configuration::updateValue('CLICKLINE_COUNTRY', Tools::getvalue('country'));
				Configuration::updateValue('CLICKLINE_TELEPHONE', Tools::getvalue('telephone'));
				Configuration::updateValue('CLICKLINE_FAX', Tools::getvalue('fax'));
				Configuration::updateValue('CLICKLINE_EMAIL', Tools::getvalue('email'));

//We need to refresh the properties with the new data
				$this->loadingVar();

				$carrier_default = Tools::getvalue('clickline_carrier_def');
				if ($this->fields_list['CLICKLINE_CARRIER_DEF'] == null && empty($carrier_default))
				{
//If is the first time and carrier_def is undefined, the module will choose a default carrier
// Create ClickLine_api Object
					$clickline = new ClickLineApi();
// Open connection and call WS
					$carrier_list = $clickline->getCarriersList();
					if (count($carrier_list) > 0)
					{
						$carrier_id = (string) $carrier_list[0]->carrier_id;
						$this->fields_list['CLICKLINE_CARRIER_DEF'] = $carrier_id;
						Configuration::updateValue('CLICKLINE_CARRIER_DEF', $carrier_id);
					}
				}

				$this->html .= $this->displayConfirmation($this->l('ClickLine Module Configuration has been updated.'));
			}
			elseif (Tools::isSubmit('submitClicklineInfo'))
			{
				if ($this->validateOrder())
					$this->submitClicklineInfo();
				else
					$this->post_errors[0] = $this->l('You must specify all fields.');
			}
		}
		catch (Exception $ex)
		{
			$this->html .= $this->displayError($ex->getMessage());
		}
	}

	private function submitClicklineInfo()
	{
		$id_cart = Tools::getvalue('id_cart');

// Get order
		$id_order = OrderCore::getOrderByCartId((int) $id_cart);
		$order = new Order($id_order);

// Customer Info
		$customer = new Customer($order->id_customer);

// Get delivery address
		$id_address_delivery = $order->id_address_delivery;
		$address_delivery = new Address($id_address_delivery);

// Get customer's delivery lastname
		$apellidos = explode(' ', trim($address_delivery->lastname));
		$lastname_to = '';
		$lastname2_to = '';
		$cant_apellidos = count($apellidos);
		for ($i = 0; $i < $cant_apellidos; $i++)
		{
			if ($i == 0)
				$lastname_to = $apellidos[$i];
			else
				$lastname2_to .= $apellidos[$i].' ';
		}

// Get country ISO codes
		$country_to = Country::getIsoById($address_delivery->id_country);

// Create ClickLine_api Object
		$clickline = new ClickLineApi();

// Get Cart
		$cart = new Cart($id_cart);

		$transporter_id = (int) Tools::getvalue('id_shipping_carrier');
		$service_id = (int) Tools::getvalue('id_shipping_service');
		$service_name = Tools::getvalue('shipping_name_carrier');
		$product_code = Tools::getvalue('shipping_code_carrier');
		$charge = (float) Tools::getvalue('shipping_charge');
		$tax = (float) Tools::getvalue('shipping_tax');

// Package size
		if (Configuration::get('CLICKLINE_APPLY_DISCOUNT'))
			$products = $this->validateMeasures($cart);
		else
			$products = false;

		if (!$products)
		{
// Get shipping charge with the package size
			$shipping = array(
				array('shipping_width' => Tools::getvalue('width'),
					'shipping_height' => Tools::getvalue('height'),
					'shipping_length' => Tools::getvalue('depth'),
					'shipping_weight' => $order->getTotalWeight())
			);

			$trans = $clickline->getShippingQuote(array(
				'shipping_cp_to' => $address_delivery->postcode,
				'country_to' => $country_to,
				'packages' => 1,
				'shippings' => $shipping));

// Cast SimpleXMLElement to array
			$trans_count = count($trans);

			for ($j = 0; $j < $trans_count; $j++)
			{
				$trans[$j] = $this->toArray($trans[$j]);

// Get price with the displacement of the carrier
				if ($trans[$j]['shipping_id_carrier'] == $transporter_id && $trans[$j]['shipping_code_carrier'] == $product_code)
				{
					$service_id = $trans[$j]['shipping_id_service'];
					$service_name = $trans[$j]['shipping_name_carrier'];
					$charge = $trans[$j]['shipping_charge'];
					$tax = $trans[$j]['shipping_tax'];
				}
			}
		}

// Check that there is sufficient balance in the Clickline's account
		$saldo = $clickline->getBalanceInquiry();

		if (((float) $saldo[0]) >= (((float) $charge) + (((float) $charge) * ((float) $tax) / 100)))
		{
//****** Set XML for put_order() ******//
			$billing = $this->getBillingArray($tax);
			$from = $this->getFromArray($order);
			$to = $this->getToArray($address_delivery, $lastname_to, $lastname2_to, $country_to, $customer);
			$packages = $this->getPackagesArray($order, $charge, $tax);

			$put_order = $clickline->putOrder(array(
				'transporter_id' => $transporter_id,
				'service_id' => $service_id,
				'service_name' => $service_name,
				'hour_min_collection' => Tools::getvalue('hour_range_from'),
				'product_code' => $product_code,
				'payment_method' => 'bolsapaymentmethod',
				'client_ip' => $_SERVER['REMOTE_ADDR'],
				'comments' => '',
				'code_to' => $country_to,
				'billing' => $billing,
				'from' => $from,
				'to' => $to,
				'packages' => $packages));

			if (count($put_order['order']) > 0)
			{
				$clorder = new CLOrderClass($id_order);
				$clorder->config = $this->getClorderConfigArray($transporter_id, $service_id, $charge, $product_code, $service_name, $tax);

// Cast SimpleXMLElement to array
				$arrayOrder = (array) $put_order['order'];
				$put_order['order'] = $arrayOrder[0];
				$arrayProforma = (array) $put_order['proforma_pdf'];
				$put_order['proforma_pdf'] = $arrayProforma[0];
				$arrayConsigment = (array) $put_order['consignment_pdf'];
				$put_order['consignment_pdf'] = $arrayConsigment[0];
				$arrayTickets = (array) $put_order['ticket'][0];
				$put_order['ticket'] = $arrayTickets[0];

				$clorder->config_ws = array('order' => $put_order['order'],
					'proforma_pdf' => $put_order['proforma_pdf'],
					'consignment_pdf' => $put_order['consignment_pdf'],
					'ticket' => $put_order['ticket']);

				$clorder->addSql();
			}
			else
				$this->post_errors[0] = $this->l('Failed to register the order. Contact with your administrator');
		}
		else
			$this->post_errors[0] = $this->l('You do not have enough fund');
	}

	private function getBillingArray($tax)
	{
		return array(
			'firstname_billing' => Configuration::get('CLICKLINE_FIRST_NAME'),
			'lastname_billing' => Configuration::get('CLICKLINE_LAST_NAME'),
			'lastname2_billing' => Configuration::get('CLICKLINE_LAST_NAME_2'),
			'company_billing' => Configuration::get('CLICKLINE_SHOP_NAME'),
			'taxvat' => $tax,
			'road_type_billing' => 'street',
			'street_billing' => Configuration::get('CLICKLINE_STREET'),
			'road_number_billing' => Configuration::get('CLICKLINE_ROAD_NUMBER'),
			'portal_billing' => Configuration::get('CLICKLINE_PORTAL'),
			'floor_billing' => Configuration::get('CLICKLINE_FLOOR'),
			'door_billing' => Configuration::get('CLICKLINE_DOOR'),
			'postcode_billing' => Configuration::get('CLICKLINE_POSTCODE'),
			'city_billing' => Configuration::get('CLICKLINE_CITY'),
			'country_billing' => 'ES',
			'telephone_billing' => Configuration::get('CLICKLINE_TELEPHONE'),
			'fax_billing' => Configuration::get('CLICKLINE_FAX'),
			'email_billing' => Configuration::get('CLICKLINE_EMAIL'));
	}

	private function getFromArray($order)
	{
		return array(
			'firstname_from' => Configuration::get('CLICKLINE_FIRST_NAME'),
			'lastname_from' => Configuration::get('CLICKLINE_LAST_NAME'),
			'lastname2_from' => Configuration::get('CLICKLINE_LAST_NAME_2'),
			'company_from' => Configuration::get('CLICKLINE_SHOP_NAME'),
			'road_type_from' => 'street',
			'street_from' => Configuration::get('CLICKLINE_STREET'),
			'road_number_from' => Configuration::get('CLICKLINE_ROAD_NUMBER'),
			'portal_from' => Configuration::get('CLICKLINE_PORTAL'),
			'floor_from' => Configuration::get('CLICKLINE_FLOOR'),
			'door_from' => Configuration::get('CLICKLINE_DOOR'),
			'postcode_from' => Configuration::get('CLICKLINE_POSTCODE'),
			'city_from' => Configuration::get('CLICKLINE_CITY'),
			'country_from' => 'ES',
			'telephone_from' => Configuration::get('CLICKLINE_TELEPHONE'),
			'fax_from' => Configuration::get('CLICKLINE_FAX'),
			'email_from' => Configuration::get('CLICKLINE_EMAIL'),
			'data_collection_from' => Tools::getvalue('data_collection_from'),
			'hour_range_from' => Tools::getvalue('hour_range_from'),
			'merchandise_value' => $order->getTotalProductsWithoutTaxes(),
			'merchandise_desc' => Tools::getvalue('merchandise_desc'),
			'merchandise_type' => '');
	}

	private function getToArray($address_delivery, $lastname_to, $lastname2_to, $country_to, $customer)
	{
		return array(
			'firstname_to' => $address_delivery->firstname,
			'lastname_to' => $lastname_to,
			'lastname2_to' => $lastname2_to,
			'company_to' => (
			($address_delivery->company != '') ? $address_delivery->company : ($address_delivery->firstname.' '.$address_delivery->lastname)
			),
			'road_type_to' => 'street',
			'street_to' => Tools::getvalue('street_to'),
			'road_number_to' => Tools::getvalue('road_number_to'),
			'portal_to' => Tools::getvalue('portal_to'),
			'floor_to' => Tools::getvalue('floor_to'),
			'door_to' => Tools::getvalue('door_to'),
			'postcode_to' => $address_delivery->postcode,
			'city_to' => $address_delivery->city,
			'country_to' => $country_to,
			'telephone_to' => (($address_delivery->phone != '') ? $address_delivery->phone : $address_delivery->phone_mobile),
			'fax_to' => '',
			'email_to' => $customer->email);
	}

	private function getPackagesArray($order, $charge, $tax)
	{
		return array(
			'width' => Tools::getvalue('width'),
			'height' => Tools::getvalue('height'),
			'length' => Tools::getvalue('depth'),
			'weight' => $order->getTotalWeight(),
			'charge' => $charge,
			'bultos' => '1',
			'taxes_percentage' => $tax);
	}

	private function getClorderConfigArray($transporter_id, $service_id, $charge, $product_code, $service_name, $tax)
	{
		return array('id_shipping_carrier' => $transporter_id,
			'id_shipping_service' => $service_id,
			'shipping_charge' => $charge,
			'shipping_code_carrier' => $product_code,
			'shipping_name_carrier' => $service_name,
			'shipping_tax' => $tax,
			'data_collection_from' => Tools::getvalue('data_collection_from'),
			'hour_range_from' => Tools::getvalue('hour_range_from'),
			'merchandise_desc' => Tools::getvalue('merchandise_desc'),
			'street_billing' => Configuration::get('CLICKLINE_STREET'),
			'road_number_billing' => Configuration::get('CLICKLINE_ROAD_NUMBER'),
			'portal_billing' => Configuration::get('CLICKLINE_PORTAL'),
			'floor_billing' => Configuration::get('CLICKLINE_FLOOR'),
			'door_billing' => Configuration::get('CLICKLINE_DOOR'),
			'street_to' => Tools::getvalue('street_to'),
			'road_number_to' => Tools::getvalue('road_number_to'),
			'portal_to' => Tools::getvalue('portal_to'),
			'floor_to' => Tools::getvalue('floor_to'),
			'door_to' => Tools::getvalue('door_to'));
	}

	public function validateWeight($cart)
	{
// Get cart weight
		$weight = $cart->getTotalWeight();

// The weight must be greater than 0 and less than 3
		if ($weight > 0 && $weight <= 3)
			return true;

		return false;
	}

	public function validateMeasures($cart)
	{
// Get cart weight
		$weight = $cart->getTotalWeight();

		if ($weight > 0)
		{
			if (Configuration::get('CLICKLINE_APPLY_DISCOUNT'))
			{
				$products = $this->getCartProductMeasures($cart);

				foreach ($products as $product)
					if ($product['shipping_width'] == 0 || $product['shipping_height'] == 0 || $product['shipping_length'] == 0)
						return false;

				return $products;
			}
		}

		return false;
	}

	public function getCartProductMeasures($cart)
	{
// Get cart products
		$p = 0;
		$products = $cart->getProducts();

		$product_det = array();
		$products_size = count($products);

		for ($i = 0; $i < $products_size; $i++)
		{
// Get size of the product
			$shipping = array('shipping_width' => $products[$i]['width'],
				'shipping_height' => $products[$i]['height'],
				'shipping_length' => $products[$i]['depth'],
				'shipping_weight' => $products[$i]['weight']);

// Add the amount of products from the cart
			for ($j = 0; $j < $products[$i]['quantity']; $j++)
			{
				$product_det[$p] = $shipping;
				$p++;
			}
		}

		return $product_det;
	}

	public function getOrderShippingCost($params, $shipping_cost)
	{
		$shipping_cost = 0;
//We don't use the second parameter called $shipping_cost but we need it to satisfy the abstract declaration in CarrierModule class
		if (Configuration::get('CLICKLINE_APPLY_DISCOUNT'))
		{
// VALIDATIONS TO SHOW OR NOT CLICKLINE
// Get all measures of products if they are specified, else returns FALSE
			if (!isset($this->products))
				$this->products = $this->validateMeasures($params);

// If not defined all measures of products
			if (!$this->products)
			{
// Check that weight is on the threshold of displacement
				if (!$this->validateWeight($params))
					return false;
			}
		}
		else
			$this->products = false;

// Get carrier information
		if (!isset($this->clcart))
			$this->clcart = new CLCartClass($params->id);

		$clcarrier_changed = false;

// Get cart weight
		$weight = $params->getTotalWeight();

		if (count($this->clcart->config_ws) == 0 && $params->id_customer != 0)
		{
// Get user information
			$user_data = $this->getCountryAndCP($params);

// Get cart products again
			$this->products = $this->validateMeasures($params);

// Check if all products have measures
			if (!$this->products && Configuration::get('CLICKLINE_APPLY_DISCOUNT'))
			{
// Check that weight is on the threshold of displacement
				if (!$this->validateWeight($params))
					return false;
			}

// Get amount of carriers and create a registry on clickline_cart
			$this->getClicklineCarriers($user_data, $weight, /* true, */ $this->products);
// Check the results remanded WS
			if (count($this->clcart->config_ws) == 0)
				return false;

			$clcarrier_changed = true;
		}
		else
		{
			if (count($this->clcart->config) > 0 && $params->id_customer != 0)
			{
				$user_data = $this->getCountryAndCP($params);
// Check if the weight , PC or country have changed
				if (($weight != $this->clcart->config['weight']) ||
						($user_data['cp'] != $this->clcart->config['cp']) ||
						($user_data['country_code'] != $this->clcart->config['country_code']))
				{
// Get again the cart products
					$this->products = $this->validateMeasures($params);

// Check if all products have measures
					if (!$this->products && Configuration::get('CLICKLINE_APPLY_DISCOUNT'))
					{
// Check that weight is on the threshold of displacement
						if (!$this->validateWeight($params))
							return false;
					}

// Get amount of carriers and update the registry of clickline_cart
					$this->getClicklineCarriers($user_data, $weight, $this->products);

// Check the results remanded WS
					if (count($this->clcart->config_ws) == 0)
						return false;

					$clcarrier_changed = true;
				}
			}
		}

// Check for updated list of carriers
		if (!$clcarrier_changed && $params->id_customer != 0)
		{
			$shipping_charge = (number_format(
							(float) $this->clcart->config['shipping_charge'] +
							((float) $this->clcart->config['shipping_charge'] * (float) $this->clcart->config['shipping_tax'] / 100) + $shipping_cost, 2, '.', ''));
			return $shipping_charge;
		}
		if (count($this->clcart->config_ws) > 0)
			return $this->updateCarrierInfo($params, $weight);

		return false;
	}

	private function updateCarrierInfo($params, $weight)
	{
		$indice = 0;
		$lowest_price = $this->clcart->config_ws[0]['shipping_charge'];
		$carrier_def = false;

// Get the designated carrier in the administration
		$configws_count = count($this->clcart->config_ws);
		for ($i = 0; $i < $configws_count && !$carrier_def; $i++)
		{
			if ((Configuration::get('CLICKLINE_CARRIER_DEF')) == $this->clcart->config_ws[$i]['shipping_id_carrier'])
			{
				$indice = $i;
				$carrier_def = true;
			}
		}

		if ($carrier_def == false)
		{
// Get the carrier with the lowest price
			$configws_count = count($this->clcart->config_ws);
			for ($i = 0; $i < $configws_count; $i++)
			{
				if ((float) $this->clcart->config_ws[$i]['shipping_charge'] < (float) $lowest_price)
				{
					$lowest_price = (float) $this->clcart->config_ws[$i]['shipping_charge'];
					$indice = $i;
				}
			}
		}

// Get the user data
		$user_data = $this->getCountryAndCP($params);

// Set values
		$this->clcart->config['id_shipping_carrier'] = $this->clcart->config_ws[$indice]['shipping_id_carrier'];
		$this->clcart->config['id_shipping_service'] = $this->clcart->config_ws[$indice]['shipping_id_service'];
		$this->clcart->config['shipping_charge'] = $this->clcart->config_ws[$indice]['shipping_charge'];
		$this->clcart->config['shipping_code_carrier'] = $this->clcart->config_ws[$indice]['shipping_code_carrier'];
		$this->clcart->config['shipping_name_carrier'] = $this->clcart->config_ws[$indice]['shipping_name_carrier'];
		$this->clcart->config['shipping_tax'] = $this->clcart->config_ws[$indice]['shipping_tax'];
		$this->clcart->config['weight'] = $weight;
		$this->clcart->config['cp'] = $user_data['cp'];
		$this->clcart->config['country_code'] = $user_data['country_code'];
		$this->clcart->updateSql();

		return number_format(
				(float) $this->clcart->config_ws[$indice]['shipping_charge'] +
				((float) $this->clcart->config_ws[$indice]['shipping_charge'] * (float) $this->clcart->config_ws[$indice]['shipping_tax'] / 100), 2, '.', '');
	}

	public function getOrderShippingCostExternal($params)
	{
//We don't use the parameter called $params but we need it to satisfy the abstract declaration in CarrierModule class
		$params = null;
		return $params;
	}

	public function toArray(SimpleXMLElement $xml)
	{
		$array = (array) $xml;

		foreach (array_slice($array, 0) as $key => $value)
		{
			if ($value instanceof SimpleXMLElement)
				$array[$key] = empty($value) ? null : toArray($value);
		}
		return $array;
	}

	public function getCountryAndCP($params)
	{
		if (count($this->user_data) == 0)
		{
// Get the user data
			$usuario_direccion_id = $params->id_address_delivery;
			$query = 'SELECT * FROM '._DB_PREFIX_.'address where id_address = "'.pSQL($usuario_direccion_id).'"';
			$usuario_datos = Db::getInstance()->ExecuteS($query);

			if ($usuario_datos != null)
			{
				$query = 'SELECT iso_code FROM '._DB_PREFIX_.'country where id_country = "'.pSQL($usuario_datos[0]['id_country']).'"';
				$usuario_pais_id = Db::getInstance()->ExecuteS($query);
			}

			$usuario_pais = '';
			$usuario_cp = '';

			if ($usuario_pais_id != null)
				$usuario_pais = $usuario_pais_id[0]['iso_code'];
			if ($usuario_datos != null)
				$usuario_cp = $usuario_datos[0]['postcode'];
			$this->user_data = array('country_code' => $usuario_pais, 'cp' => $usuario_cp);
		}

		return $this->user_data;
	}

	public function getClicklineCarriers($user_data, $weight, /* $insert = true, */ $shipping = false) /* Insert comentado porque no tiene uso */
	{
// Call WS for obtain shipping cost
// Create ClickLine_api Object
		$clickline = new ClickLineApi();

		if (!$shipping || !Configuration::get('CLICKLINE_APPLY_DISCOUNT'))
		{
			$trans = $clickline->getShippingQuoteNoMesure(array(
				'shipping_cp_to' => $user_data['cp'],
				'country_to' => $user_data['country_code'],
				'weight' => $weight,
				'packages' => 1));
		}
		else
		{
			$trans = $clickline->getShippingQuote(array(
				'shipping_cp_to' => $user_data['cp'],
				'country_to' => $user_data['country_code'],
				'packages' => 1,
				'shippings' => $shipping));
		}

// Cast SimpleXMLElement to array
		$trans_count = count($trans);

		for ($j = 0; $j < $trans_count; $j++)
			$trans[$j] = $this->toArray($trans[$j]);

		$trans = $this->orderMultiDimensionalArray($trans, 'shipping_charge', false);

// Save carriers on BDD
		$this->clcart->config_ws = $trans;
		if (!$this->clcart->exist)
		{
			if ($this->clcart->addSql())
				$this->clcart->exist = true;
		}
		else
			$this->clcart->updateSql();
	}

	public function orderMultiDimensionalArray($to_order_array, $field, $inverse = false)
	{
		$new_row = array();
		$position = array();

		foreach ($to_order_array as $key => $row)
		{
			$position[$key] = $row[$field];
			$new_row[$key] = $row;
		}
		if ($inverse)
			arsort($position);
		else
			asort($position);

		$return_array = array();
		foreach ($position as $key => $row)
			$return_array[] = $new_row[$key];

		return $return_array;
	}

	public function hookbackOfficeHeader()
	{
//TODO bloqueo para un solo controlador (1.5 y 1.4)
		if (isset(Context::getContext()->controller) && $this->context->controller != null)
			$this->context->controller->addJS($this->_path.'js/clickline_order.js');

//Ponemos el css para el admin
		if ((int) strcmp((version_compare(_PS_VERSION_, '1.5', '>=') ? Tools::getValue('configure') : Tools::getValue('module_name')), $this->name) == 0)
		{

			if (isset(Context::getContext()->controller) && $this->context->controller != null)
			{
				$this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/css/admin.css');
				$this->context->controller->addJS(_MODULE_DIR_.$this->name.'/js/admin.js');
			}
		}
	}

	public function hookHeader($params)
	{
		if (isset(Context::getContext()->controller))
			Context::getContext()->controller->addCSS(_MODULE_DIR_.$this->name.'/css/timetable.css', 'all');
		else
			return;

		$cart = $params['cart'];
		if (validate::isLoadedObject($cart))
		{
// Crete CLCart Object
			if (!isset($this->clcart))
				$this->clcart = new CLCartClass($cart->id);

// Check if a carrier was selected for this cart previously
			$carrier_selected = (
					((int) $cart->id_carrier == (int) Configuration::get('CLICKLINE_CARRIER_ID')) ? (
							isset($this->clcart->config['id_shipping_carrier']) ? $this->clcart->config['id_shipping_carrier'] : ''
							) : ''
					);

			$carrier_selected = ((isset($this->clcart->config['id_shipping_carrier'])) ? $this->clcart->config['id_shipping_carrier'] : '');

// Asign smarty vars
			$this->context->smarty->assign(array(
				'clickline_carrier' => (int) Configuration::get('CLICKLINE_CARRIER_ID'),
				'id_carrier' => (int) $cart->id_carrier,
				'carrier_sel' => $carrier_selected,
				'ps15' => (version_compare(_PS_VERSION_, '1.5', '>=') && version_compare(_PS_VERSION_, '1.6', '<'))
			));

			return $this->display(__FILE__, '/views/templates/hook/hook_header.tpl');
		}
	}

}

?>
