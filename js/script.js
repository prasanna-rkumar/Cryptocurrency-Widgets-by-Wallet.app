jQuery( document ).ready(function() {

    $(document).on("focusout","input[name=walletAppCurrencySearch]",function(){
        $(`#widget-wallet_app_currency_widget-3-coinID option[value="${$(this).val()}"`).prop("selected", true);
        console.log($('#widget-wallet_app_currency_widget-3-coinID').val());
        $('#widget-wallet_app_currency_widget-3-coinID').val($('#widget-wallet_app_currency_widget-3-coinID').val() + ',' + $(this).val());
    });

    var coins = $('#widget-wallet_app_currency_widget-3-coinID').val().split(',');
    for(var i = 0; i < coins.length; i++)
    {
        document.getElementById('widget-wallet_app_currency_widget[3][coinIDList]').innerHTML = document.getElementById('widget-wallet_app_currency_widget[3][coinIDList]').innerHTML + `
            <div class="coins-list-individual-item">
                ${coins[i]} 
                <div class="coins-list-controlls">
                    ${i == 0 ? '' : '<span class="dashicons dashicons-arrow-up-alt2"></span> '}
                    ${i == coins.length - 1 ? '' : '<span class="dashicons dashicons-arrow-down-alt2"></span>'}
                    
                    <span data-coin-id="${coins[i]}" class="dashicons dashicons-no-alt"></span>
                </div>
            </div>
        `;
    }
    
    $(document).on('click', '.dashicons-arrow-up-alt2', function(){
        console.log('up clicked');
    });

    $(document).on('click', '.dashicons-arrow-down-alt2', function(){
        console.log('down clicked');
    });

    $(document).on('click', '.dashicons-no-alt', function(){
        console.log('remove clicked');
        var coinIds = $('#widget-wallet_app_currency_widget-3-coinID').val().split(',');
        console.log(coinIds);
        coinIds.splice(coinIds.indexOf($(this).attr('data-coin-id')), 1);
        console.log(coinIds);
        $('#widget-wallet_app_currency_widget-3-coinID').val(coinIds.join(','));
        $(this).closest('.coins-list-individual-item').remove();
    });

});