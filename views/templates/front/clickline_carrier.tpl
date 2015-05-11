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

<td colspan="4">
    <div id="carrierTableCL">
        <h3>{l s='Select a carrier of Clickline' mod='clickline'}</h3>
        <div class="clickline_info">
            <div>
                <span><span class="clickline_bold">{l s='From' mod='clickline'}:</span> {$cp_from|escape:'htmlall':'UTF-8'} - {$country_from|escape:'htmlall':'UTF-8'}</span>
                <span class="clickline_info_to"><span class="clickline_bold">{l s='To' mod='clickline'}:</span> {$cp_to|escape:'htmlall':'UTF-8'} - {$country_to|escape:'htmlall':'UTF-8'}</span>
                <span class="clickline_info_to"><span class="clickline_bold">{l s='Weight' mod='clickline'}:</span> {$weight|escape:'htmlall':'UTF-8'} kg</span>
            </div>
        </div>
        <div class="clickline_carriers">
            {foreach from=$djlCarriers item=carrier name=myLoop}
                <p class="clickline_module">
                    <input type="hidden" name="clickline_id_carrier{$carrier.id_carrier|intval}_{$carrier.id_service|intval}" value="{$carrier.id_carrier|intval}"/>
                    <input type="hidden" name="clickline_code_carrier{$carrier.id_carrier|intval}_{$carrier.id_service|intval}" value="{$carrier.code_carrier|escape:'htmlall':'UTF-8'}"/>
                    <input type="hidden" name="clickline_name_carrier{$carrier.id_carrier|intval}_{$carrier.id_service|intval}" value="{$carrier.name|escape:'htmlall':'UTF-8'}"/>
                    <input type="hidden" name="clickline_id_service{$carrier.id_carrier|intval}_{$carrier.id_service|intval}" value="{$carrier.id_service|intval}"/>
                    <input type="hidden" name="clickline_price{$carrier.id_carrier|intval}_{$carrier.id_service|intval}" value="{$carrier.price|escape:'htmlall':'UTF-8'}"/>
                    <input type="hidden" name="clickline_shipping_tax{$carrier.id_carrier|intval}_{$carrier.id_service|intval}" value="{$carrier.shipping_tax|intval}"/>
                    <input type="hidden" name="clickline_price_with_tax{$carrier.id_carrier|intval}_{$carrier.id_service|intval}" value="{convertPrice price=$carrier.price_with_tax}"/>
                    <a href="#{$smarty.foreach.myLoop.index|escape:'htmlall':'UTF-8'}" id="carrierC{$carrier.id_carrier|intval|escape:'htmlall':'UTF-8'}_{$carrier.id_service|intval|escape:'htmlall':'UTF-8'}" name="carrierC{$carrier.id_carrier|intval|escape:'htmlall':'UTF-8'}_{$carrier.id_service|intval|escape:'htmlall':'UTF-8'}" 
                       onclick="javascript:DJLupdateCarrierSelectionAndGift({$carrier.id_carrier|intval|escape:'javascript':'UTF-8'},{$carrier.id_service|intval|escape:'javascript':'UTF-8'});"
                       {if $carrier.id_carrier == $carrier_sel && $carrier.id_service == $service_sel} class="clickline_selected" {else} class="carrierC" {/if}>
                        <img src="{$clickline_module_dir}lib/thumb/timthumb.php?src={$carrier.logo|escape:'htmlall':'UTF-8'}" alt="{$carrier.name|escape:'htmlall':'UTF-8'}" title="{$carrier.name|escape:'htmlall':'UTF-8'}" width="100" height="65"></br>
                        {$carrier.name|escape:'htmlall':'UTF-8'} - {$carrier.code_carrier|escape:'htmlall':'UTF-8'}
                        <span class="price">
                            {convertPrice price=$carrier.price}
                            <span class="tax">
                                {l s='(IVA not incl.)' mod='clickline'}	
                            </span>
                        </span>
                    </a>	
                </p>
            {/foreach}
        </div>
    </div>
</td>
