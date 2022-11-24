function toggleBookingModal(mode){
    let modal = document.getElementById('booking-modal');
    let header = document.getElementsByClassName('whb-main-header')[0];

    switch(mode){
        case 'show':
            modal.classList.add('show');
            header.classList.add('hide');
        break;

        case 'hide':
            modal.classList.remove('show');
            header.classList.remove('hide');
        break;
    }
}