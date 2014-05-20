{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div id="clickline-settings">
    <div class="clickline-content">
        <div class="clickline-left">
            <img src="{$logo_src|escape:'htmlall':'UTF-8'}img/logo.png" alt="clickline.com">
        </div>
        <div class="clickline-right">
            <ul>
                <li>
                    <span class='heads'>{l s='Comparador de envíos' mod='clickline'}</span><br>
                    <span class ='subs'>{l s='Compare entre los más importantes transportistas del mercado' mod='clickline'}</span>
                </li>
                <li>
                    <span class='heads'>{l s='Envíos nacionales e internacionales' mod='clickline'}</span><br>
                    <span class ='subs'>{l s='Realice envíos a cualquier parte del mundo' mod='clickline'}</span>
                </li>
            </ul>
        </div>
    </div>
    <div class="clear clearfix"></div>
    <div id="clickline-menu" class="row {if $ps_16 eq false}not_{/if}ps_16">
        <div class="clickline-menu-option col-xs-3" id="account-option">
            <a href="https://www.clickline.com/customer/account/create/" target="_blank">
                <img class="img-responsive" src="{$logo_src|escape:'htmlall':'UTF-8'}img/settings_options/create_account.jpg" alt="{l s='Create an account' mod='clickline'}"/>
                <div>
                    <h3>{l s='Create an account' mod='clickline'}</h3>
                </div>
            </a>
        </div>
        <div class="clickline-menu-option col-xs-3" id="settings-option">
            <img class="img-responsive" src="{$logo_src|escape:'htmlall':'UTF-8'}img/settings_options/settings.jpg" alt="{l s='Settings' mod='clickline'}"/>
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
        </div>
    </div>
    <div class="clear"></div>
    <div id="clickline-configuration">
        <form action="{$action_url|escape:'none':'UTF-8'}" method="post">
            <fieldset>
                <legend><img src="{$logo_src|escape:'htmlall':'UTF-8'}logo.gif" width="16" height="16" alt="Clickline" >{l s='ClickLine Module Configuration' mod='clickline'}</legend>
                <dl>
                    <dt style="display: none"><label>{l s='Host' mod='clickline'}: </label></dt>
                    <dd style="display: none"><input type="text" name="clickline_url" id="clickline_url" style="width:240px" value="{if isset($fieldsList['CLICKLINE_URL'])}{$fieldsList['CLICKLINE_URL']|escape:'htmlall':'UTF-8'}{/if}" /></dd>
                    <dt><label>{l s='User' mod='clickline'}: </label></dt>
                    <dd><input type="text" name="clickline_account" id="clickline_account" style="width:240px" value="{if isset($fieldsList['CLICKLINE_ACCOUNT'])}{$fieldsList['CLICKLINE_ACCOUNT']|escape:'htmlall':'UTF-8'}{/if}" />
                        <span style="color: #7F7F7F; font-size: 0.85em; margin-left: 5px;">{l s='User of Clickline account' mod='clickline'}</span></dd>
                    <dt><label>{l s='Password' mod='clickline'}: </label></dt>
                    <dd>
                        <input type="password" name="clickline_password" id="clickline_password" style="width:240px" value="{if isset($fieldsList['CLICKLINE_PASSWORD'])}{$fieldsList['CLICKLINE_PASSWORD']|escape:'htmlall':'UTF-8'}{/if}" />
                        <span style="color: #7F7F7F; font-size: 0.85em; margin-left: 5px;">{l s='Password of Clickline account' mod='clickline'}</span>
                    </dd>
                    <dt><label>{l s='CP From' mod='clickline'}: </label></dt>
                    <dd>
                        <input type="text" name="clickline_cp_from" id="clickline_cp_from" style="width:240px" value="{if isset($fieldsList['CLICKLINE_CP_FROM'])}{$fieldsList['CLICKLINE_CP_FROM']|escape:'htmlall':'UTF-8'}{/if}" />
                        <span style="color: #7F7F7F; font-size: 0.85em; margin-left: 5px;">{l s='ZIP code where the order is sent' mod='clickline'}</span>
                    </dd>
                    <dt><label>{l s='Country From' mod='clickline'}: </label></dt>
                    <dd>
                        <input type="text" name="clickline_country_from" id="clickline_country_from" style="width:240px" value="{if isset($fieldsList['CLICKLINE_COUNTRY_FROM'])}{$fieldsList['CLICKLINE_COUNTRY_FROM']|escape:'htmlall':'UTF-8'}{/if}" />
                        <span style="color: #7F7F7F; font-size: 0.85em; margin-left: 5px;">{l s='Country code where the order is sent, for example: ES for Spain' mod='clickline'}</span>
                    </dd>
                    <dt><label>{l s='Default carrier' mod='clickline'}: </label></dt>
                    <dd>
                        <select name="clickline_carrier_def" id="clickline_carrier_def" style="width:250px">
                            <option value="">{l s='None' mod='clickline'}</option>
                            {foreach from=$carrier_list item=carrier}
                                <option value="{$carrier->carrier_id|escape:'htmlall':'UTF-8'}" {if isset($fieldsList['CLICKLINE_CARRIER_DEF'])} {if $fieldsList['CLICKLINE_CARRIER_DEF'] eq $carrier->carrier_id} selected=selected {/if}{/if}>{$carrier->carrier|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                        <span style="color: #7F7F7F; font-size: 0.85em; margin-left: 5px;">{l s='Carrier that will be selected by default at Front Office' mod='clickline'}</span>
                    </dd>
                    <dt><label>{l s='Send measures from the Front Office of the store (in the product must indicate their size and weight)' mod='clickline'}: </label></dt>
                    <dd><input type="checkbox" name="apply_discount" id="apply_discount" {if $apply_discount} checked="checked" {/if}></dd>
                </dl>
            </fieldset><br/>
            <fieldset>
                <legend><img src="{$logo_src|escape:'htmlall':'UTF-8'}logo.gif" width="16" height="16" alt="Clickline" >{l s='Shop Information' mod='clickline'}</legend>

                <dl>
                    <dt>
                    <label>{l s='Shop name' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" name="shop_name" id="shop_name" style="width:240px" value="{if isset($fieldsList['CLICKLINE_SHOP_NAME'])}{$fieldsList['CLICKLINE_SHOP_NAME']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='First Name' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" name="first_name" id="first_name" style="width:240px" value="{if isset($fieldsList['CLICKLINE_FIRST_NAME'])}{$fieldsList['CLICKLINE_FIRST_NAME']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='Last Name' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" name="last_name" id="last_name" style="width:240px" value="{if isset($fieldsList['CLICKLINE_LAST_NAME'])}{$fieldsList['CLICKLINE_LAST_NAME']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='Last Name 2' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" name="last_name_2" id="last_name_2" style="width:240px" value="{if isset($fieldsList['CLICKLINE_LAST_NAME_2'])}{$fieldsList['CLICKLINE_LAST_NAME_2']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='Street' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" name="street" id="street" style="width:240px" value="{if isset($fieldsList['CLICKLINE_STREET'])}{$fieldsList['CLICKLINE_STREET']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='Road number' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" id="road_number" name="road_number" style="width:240px" value="{if isset($fieldsList['CLICKLINE_ROAD_NUMBER'])}{$fieldsList['CLICKLINE_ROAD_NUMBER']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='Portal' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" id="portal" name="portal" style="width:240px" value="{if isset($fieldsList['CLICKLINE_PORTAL'])}{$fieldsList['CLICKLINE_PORTAL']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='Floor' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" id="floor" name="floor" style="width:240px" value="{if isset($fieldsList['CLICKLINE_FLOOR'])}{$fieldsList['CLICKLINE_FLOOR']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='Door' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" id="door" name="door" style="width:240px" value="{if isset($fieldsList['CLICKLINE_DOOR'])}{$fieldsList['CLICKLINE_DOOR']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='Postcode' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" id="postcode" name="postcode" style="width:240px" value="{if isset($fieldsList['CLICKLINE_POSTCODE'])}{$fieldsList['CLICKLINE_POSTCODE']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='City' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" id="city" name="city" style="width:240px" value="{if isset($fieldsList['CLICKLINE_CITY'])}{$fieldsList['CLICKLINE_CITY']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='Country' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <select name="country" id="country" style="width:250px">
                            {foreach from=$country_list item=country}
                                <option value="{$country['id']|escape:'htmlall':'UTF-8'}" {if isset($fieldsList['CLICKLINE_COUNTRY'])} {if $fieldsList['CLICKLINE_COUNTRY'] eq $country['id']} selected=selected {/if}{/if}>{$country['name']|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </dd>

                    <dt>
                    <label>{l s='Telephone' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" id="telephone" name="telephone" style="width:240px" value="{if isset($fieldsList['CLICKLINE_TELEPHONE'])}{$fieldsList['CLICKLINE_TELEPHONE']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='Fax' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" id="fax" name="fax" style="width:240px" value="{if isset($fieldsList['CLICKLINE_FAX'])}{$fieldsList['CLICKLINE_FAX']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                    <dt>
                    <label>{l s='Email' mod='clickline'}: </label>
                    </dt>
                    <dd>
                        <input type="text" id="email" name="email" style="width:240px" value="{if isset($fieldsList['CLICKLINE_EMAIL'])}{$fieldsList['CLICKLINE_EMAIL']|escape:'htmlall':'UTF-8'}{/if}" />
                    </dd>

                </dl>

            </fieldset>
            <br/>
            <div style="padding-right: 10px;text-align: center;">
                <input type="submit" value="{l s='Save' mod='clickline'}" name="submitSave" id="submitSave" class="button btn" />
            </div>
        </form>
        <div class="clear">&nbsp;</div>
    </div>
</div>