<?php
/*
* 2007-2019 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class HomeSpecials extends Module
{
	private $_html = '';
	private $_postErrors = array();

    	function __construct()
	{
		$this->name = 'homespecials';
		$this->tab = 'pricing_promotion';
		$this->version = 1.0;
		$this->author = 'Nemo and Andrea Di Mario';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Homepage specials');
		$this->description = $this->l('Adds a block with current product specials in homepage.');
	}

	public function install()
	{
		if (!Configuration::updateValue('HOME_SPECIALS_NBR', 8) OR
			!parent::install() OR
			!$this->registerHook('home') OR 
			!$this->registerHook('header'))
					return false;
				return true;
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitSpecial'))
		{
			$nbr = (int)(Tools::getValue('nbr'));
			if (!$nbr OR $nbr <= 0 OR !Validate::isInt($nbr))
				$errors[] = $this->l('Invalid number of products');
			else
				Configuration::updateValue('HOME_SPECIALS_NBR', (int)($nbr));
			if (isset($errors) AND sizeof($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Number of products displayed').'</label>
				<div class="margin-form">
					<input type="text" size="5" name="nbr" value="'.Tools::safeOutput(Tools::getValue('nbr', (int)(Configuration::get('HOME_SPECIALS_NBR')))).'" />
					<p class="clear">'.$this->l('The number of products displayed on homepage (default: 10).').'</p>
					
				</div>
				<center><input type="submit" name="submitSpecial" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}

	public function getHomeSpecials($id_lang, $nbProducts) {
	 	global $link;
		if ($nbProducts < 1) $nbProducts = 10;
		
		$groups = FrontController::getCurrentCustomerGroups();
		$sqlGroups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');

		$query = 'SELECT p.id_product, p.name, offers.price FROM `'._DB_PREFIX_.'specific_price` offers LEFT JOIN `'._DB_PREFIX_.'product_lang` p ON (p.`id_product` = offers.`id_product`) LIMIT '.(int)($nbProducts);

		$result = Db::getInstance()->ExecuteS($query);
			
		return Product::getProductsProperties((int) $id_lang, $result);
					
	}	

	public function hookHome($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;
		global $smarty;
		if (!$special = $this->getHomeSpecials((int)($params['cookie']->id_lang), Configuration::get('HOME_SPECIALS_NBR')))
			return;

		$smarty->assign(array(
			'special' => $special,
			'homeSize' => Image::getSize('home')
		));

		return $this->display(__FILE__, 'homespecials.tpl');
	}

	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;
		$this->context->controller->addCSS(($this->_path).'homespecials.css', 'all');
	}
}

