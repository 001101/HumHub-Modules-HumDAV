function updateAddressbookAddress() {
    let selectorValue = $('#ical_addressbook_selector').val();
    let defaultUrl = $('#ical_addressbook_address').data('default-url');
    $('#ical_addressbook_address').text(defaultUrl.replace('[ADDRESSBOOK-ID]', selectorValue));
}
