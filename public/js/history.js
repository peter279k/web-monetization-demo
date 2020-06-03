$(function () {
    let postData = {
        'web_monetization_event': 'transaction_history',
    };

    $.post('/history', postData, function (response) {
        if (!response || response.length === 0) {
            console.log('It seems that fetching transaction history amounts is failed.');
            $('#transaction_history_amounts').text('Sorry. We cannot fetch latest transaction history amount result :(.');

            return false;
        }

        let total = 0;

        let maxCreatedDateTime = response['max_created_date_time'];
        let minCreatedDateTime = response['min_created_date_time'];

        let historyAmounts = response['history_amount_data'];

        for (historyAmountIndex in historyAmounts) {
            total += Number(historyAmounts[historyAmountIndex].amount);
        }

        let formatted = (total * Math.pow(10, -historyAmounts[historyAmountIndex].assetScale)).toFixed(historyAmounts[historyAmountIndex].assetScale);

        if (total === 0 || formatted === undefined || isNaN(formatted)) {
            $('#transaction_history_amounts').text('Sorry. We cannot fetch latest transaction history amount result :(.');
        } else {
            $('#transaction_history_created_date_time').text('From ' + minCreatedDateTime + ' to ' + maxCreatedDateTime);
            $('#transaction_history_amounts').html('I\'ve made <span class="text-secondary">' + formatted + 'USD dollars</span> :).');
        }
    });
});

