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

<script type="text/javascript">
    var djlAjaxStoreUrl = baseDir + 'modules/clickline/ajaxStoreShippingInfo.php';
            function DJLupdateCarrierSelectionAndGift(id_carrier, id_service) {
            //
            $('a[name^="carrierC"]').removeClass("clickline_selected").addClass("carrierC");
                    $('#carrierC' + id_carrier + '_' + id_service).addClass("clickline_selected");
                    //
                    djlUpdateCarrierInfo(id_carrier, id_service);
    {if $opc}
            $('#opc_payment_methods').show();
                    $('#opc_payment_methods-overlay').css("display", "none");
    {else}
            $('input[name="processCarrier"]').show();
    {/if}

            var hasInnerText = (document.getElementsByTagName("body")[0].innerText != undefined) ? true : false;
                    // Actualiza el importe de Clickline
                    if (!hasInnerText) {
    {if $ps15}
            $("input[value='{$clickline_carrier|escape:'javascript':'UTF-8'},']").parent().children("label").children("table").children("tbody").children("tr").children("td").children("div.delivery_option_price")[0].textContent = $('input[name=clickline_price_with_tax' + id_carrier + '_' + id_service + ']').val() + " {if $use_taxes == 1}{l s='(tax incl.)' mod='clickline'}{else}{l s='(tax excl.)' mod='clickline'}{/if}";
    {else}
            var click_table = $("table.resume").filter(function() {
            return($("input[value='{$clickline_carrier|escape:'javascript':'UTF-8'},']", $(this)).length > 0);
            });
                    $("div.delivery_option_price", click_table).text($('input[name=clickline_price_with_tax' + id_carrier + '_' + id_service + ']').val() + " {if $use_taxes == 1}{l s='(tax incl.)' mod='clickline'}{else}{l s='(tax excl.)' mod='clickline'}{/if}");
    {/if}
            }
            else {
    {if $ps15}
            $("input[value='{$clickline_carrier|escape:'javascript':'UTF-8'},']").parent().children("label").children("table").children("tbody").children("tr").children("td").children("div.delivery_option_price")[0].innerText = $('input[name=clickline_price_with_tax' + id_carrier + '_' + id_service + ']').val() + " {if $use_taxes == 1}{l s='(tax incl.)' mod='clickline'}{else}{l s='(tax excl.)' mod='clickline'}{/if}";
    {else}
            var click_table = $("table.resume").filter(function() {
            return($("input[value='{$clickline_carrier|escape:'javascript':'UTF-8'},']", $(this)).length > 0);
            });
                    $("div.delivery_option_price", click_table).text($('input[name=clickline_price_with_tax' + id_carrier + '_' + id_service + ']').val() + " {if $use_taxes == 1}{l s='(tax incl.)' mod='clickline'}{else}{l s='(tax excl.)' mod='clickline'}{/if}");
    {/if}
            }

    {if $opc}
            // Actualizar importes resumen en OPC
            updateCarrierSelectionAndGift();
    {/if}
            }
    //]]>
    </script>
    
    <script type="text/javascript">
    // <![CDATA[
    {literal}
        function djlUpdateCarrierInfo(id_carrier, id_service) {
        $.ajax({
        type: 'POST',
                url: djlAjaxStoreUrl,
                async: true,
                cache: false,
                dataType: "json",
                data: {
                'token': '{/literal}{Tools::getToken('ajaxStoreShippingInfo.php')}{literal}',
                        'clickline_id_carrier': id_carrier,
                        'clickline_code_carrier': $('input[name=clickline_code_carrier' + id_carrier + '_' + id_service + ']').val(),
                        'clickline_name_carrier': $('input[name=clickline_name_carrier' + id_carrier + '_' + id_service + ']').val(),
                        'clickline_id_service': id_service,
                        'clickline_price': $('input[name=clickline_price' + id_carrier + '_' + id_service + ']').val(),
                        'clickline_shipping_tax': $('input[name=clickline_shipping_tax' + id_carrier + '_' + id_service + ']').val()
                },
                success: function(jsonData)
                {
                if (jsonData.hasError)
                {
                var errors = '';
                        for (error in jsonData.errors)
                        //IE6 bug fix
                        if (error != 'indexOf')
                        errors += jsonData.errors[error] + "\n";
                        alert(errors);
                }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("TECHNICAL ERROR: unable to update ClickLine shipping information \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
                }
        });
        }
    {/literal}

        function change_action_form()
    {ldelim}
       
        $('input[name^="delivery_option"]').click(function()
    {ldelim}
        $('#carrierTableCL').hide();
    {if $opc}
        $('#opc_payment_methods').show();
                $('#opc_payment_methods-overlay').css("display", "none");
    {else}
        $('input[name="processCarrier"]').show();
    {/if}
    {rdelim});
                $('#id_carrier{$clickline_carrier|escape:'javascript':'UTF-8'}').click(function()
    {ldelim}
        $('#carrierTableCL').show();
    {if $opc}
        if ($('a[name^="carrierC"].clickline_selected').length == 0)
                $('#opc_payment_methods').hide();
    {else}
        if ($('a[name^="carrierC"].clickline_selected').length == 0)
                $('input[name="processCarrier"]').hide();
    {/if}
    {rdelim});
    {rdelim}
        
        $(document).ready(function()
    {ldelim}
    {$clickline_carrier};
    {$id_carrier};
    {if $carrier_sel == ''}

        {if $id_carrier == $clickline_carrier}
        if ($('a[name^="carrierC"].clickline_selected').length == 0)
            {if $opc}
        $('#opc_payment_methods').hide();
            {else}
        $('input[name="processCarrier"]').hide();
            {/if}
        {else}
        $('#carrierTableCL').hide();
        {/if}
    {/if}
    {if $id_carrier != $clickline_carrier}
        clicklineId = $('#carrierTableCL').parent().attr('id').split('_')[$('#carrierTableCL').parent().attr('id').split('_').length-1];
        if(!$('input[name^="delivery_option['+ clicklineId +']"]').is(':checked'))
            $('#carrierTableCL').hide();
    {/if}
        change_action_form();
    {rdelim});
                //]]>
</script>