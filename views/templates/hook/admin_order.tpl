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
</br>
<div class="row">
    <div class="col-lg-7">
        <div class="panel">
            <div class="panel-heading">
                <img src="{$path|escape:'htmlall':'UTF-8'}logo.png" alt="" />{l s='Clickline Information' mod='clickline'}

            </div>
            <div class="well hidden-print">
                {if isset($errors[0])}
                    <span style="color:red">{$errors[0]|escape:'htmlall':'UTF-8'}</span>
                {/if}
                <form action="{$request_uri|escape:'none':'UTF-8'}" method="post" id="clickline-order-form">
                    <input type="hidden" name="id_cart" id="id_cart" value="{$id_cart|escape:'htmlall':'UTF-8'}" />
                    <input type="hidden" name="id_shipping_carrier" id="id_shipping_carrier" value="{if isset($clorder)}{$clorder['id_shipping_carrier']|escape:'htmlall':'UTF-8'}{else}{$clcart['id_shipping_carrier']|escape:'htmlall':'UTF-8'}{/if}" />
                    <input type="hidden" name="id_shipping_service" id="id_shipping_service" value="{if isset($clorder)}{$clorder['id_shipping_service']|escape:'htmlall':'UTF-8'}{else}{$clcart['id_shipping_service']|escape:'htmlall':'UTF-8'}{/if}" />
                    <input type="hidden" name="shipping_charge" id="shipping_charge" value="{if isset($clorder)}{$clorder['shipping_charge']|escape:'htmlall':'UTF-8'}{else}{$clcart['shipping_charge']|escape:'htmlall':'UTF-8'}{/if}" />
                    <input type="hidden" name="shipping_code_carrier" id="shipping_code_carrier" value="{if isset($clorder)}{$clorder['shipping_code_carrier']|escape:'htmlall':'UTF-8'}{else}{$clcart['shipping_code_carrier']|escape:'htmlall':'UTF-8'}{/if}" />
                    <input type="hidden" name="shipping_name_carrier" id="shipping_name_carrier" value="{if isset($clorder)}{$clorder['shipping_name_carrier']|escape:'htmlall':'UTF-8'}{else}{$clcart['shipping_name_carrier']|escape:'htmlall':'UTF-8'}{/if}" />
                    <input type="hidden" name="shipping_tax" id="shipping_tax" value="{if isset($clorder)}{$clorder['shipping_tax']|escape:'htmlall':'UTF-8'}{else}{$clcart['shipping_tax']|escape:'htmlall':'UTF-8'}{/if}" />

                    <div id="shipping_prices" class="panel form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-lg-3">{l s='Available balance' mod='clickline'}: </label>
                            <div class="col-lg-2 control-label input-group">
                                {$balance|escape:'htmlall':'UTF-8'}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">{l s='Carrier code' mod='clickline'}: </label>
                            <div class="col-lg-2 control-label input-group">
                                {$clcart['shipping_code_carrier']|escape:'htmlall':'UTF-8'}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">{l s='Tax' mod='clickline'}: </label>
                            <div class="col-lg-2 control-label input-group">
                                {$clcart['shipping_tax']|escape:'htmlall':'UTF-8'} %
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">{l s='Charge without taxes' mod='clickline'}: </label>
                            <div class="col-lg-2 control-label input-group">
                        {if isset($clorder)}{$clorder['shipping_charge']|escape:'htmlall':'UTF-8'}{else}{$clcart['shipping_charge']|escape:'htmlall':'UTF-8'}{/if}
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Weight' mod='clickline'}: </label>
                    <div class="col-lg-2 control-label input-group">
                        {$weight|escape:'htmlall':'UTF-8'} Kg
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Height' mod='clickline'}: <span style="color:red;">*</span></label>
                    <div class="col-lg-2 control-label input-group">
                        <input type="text" id="height" name="height" value="{$height|escape:'htmlall':'UTF-8'}" />
                    </div>
                    <label class="control-label col-lg-3">{l s='Width' mod='clickline'}: <span style="color:red;">*</span></label>
                    <div class="col-lg-2 control-label input-group">
                        <input type="text" id="width" name="width" value="{$width|escape:'htmlall':'UTF-8'}" />
                    </div>
                    <label class="control-label col-lg-3">{l s='Depth' mod='clickline'}: <span style="color:red;">*</span></label>
                    <div class="col-lg-2 control-label input-group">
                        <input type="text" id="depth" name="depth" value="{$depth|escape:'htmlall':'UTF-8'}" />
                    </div>
                </div>
                {if $products!=false}
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Package measures' mod='clickline'}: </label>
                        <div class="col-lg-2 control-label input-group">
                            {$height|escape:'htmlall':'UTF-8'} cm&nbsp;x&nbsp;{$width|escape:'htmlall':'UTF-8'} cm&nbsp;x&nbsp;{$depth|escape:'htmlall':'UTF-8'} cm
                        </div>
                    </div>
                {/if}
                {if isset($clorderWS)}
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Data collection from' mod='clickline'}: </label>
                        <div class="col-lg-2 control-label input-group">
                            {$clorder['data_collection_from']|escape:'htmlall':'UTF-8'}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Hour range from' mod='clickline'}: </label>
                        <div class="col-lg-2 control-label input-group">
                            {if $clorder['hour_range_from'] eq '1'}
                                09:00 - 11:00
                            {elseif $clorder['hour_range_from'] eq '2'}
                                11:00 - 13:00
                            {elseif $clorder['hour_range_from'] eq '3'}
                                13:00 - 15:00
                            {elseif $clorder['hour_range_from'] eq '4'}
                                15:00 - 17:30
                            {elseif $clorder['hour_range_from'] eq '5'}
                                9:00 - 14:00
                            {elseif $clorder['hour_range_from'] eq '6'}
                                14:00 - 18:00
                            {/if}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Clickline order' mod='clickline'}: </label>
                        <div class="col-lg-2 control-label input-group">
                            {$clorderWS['order']|escape:'htmlall':'UTF-8'}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Proforma pdf' mod='clickline'}: </label>
                        <div class="col-lg-2 control-label input-group">
                            <a href="{$clorderWS['proforma_pdf']|escape:'htmlall':'UTF-8'}" target="_blank" title="{l s='Proforma pdf' mod='clickline'}">
                                {l s='Open' mod='clickline'}
                            </a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Consignment pdf' mod='clickline'}: </label>
                        <div class="col-lg-2 control-label input-group">
                            <a href="{$clorderWS['consignment_pdf']|escape:'htmlall':'UTF-8'}" target="_blank" title="{l s='Consignment pdf' mod='clickline'}">
                                {l s='Open' mod='clickline'}
                            </a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Ticket' mod='clickline'}: </label>
                        <div class="col-lg-2 control-label input-group">
                            <a href="{$clorderWS['ticket']|escape:'htmlall':'UTF-8'}" target="_blank" title="{l s='Ticket' mod='clickline'}">
                                {l s='Open' mod='clickline'}
                            </a>
                        </div>
                    </div>
                {else}
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Data collection from' mod='clickline'}: <span style="color:red;">*</span></label>
                        <div class="col-lg-2">
                            <input type="text" id="data_collection_from" name="data_collection_from" value="{$params['data_collection_from']|escape:'htmlall':'UTF-8'}" />

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Hour range from' mod='clickline'}: <span style="color:red;">*</span></label>
                        <div class="col-lg-2">
                            <select name="hour_range_from">
                                <option value="5" {if $params['hour_range_from'] eq '5'}selected="selected"{/if}>9:00 - 14:00</option>
                                <option value="6" {if $params['hour_range_from'] eq '6'}selected="selected"{/if}>14:00 - 18:00</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Merchandise description' mod='clickline'}: <span style="color:red;">*</span></label>
                        <div class="col-lg-2">
                            <input type="text" id="merchandise_desc" name="merchandise_desc" value="{$params['merchandise_desc']|escape:'htmlall':'UTF-8'}" />

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Destination Address' mod='clickline'}: <span style="color:red;">*</span></label>
                        <div class="col-lg-2 control-label">
                            {$address1|escape:'htmlall':'UTF-8'} {$address2|escape:'htmlall':'UTF-8'}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Destination Street' mod='clickline'}: <span style="color:red;">*</span></label>
                        <div class="col-lg-2">
                            <input type="text" id="street_to" name="street_to" value="{$params['street_to']|escape:'htmlall':'UTF-8'}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Destination road number' mod='clickline'}: <span style="color:red;">*</span></label>
                        <div class="col-lg-2">
                            <input type="text" id="road_number_to" name="road_number_to" value="{$params['road_number_to']|escape:'htmlall':'UTF-8'}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Destination portal' mod='clickline'}: </label>
                        <div class="col-lg-2">
                            <input type="text" id="portal_to" name="portal_to" value="{$params['portal_to']|escape:'htmlall':'UTF-8'}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Destination floor' mod='clickline'}: </label>
                        <div class="col-lg-2">
                            <input type="text" id="floor_to" name="floor_to" value="{$params['floor_to']|escape:'htmlall':'UTF-8'}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Destination door' mod='clickline'}: </label>
                        <div class="col-lg-2">
                            <input type="text" id="door_to" name="door_to" value="{$params['door_to']|escape:'htmlall':'UTF-8'}" />
                        </div>
                    </div>
                {/if}
                {if isset($clorderWS) == false}
                    <div style="padding-right: 10px;text-align: right;">
                        <input type="submit" value="{l s='Put order' mod='clickline'}" name="submitClicklineInfo" id="submitClicklineInfo" class="button" />
                    </div>
                    <div>{l s='Required fields' mod='clickline'}<span style="color:red">*</span></div>
                {/if}
                <button class="button" onclick="$('#clickline-order-form').slideDown(); $(this).slideUp();">{l s='Put order' mod='clickline'}</button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
