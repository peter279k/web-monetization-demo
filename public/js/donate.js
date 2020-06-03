$(function () {

    if (document.monetization === undefined || $('meta[name="monetization"]').length === 0) {
        console.log('Web Monetization is not supported on this page...');
    } else {
        let total = 0;
        let scale;

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

            $('#web_monetization_donated').text('Thank you! I\'ve made ' + formatted + 'USD on this donation page :)!');

            let postData = ev.detail;
            postData.web_monetization_event = 'pending';
            $.post('/donate', postData, function (response) {
                if (response['monetization_id']) {
                    console.log('The monetization stored has been successful');
                }
            });
        });
    }

    $('#disable_web_monetization').click(function () {
        let data = {
            'web_monetization_event': 'disabled',
            'event_page': 'donate',
        };

        $.post('/donate', data, function (response) {
            if (response['message'] === 'disabled') {
                window.location.reload();
            }
        });
    });

    $('#enable_web_monetization').click(function () {
        let data = {
            'web_monetization_event': 'enabled',
            'event_page': 'donate',
        };

        $.post('/donate', data, function (response) {
            if (response['message'] === 'enabled') {
                window.location.reload();
            }
        });
    });
});

function startEventHandler(event) {
    console.log('Start event: ' + event);
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
            }
        });
    }
}

function stopEventHandler(event) {
    console.log('Stop event:' + event);
    console.log('The Web Monetization event is stopped');
}

function pendingEventHandler(event) {
    console.log('Pending event: ' + event);
    console.log('The Web Monetization event is pending');
}
