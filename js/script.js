jQuery( document ).ready(function() {

    jQuery(document).on("focusout","input[class=currency-search-input]",function(){
        var coinIds = jQuery(this).closest('.widget-inside').find('.wallet-app-coin-id-hidden-input').val().split(',');
        if( coinIds.indexOf(jQuery(this).val()) >= 0 || jQuery(this).val() == '' )
        {
            return;
        }
        jQuery(this).closest('.widget-inside').find('.wallet-app-coin-id-hidden-input').val(jQuery(this).closest('.widget-inside').find('.wallet-app-coin-id-hidden-input').val() + ',' + jQuery(this).val());
        jQuery(this).closest('.widget-inside').find('.wallet-app-added-coins-list').append(`
            <div class="coins-list-individual-item">
                ${jQuery(this).val()}
                <div class="coins-list-controlls">
                    <span data-coin-id="${jQuery(this).val()}" class="dashicons dashicons-arrow-up-alt2"></span>
                    <span data-coin-id="${jQuery(this).val()}" class="dashicons dashicons-arrow-down-alt2"></span>
                    <span data-coin-id="${jQuery(this).val()}" class="dashicons dashicons-no-alt"></span>
                </div>
            </div>
        `);
    });
    
    jQuery(document).on('click', '.dashicons-arrow-up-alt2', function(){
        var coinIdsDOM = jQuery(this).closest('.widget-inside').find('.wallet-app-coin-id-hidden-input');
        var coinIds = coinIdsDOM.val().split(',');
        
        var curIndex = jQuery(this).closest('.coins-list-individual-item').index();
        if(curIndex <= 0) return;
        
        
        var temp = coinIds[curIndex];
        coinIds[curIndex] = coinIds[curIndex - 1];
        coinIds[curIndex - 1] = temp;

        var htmlToAppend = jQuery(this).closest('.coins-list-individual-item').html();

        jQuery(this).closest('.wallet-app-added-coins-list').children(`:eq(${curIndex - 1})`).before(`<div class="coins-list-individual-item">${htmlToAppend}</div>`);
        jQuery(coinIdsDOM).val(coinIds.join(','));

        jQuery(this).closest('.coins-list-individual-item').remove();
    });

    jQuery(document).on('click', '.dashicons-arrow-down-alt2', function(){
        var coinIdsDOM = jQuery(this).closest('.widget-inside').find('.wallet-app-coin-id-hidden-input');
        var coinIds = coinIdsDOM.val().split(',');
        
        var curIndex = jQuery(this).closest('.coins-list-individual-item').index();
        if(curIndex >= coinIds.length - 1) return;
        
        var temp = coinIds[curIndex];
        coinIds[curIndex] = coinIds[curIndex + 1];
        coinIds[curIndex + 1] = temp;

        var htmlToAppend = jQuery(this).closest('.coins-list-individual-item').html();

        jQuery(this).closest('.wallet-app-added-coins-list').children(`:eq(${curIndex + 1})`).after(`<div class="coins-list-individual-item">${htmlToAppend}</div>`);
        jQuery(coinIdsDOM).val(coinIds.join(','));

        jQuery(this).closest('.coins-list-individual-item').remove();
    });

    jQuery(document).on('click', '.dashicons-no-alt', function(){

        var coinIdsDOM = jQuery(this).closest('.widget-inside').find('.wallet-app-coin-id-hidden-input');
        var coinIds = coinIdsDOM.val().split(',');

        coinIds.splice(coinIds.indexOf(jQuery(this).attr('data-coin-id')), 1);

        jQuery(coinIdsDOM).val(coinIds.join(','));
        jQuery(this).closest('.coins-list-individual-item').remove();
    });

});