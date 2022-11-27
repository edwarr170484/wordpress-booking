function getDateTimes(date) {
    $.post(wplb_ajax_obj.ajaxurl, {
        _ajax_nonce: wplb_ajax_obj.nonce,
        action: "get_available_times",
        date: date
    }, function (data) {
        let times = [];

        if (data.times.length > 0) {
            data.times.forEach((time) => {
                times.push(`
                    <div class="form__select-option" data-option="${time}" onclick="setSelectedItem($(this), null)">
                        <span>${time}</span>
                    </div>
                `);
            });
        }

        $("#date__times").html(times.join(''));
    }
    );
}

function setSelectedItem(item, param) {
    let value = item.data('option');
    let label = item.find('span').text();
    item.parent().next('input[type="hidden"]').val(value);
    item.parent().parent().find('.form__field-label').addClass('form__field-label_selected').text(label);

    if (param) {
        getDateTimes(param)
    }
}

function setMultipleSelectedItem(item, event) {
    event.stopPropagation();
    let value = item.data('option');
    if (item.hasClass('form__select-option_check')) {
        item.removeClass('form__select-option_check').addClass('form__select-option_plus');
        item.find('input[type="hidden"]').val(NULL);
    } else {
        item.removeClass('form__select-option_plus').addClass('form__select-option_check');
        item.find('input[type="hidden"]').val(value);
    }
}

function createOrder(event) {
    let orderData = new FormData(event.target);
    orderData.append('_ajax_nonce', wplb_ajax_obj.nonce);
    orderData.append('action', 'register_new_order');

    $.ajax({
        url: wplb_ajax_obj.ajaxurl,
        data: orderData,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (response) {
            if (response.error == 0) {
                window.location.href = "/order-success";
            }
        }
    });

    return false;
}

$(document).ready(function () {
    $('.modal__dialog').click(function () {
        $('.form__select').removeClass('form__select_opened');
    })
});