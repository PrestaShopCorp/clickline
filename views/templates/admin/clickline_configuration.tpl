{*
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
*  @author alabazweb.com <tecnico@alabazweb.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div id="clickline-settings">
    <div class="clickline-content">
        <div class="col-xs-9">
            <img src="{$logo_src|escape:'htmlall':'UTF-8'}img/logo.png" alt="clickline.com">
            <div>
                <strong>{l s='Quit managing different accounts, Clickline is a shipping comparator integrated in PrestaShop.' mod='clickline'}</strong><br>
                {l s='This module allows your customers to choose the carrier they want, and let you manage your shipments in peace.' mod='clickline'}<br>
                {l s='For the module to work correctly you must create an account on Clickline, when shipping is also required to access the web a www.clickline.com to fund your account.' mod='clickline'}<br>
                {l s='As a client of Clickline also save up 5 percent in each of their shipments and:' mod='clickline'}<br>
                <ul>
                    <li>{l s='Expedite the shipment process' mod='clickline'}</li>
                    <li>{l s='Check your orders status' mod='clickline'}</li>
                    <li>{l s='Fund your prepaid account' mod='clickline'}</li>
                </ul>
                 {l s='If you have any questions please contact us by email at comunicados@clickline.com or calling us on Monday through Friday October +34 902 10 80 902 from 9 am to 6 pm' mod='clickline'}
            </div>
        </div>
        <div class="clickline-right col-xs-3 pull-right">
            <ul>
                <li>
                    <span class='heads'>{l s='Comparador de envíos' mod='clickline'}</span><br>
                    <span class ='subs'>{l s='Compare entre los más importantes transportistas del mercado' mod='clickline'}</span>
                </li>
                <li>
                    <span class='heads'>{l s='Envíos nacionales e internacionales' mod='clickline'}</span><br>
                    <span class ='subs'>{l s='Realice envíos a cualquier parte del mundo' mod='clickline'}</span>
                </li>
                <li>
                    <a href="https://www.clickline.com/customer/account/create/" target="_blank">
                        <span class='heads'>
                            {l s='Create an account' mod='clickline'}
                        </span>
                    </a><br>
                    <span class ='subs'>{l s='Required register User' mod='clickline'}</span>
                </li>
            </ul>
        </div>
    </div>
    <div class="clear clearfix"></div>
    <div id="clickline-menu" class="row {if $ps_16 eq false}not_{/if}ps_16">
        <!--<div class="clickline-menu-option col-xs-3" id="settings-option">
            <img class="img-responsive" src="{$logo_src|escape:'htmlall':'UTF-8'}img/settings_options/settings.png" alt="{l s='Settings' mod='clickline'}"/>
            <div>
                <h3>{l s='Settings' mod='clickline'}</h3>
            </div>
        </div>
        <div class="clickline-menu-option col-xs-3" id="documentation-option">
            <a href="#">
                <img class="img-responsive" src="{$logo_src|escape:'htmlall':'UTF-8'}img/settings_options/documentation.png" alt="{l s='Documentation' mod='clickline'}"/>
                <div>
                    <h3>{l s='Documentation' mod='clickline'}</h3>
                </div>
            </a>
        </div>
        <div class="clickline-menu-option col-xs-3" id="support-option">
            <a href="#">
                <img class="img-responsive" src="{$logo_src|escape:'htmlall':'UTF-8'}img/settings_options/support.png" alt="{l s='Support' mod='clickline'}"/>
                <div>
                    <h3>{l s='Support' mod='clickline'}</h3>
                </div>
            </a>
        </div>-->
    </div>
    <div class="clear"></div>
    <div id="clickline-configuration">
            {$main_form|escape:'none':'UTF-8'}
    </div>
</div>