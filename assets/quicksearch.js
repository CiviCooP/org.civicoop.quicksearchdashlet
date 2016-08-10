/* org.civicoop.quicksearchdashlet JS */

cj(function ($) {

    function qsdashletShowResults(contacts) {

        // console.log(contacts);

        $('#qsdashlet-spinner').hide();
        $("#qsdashlet-form input").val('');

        if (contacts.length > 0) {

            var table = $('<table>');
            cj.each(contacts, function(index, contact) {

                var url = CRM.url('civicrm/contact/view', {reset: 1, cid: contact.contact_id });
                var tr = $('<tr>');
                $('<td class="qsdashlet-id"><a href="' + url + '">' + contact.contact_id + '</a></td>').appendTo(tr);
                $('<td class="qsdashlet-name"><a href="' + url + '">' + contact.display_name + '</a></td>').appendTo(tr);
                $('<td class="qsdashlet-address">' + contact.street_address + '</td>').appendTo(tr);
                $('<td class="qsdashlet-postcode">' + contact.postal_code + '</td>').appendTo(tr);
                $('<td class="qsdashlet-city>">' + contact.city + '</td>').appendTo(tr);
                $('<td class="qsdashlet-phone">' + contact.phone + '</td>').appendTo(tr);
                $('<td class="qsdashlet-email">' + (contact.email ? '<a href="mailto:' + contact.email + '">' + contact.email + '</a>' : '') + '</td>').appendTo(tr);
                tr.appendTo(table);

            });
            $('#qsdashlet-results').show().html('').append(table);


        } else {

            $('#qsdashlet-results').show().html('<p>Geen resultaten.</p>');
        }
    }

    function qsdashletShowError() {
        CRM.alert('An error occurred while fetching search results.');
    }

    function qsdashletGetContactsFromRecords(records) {

        // console.log(records);

        var ids = [];
        cj.each(records, function (index, rec) {
            ids.push(rec.contact_id);
        });

        CRM.api3('Contact', 'Get', {
            contact_id: { 'IN': ids },
            sequential: 1,
            debug: 1
        }).success(function (data) {
            qsdashletShowResults(data.values);
        }).error(function () {
            qsdashletShowError();
        });
    }

    function qsdashletPerformSearch() {

        $('#qsdashlet-results').hide();
        $('#qsdashlet-spinner').show();

        var id = $('#qsdashlet-id').val();
        var postcode = $('#qsdashlet-postcode').val();
        var streetno = $('#qsdashlet-streetno').val();
        var name = $('#qsdashlet-name').val();
        var city = $('#qsdashlet-city').val();
        var communic = $('#qsdashlet-communic').val();

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
                    qsdashletShowError(data.error_message);
                else if (data.count > 0) {
                    qsdashletGetContactsFromRecords(data.values);
                } else {
                    qsdashletShowResults([]);
                }
            }).error(function () {
                qsdashletShowError();
            });

        } else if (communic) {

            // Get Contacts by phone OR email
            if (communic.match(/@/)) {

                CRM.api3('Email', 'Get', {
                    email: communic,
                    sequential: 1
                }).success(function (data) {
                    if(data.is_error)
                        qsdashletShowError(data.error_message);
                    else if(data.count > 0)
                        qsdashletGetContactsFromRecords(data.values);
                    else
                        qsdashletShowResults([]);
                }).error(function () {
                    qsdashletShowError();
                });

            } else {

                var phone = communic.replace(/[^0-9]/g, '');
                CRM.api3('Phone', 'Get', {
                    phone_numeric: phone,
                    sequential: 1
                }).success(function (data) {
                    if(data.is_error)
                        qsdashletShowError(data.error_message);
                    else if(data.count > 0)
                        qsdashletGetContactsFromRecords(data.values);
                    else
                        qsdashletShowResults([]);
                }).error(function () {
                    qsdashletShowError();
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
                    qsdashletShowError(data.error_message);
                else
                    qsdashletShowResults(data.values);
            }).error(function () {
                qsdashletShowError();
            });
        }
    }

    $('#civicrm-dashboard').on('keyup', '#qsdashlet-form input', function (ev) {

        // Perform search on enter
        if (ev.keyCode == 13) {
            qsdashletPerformSearch();
        }
        // Clear other field groups
        else {
            $("#qsdashlet-form input").not('*[data-group=' + $(this).attr('data-group') + ']').val('');
        }
    });

    $('#civicrm-dashboard').on('submit', '#qsdashlet-form', function (ev) {
        qsdashletPerformSearch();
    });

});