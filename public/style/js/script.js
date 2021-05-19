let icon = document.querySelector('.icon-el i');
if (icon !== null) {
    let className = icon.getAttribute('class');
    if (className.includes('glyphicon-ok-sign')) {
        icon.setAttribute('class', 'fa fa-check');
    } else {
        icon.setAttribute('class', 'fa fa-exclamation-triangle');
    }
}

flatpickr('.datepicker', {
    altInput: true,
    altFormat: 'J F Y, H:i',
    dateFormat: 'Y-m-d H:i:S',
    "locale": "fr"
})
