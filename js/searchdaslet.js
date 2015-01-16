/* SP Search Dashlet */

cj(function ($) {

    function spqsShowResults(contacts) {

        // console.log(contacts);

        $('#spqs-spinner').hide();
        $("#spqs-form input").val('');

        if (contacts.length > 0) {

            var table = $('<table>');
            cj.each(contacts, function(index, contact) {

                var url = CRM.url('civicrm/contact/view', {reset: 1, cid: contact.contact_id });
                var tr = $('<tr>');
                $('<td class="spqs-id"><a href="' + url + '">' + contact.contact_id + '</a></td>').appendTo(tr);
                $('<td class="spqs-name"><a href="' + url + '">' + contact.display_name + '</a></td>').appendTo(tr);
                $('<td class="spqs-address">' + contact.street_address + '</td>').appendTo(tr);
                $('<td class="spqs-postcode">' + contact.postal_code + '</td>').appendTo(tr);
                $('<td class="spqs-city>">' + contact.city + '</td>').appendTo(tr);
                $('<td class="spqs-phone">' + contact.phone + '</td>').appendTo(tr);
                $('<td class="spqs-email">' + (contact.email ? '<a href="mailto:' + contact.email + '">' + contact.email + '</a>' : '') + '</td>').appendTo(tr);
                tr.appendTo(table);

            });
            $('#spqs-results').show().html('').append(table);


        } else {

            $('#spqs-results').show().html('<p>Geen resultaten.</p>');
        }
    }

    function spqsShowError() {
        CRM.alert('Er is een fout opgetreden bij het ophalen van de zoekresultaten.');
    }

    function spqsGetContactsFromRecords(records) {

        var ids = [];
        cj.each(records, function (index, rec) {
            ids.push(rec.contact_id);
        });

        CRM.api3('Contact', 'Get', {
            contact_id: ids,
            sequential: 1
        }).success(function (data) {
            spqsShowResults(data.values);
        }).error(function () {
            spqsShowError();
        });
    }

    function spqsPerformSearch() {

        $('#spqs-results').hide();
        $('#spqs-spinner').show();

        var id = $('#spqs-id').val();
        var postcode = $('#spqs-postcode').val();
        var streetno = $('#spqs-streetno').val();
        var name = $('#spqs-name').val();
        var city = $('#spqs-city').val();
        var communic = $('#spqs-communic').val();

        postcode = postcode.toUpperCase();
        if(postcode && postcode.match(/[0-9]{4}[A-Z]{2}/)) {
            postcode = postcode.substring(0, 4) + ' ' + postcode.substring(4, 6);
        }

        if (postcode && streetno) {

            // Get Address by postal code and street no

            CRM.api3('Address', 'Get', {
                postal_code: postcode,
                street_number: streetno,
                sequential: 1
            }).success(function (data) {
                // Then get contacts for these addresses
                if(data.is_error)
                    spqsShowError(data.error_message);
                else if (data.count > 0) {
                    spqsGetContactsFromRecords(data.values);
                } else {
                    spqsShowResults([]);
                }
            }).error(function () {
                spqsShowError();
            });

        } else if (communic) {

            // Get Contacts by phone OR email
            if (communic.match(/@/)) {

                CRM.api3('Email', 'Get', {
                    email: communic,
                    sequential: 1
                }).success(function (data) {
                    if(data.is_error)
                        spqsShowError(data.error_message);
                    else if(data.count > 0)
                        spqsGetContactsFromRecords(data.values);
                    else
                        spqsShowResults([]);
                }).error(function () {
                    spqsShowError();
                });

            } else {

                var phone = communic.replace(/[^0-9]/g, '');
                CRM.api3('Phone', 'Get', {
                    phone_numeric: phone,
                    sequential: 1
                }).success(function (data) {
                    if(data.is_error)
                        spqsShowError(data.error_message);
                    else if(data.count > 0)
                        spqsGetContactsFromRecords(data.values);
                    else
                        spqsShowResults([]);
                }).error(function () {
                    spqsShowError();
                });
            }

        } else {

            // Get Contact by id, postal code, last_name and/or city
            CRM.api3('Contact', 'get', {
                'contact_id': id,
                'postal_code': postcode,
                'last_name': name,
                'city': city,
                sequential: 1
            }).success(function (data) {
                if(data.is_error)
                    spqsShowError(data.error_message);
                else
                    spqsShowResults(data.values);
            }).error(function () {
                spqsShowError();
            });
        }
    }

    $('#civicrm-dashboard').on('keyup', '#spqs-form input', function (ev) {

        // Perform search on enter
        if (ev.keyCode == 13) {
            spqsPerformSearch();
        }
        // Clear other field groups
        else {
            $("#spqs-form input").not('*[data-group=' + $(this).attr('data-group') + ']').val('');
        }
    });

    $('#civicrm-dashboard').on('submit', '#spqs-form', function (ev) {
        spqsPerformSearch();
    });

});