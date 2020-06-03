$(function () {
    console.log('Web Monetization status is detected...');

    $('#pending_web_monetization').hide();

    if (document.monetization === undefined || $('meta[name="monetization"]').length === 0) {
        console.log('Web Monetization is not supported on this page...');

        $('#locked_section_one').html('This is the locked section one, You\'ve to enable and pay with Web Monetization to unlock it :)!');
        $('#locked_section_two').html('This is the locked section two, You\'ve to enable and pay with Web Monetization to unlock it :)!');

        $('#locked_section_one').attr('class', 'text-warning');
        $('#locked_section_two').attr('class', 'text-warning');

        $('#web_monetization_clicked').text('The Web Monetization has been disabled!');
        $('#web_monetization_clicked').attr('class', 'text-danger');

        $('#calculate_web_monetization').attr('class', 'btn btn-primary disabled');
        $('#calculate_web_monetization').attr('disabled', '');
    } else {
        let total = 0;
        let scale;

        $('#pending_web_monetization').show();

        document.monetization.addEventListener('monetizationstart', startEventHandler);
        document.monetization.addEventListener('monetizationstop', stopEventHandler);
        document.monetization.addEventListener('monetizationpending', pendingEventHandler);
        document.monetization.addEventListener('monetizationprogress', ev => {
            console.log('The Web Monetization event is progressed');

            if (total === 0) {
                scale = ev.detail.assetScale;
                console.log('Web Monetization Scale is: ' + scale);
            }

            total += Number(ev.detail.amount);

            let formatted = (total * Math.pow(10, -scale)).toFixed(scale);

            if ($('#calculate_web_monetization').text() !== 'Calculate Web Monetization!') {
                $('#web_monetization_clicked').text('Thank you! I\'ve made ' + formatted + 'USD on this page :)!');
            } else {
                $('#web_monetization_clicked').text('');
            }

            let postData = ev.detail;
            postData.web_monetization_event = 'pending';
            $.post('/blog', postData, function (response) {
                if (response['monetization_id']) {
                    console.log('The monetization stored has been successful');
                }
            });
        });
    }

    $('#disable_web_monetization').click(function () {
        let data = {'web_monetization_event': 'disabled'};

        $.post('/blog', data, function (response) {
            if (response['message'] === 'disabled') {
                window.location.reload();
            }
        });
    });

    $('#enable_web_monetization').click(function () {
        let data = {'web_monetization_event': 'enabled'};

        $.post('/blog', data, function (response) {
            if (response['message'] === 'enabled') {
                window.location.reload();
            }
        });
    });

    $('#calculate_web_monetization').click(function () {
        if ($(this).text() === 'Stop calculating Web Monetization') {
            $(this).text('Calculate Web Monetization!');
            $(this).attr('class', 'btn btn-primary');
        } else {
            $(this).text('Stop calculating Web Monetization');
            $(this).attr('class', 'btn btn-warning');
        }
    });
});

function startEventHandler() {
    console.log('The Web Monetization event is started');

    let data = {'web_monetization_event': 'started'};

    if ($('#locked_section_one').html().indexOf('unlocked section') === -1) {
        $.post('/blog', data, function (response) {
            if (response['message']) {

                let unlockedSectionOne = response['message']['unlocked_section_one'];
                let unlockedSectionTwo = response['message']['unlocked_section_two'];

                $('#locked_section_one').html(unlockedSectionOne);
                $('#locked_section_two').html(unlockedSectionTwo);

                $('#locked_section_one').attr('class', 'text-success');
                $('#locked_section_two').attr('class', 'text-success');

                $('#pending_web_monetization').hide();
            }
        });
    }
}

function stopEventHandler() {
    console.log('The Web Monetization event is stopped');
}

function pendingEventHandler() {
    console.log('The Web Monetization event is pending');
}
