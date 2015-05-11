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

class CLCartClass
{

	public $id_cart;
	public $config = array(
		'id_shipping_carrier' => 0,
		'id_shipping_service' => 0,
		'shipping_charge' => 0,
		'shipping_code_carrier' => '',
		'shipping_name_carrier' => '',
		'shipping_tax' => 0,
		'weight' => 1,
		'cp' => 0,
		'country_code' => ''
	);
	public $config_ws = array();
	public $exist = false;

	public function __construct($id_cart = null)
	{
		if (!is_null($id_cart))
		{
			$this->id_cart = (int)$id_cart;
			$cart = self::getClicklineCart((int)$id_cart);
			if (!is_null($cart))
			{
				$this->exist = true;
				if (isset($cart['config']))
					$this->config = array_merge(self::getDefaultConfig(), Tools::jsonDecode($cart['config'], true));
				if (isset($cart['configWS']))
					$this->config_ws = Tools::jsonDecode($cart['configWS'], true);
			}
		}
	}

	public static function installDB()
	{
		if (!file_exists(_PS_MODULE_DIR_.'clickline'.DIRECTORY_SEPARATOR.'install.sql'))
			die(Tools::displayError('File install.sql is missing'));
		elseif (!$sql = Tools::file_get_contents(_PS_MODULE_DIR_.'clickline'.DIRECTORY_SEPARATOR.'install.sql'))
			die(Tools::displayError('File install.sql is not readable'));
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach ($sql as $query)
		{
			if (!empty($query) && !Db::getInstance()->execute(trim($query)))
				return false;
		}
		return true;
	}

	public function addSql()
	{
		return Db::getInstance()->autoExecute(_DB_PREFIX_.'clickline_cart', array(
					'id_cart' => (int)$this->id_cart,
					'config' => pSQL(Tools::jsonEncode($this->config)),
					'configWS' => pSQL(Tools::jsonEncode($this->config_ws)),
						), 'INSERT');
	}

	public function deleteSql()
	{
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'clickline_cart` WHERE `id_cart` = '.(int)$this->id_cart);
	}

	public function updateSql()
	{
		Db::getInstance()->autoExecute(_DB_PREFIX_.'clickline_cart', array(
			'id_cart' => (int)$this->id_cart,
			'config' => pSQL(Tools::jsonEncode($this->config)),
			'configWS' => pSQL(Tools::jsonEncode($this->config_ws)),
				), 'UPDATE', '`id_cart` = '.(int)$this->id_cart);
	}

	public static function getDefaultConfig()
	{
		return array('id_shipping_carrier' => 0,
			'id_shipping_service' => 0,
			'shipping_charge' => 0,
			'shipping_code_carrier' => '',
			'shipping_name_carrier' => '',
			'shipping_tax' => 0,
			'weight' => 1,
			'cp' => 0,
			'country_code' => '');
	}

	public static function getClicklineCart($id_cart)
	{
		$cart = Db::getInstance()->getRow('
		SELECT cp.*
		FROM `'._DB_PREFIX_.'clickline_cart` cp
		WHERE cp.`id_cart` = '.(int)$id_cart);
		if ($cart != false)
			return $cart;
		return null;
	}

}

?>
