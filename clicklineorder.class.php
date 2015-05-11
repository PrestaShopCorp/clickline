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

class CLOrderClass
{

	public $id_order;
	public $config = array();
	public $config_ws = array();

	public function __construct($id_order = null)
	{
		if (isset($id_order))
		{
			$this->id_order = (int)$id_order;
			$order = self::getClicklineOrder((int)$id_order);
			if (isset($order['config']))
				$this->config = Tools::jsonDecode($order['config']);
			if (isset($order['configWS']))
				$this->config_ws = Tools::jsonDecode($order['configWS']);
		}
	}

	public function addSql()
	{
		return Db::getInstance()->autoExecute(
			_DB_PREFIX_.'clickline_order', array(
			'id_order' => (int)$this->id_order,
			'config' => pSQL(Tools::jsonEncode($this->config)),
			'configWS' => pSQL(Tools::jsonEncode($this->config_ws)),
		), 'INSERT');
	}

	public function deleteSql()
	{
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'clickline_order` WHERE `id_order` = '.(int)$this->id_order);
	}

	public function updateSql()
	{
		Db::getInstance()->autoExecute(_DB_PREFIX_.'clickline_order', array(
			'id_order' => (int)$this->id_order,
			'config' => pSQL(Tools::jsonEncode($this->config)),
			'configWS' => pSQL(Tools::jsonEncode($this->config_ws)),
		), 'UPDATE', '`id_order` = '.(int)$this->id_order);
	}

	public static function getClicklineOrder($id_order)
	{
		$order = Db::getInstance()->ExecuteS('
		SELECT cp.*
		FROM `'._DB_PREFIX_.'clickline_order` cp
		WHERE cp.`id_order` = '.(int)$id_order);
		if (count($order))
			return $order[0];
		return null;
	}

}

?>