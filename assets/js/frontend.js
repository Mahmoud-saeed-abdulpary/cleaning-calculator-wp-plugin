(function($) {
    'use strict';

    let roomTypes = [];
    let rooms = [];
    let roomCounter = 0;

    $(document).ready(function() {
        initCalculator();
    });

    function initCalculator() {
        loadRoomTypes();
        bindEvents();
    }

    function loadRoomTypes() {
        $.ajax({
            url: cpcFrontend.ajaxurl,
            type: 'POST',
            data: {
                action: 'cpc_get_room_types',
                nonce: cpcFrontend.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    roomTypes = response.data;
                }
            }
        });
    }

    function bindEvents() {
        // Add room button
        $(document).on('click', '#cpc-add-room', function() {
            addRoom();
        });

        // Remove room button
        $(document).on('click', '.cpc-remove-room', function() {
            const roomItem = $(this).closest('.cpc-accordion-item');
            const roomIndex = parseInt(roomItem.data('room-index'));
            removeRoom(roomIndex);
        });

        // Toggle accordion
        $(document).on('click', '.cpc-accordion-toggle', function() {
            const accordionItem = $(this).closest('.cpc-accordion-item');
            const accordionContent = accordionItem.find('.cpc-accordion-content');
            
            accordionItem.toggleClass('active');
            accordionContent.slideToggle(300);
        });

        // Room type change
        $(document).on('change', '.cpc-room-type', function() {
            const roomItem = $(this).closest('.cpc-accordion-item');
            const roomIndex = parseInt(roomItem.data('room-index'));
            updateRoomCalculation(roomIndex);
        });

        // Area change
        $(document).on('input', '.cpc-room-area', function() {
            const roomItem = $(this).closest('.cpc-accordion-item');
            const roomIndex = parseInt(roomItem.data('room-index'));
            updateRoomCalculation(roomIndex);
        });

        // Request quote button
        $(document).on('click', '#cpc-request-quote', function() {
            if (rooms.length === 0) {
                alert(cpcFrontend.strings.addRoom || 'Please add at least one room');
                return;
            }
            showQuoteForm();
        });

        // Submit quote form
        $(document).on('submit', '#cpc-quote-form-element', function(e) {
            e.preventDefault();
            submitQuote();
        });

        // Modal close
        $(document).on('click', '#cpc-modal-close, #cpc-modal-overlay', function() {
            hideQuoteForm();
        });
        
        // Prevent modal content clicks from closing modal
        $(document).on('click', '.cpc-modal-content', function(e) {
            e.stopPropagation();
        });
        
        // Close modal on ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#cpc-quote-modal').is(':visible')) {
                hideQuoteForm();
            }
        });
    }

    function addRoom() {
        roomCounter++;
        const template = $('#cpc-room-template').html();
        const roomHtml = template.replace(/\{\{index\}\}/g, roomCounter);
        
        $('#cpc-rooms-accordion').append(roomHtml);
        $('#cpc-no-rooms').hide();

        // Populate room type options
        const roomItem = $(`.cpc-accordion-item[data-room-index="${roomCounter}"]`);
        const selectElement = roomItem.find('.cpc-room-type');
        
        roomTypes.forEach(function(type) {
            selectElement.append(`<option value="${type.id}" data-price="${type.price_per_sqm}">${type.name} - ${type.price_per_sqm} ${cpcFrontend.currency}/m²</option>`);
        });

        // Open the newly added room
        roomItem.addClass('active');
        roomItem.find('.cpc-accordion-content').show();

        // Add to rooms array
        rooms.push({
            index: roomCounter,
            room_type_id: '',
            room_type_name: '',
            area: 0,
            price_per_sqm: 0,
            subtotal: 0
        });
    }

    function removeRoom(roomIndex) {
        $(`.cpc-accordion-item[data-room-index="${roomIndex}"]`).remove();
        rooms = rooms.filter(room => room.index !== roomIndex);
        
        if (rooms.length === 0) {
            $('#cpc-no-rooms').show();
        }
        
        updateGrandTotal();
    }

    function updateRoomCalculation(roomIndex) {
        const roomItem = $(`.cpc-accordion-item[data-room-index="${roomIndex}"]`);
        const roomTypeId = roomItem.find('.cpc-room-type').val();
        const roomTypeName = roomItem.find('.cpc-room-type option:selected').text();
        const pricePerSqm = parseFloat(roomItem.find('.cpc-room-type option:selected').data('price')) || 0;
        const area = parseFloat(roomItem.find('.cpc-room-area').val()) || 0;
        const subtotal = area * pricePerSqm;

        // Update room data
        const roomData = rooms.find(room => room.index === roomIndex);
        if (roomData) {
            roomData.room_type_id = roomTypeId;
            roomData.room_type_name = roomTypeName;
            roomData.area = area;
            roomData.price_per_sqm = pricePerSqm;
            roomData.subtotal = subtotal;
        }

        // Update UI
        roomItem.find('.cpc-room-title').text(`${roomTypeName || 'Room'} #${roomIndex}`);
        roomItem.find('.cpc-subtotal-amount').text(subtotal.toFixed(2) + ' ' + cpcFrontend.currency);

        updateGrandTotal();
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        const totalsHtml = [];

        rooms.forEach(function(room) {
            if (room.subtotal > 0) {
                grandTotal += room.subtotal;
                totalsHtml.push(`
                    <div class="cpc-total-item">
                        <span>${room.room_type_name}</span>
                        <span>${room.subtotal.toFixed(2)} ${cpcFrontend.currency}</span>
                    </div>
                `);
            }
        });

        $('#cpc-totals-list').html(totalsHtml.join(''));
        $('#cpc-grand-total').text(grandTotal.toFixed(2));
    }

    function showQuoteForm() {
        const displayMode = cpcFrontend.formDisplay || 'modal';

        if (displayMode === 'modal') {
            const modal = $('#cpc-quote-modal');
            modal.fadeIn(300, function() {
                modal.addClass('cpc-modal-open');
            });
            $('body').addClass('cpc-modal-active').css('overflow', 'hidden');
            
            // Scroll modal content to top
            $('.cpc-modal-content').scrollTop(0);
        } else if (displayMode === 'inline') {
            $('#cpc-quote-form-container').slideDown(300);
            
            // Scroll to form
            $('html, body').animate({
                scrollTop: $('#cpc-quote-form-container').offset().top - 50
            }, 500);
        } else if (displayMode === 'replace') {
            $('.cpc-calculator-body').hide();
            $('#cpc-quote-form-container').show();
            
            // Scroll to top
            $('html, body').animate({
                scrollTop: $('#cpc-calculator').offset().top - 50
            }, 500);
        }
    }

    function hideQuoteForm() {
        const modal = $('#cpc-quote-modal');
        modal.removeClass('cpc-modal-open');
        
        setTimeout(function() {
            modal.fadeOut(300);
        }, 100);
        
        $('body').removeClass('cpc-modal-active').css('overflow', '');
        $('#cpc-quote-form-container').slideUp(300);
        $('.cpc-calculator-body').show();
    }

    function submitQuote() {
        const submitBtn = $('#cpc-submit-quote');
        const btnText = submitBtn.find('.cpc-btn-text');
        const btnLoader = submitBtn.find('.cpc-btn-loader');
        
        // Validate
        if (rooms.length === 0) {
            showFormNotice('error', cpcFrontend.strings.addRoom || 'Please add at least one room');
            return;
        }

        const formData = {
            name: $('#cpc-customer-name').val().trim(),
            email: $('#cpc-customer-email').val().trim(),
            phone: $('#cpc-customer-phone').val().trim(),
            address: $('#cpc-customer-address').val().trim(),
            message: $('#cpc-customer-message').val().trim()
        };

        // Client-side validation
        if (!formData.name || !formData.email || !formData.phone) {
            showFormNotice('error', cpcFrontend.strings.requiredField || 'Please fill in all required fields');
            return;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            showFormNotice('error', cpcFrontend.strings.invalidEmail || 'Please enter a valid email address');
            return;
        }

        // Disable submit button and show loader
        submitBtn.prop('disabled', true);
        btnText.hide();
        btnLoader.show();

        $.ajax({
            url: cpcFrontend.ajaxurl,
            type: 'POST',
            data: {
                action: 'cpc_submit_quote',
                nonce: cpcFrontend.nonce,
                name: formData.name,
                email: formData.email,
                phone: formData.phone,
                address: formData.address,
                message: formData.message,
                rooms: JSON.stringify(rooms)
            },
            success: function(response) {
                if (response.success) {
                    showFormNotice('success', response.data.message || cpcFrontend.strings.success);
                    $('#cpc-quote-form-element')[0].reset();
                    
                    // Reset calculator
                    setTimeout(function() {
                        hideQuoteForm();
                        resetCalculator();
                    }, 2000);
                } else {
                    showFormNotice('error', response.data.message || cpcFrontend.strings.error);
                }
            },
            error: function() {
                showFormNotice('error', cpcFrontend.strings.error || 'An error occurred');
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                btnText.show();
                btnLoader.hide();
            }
        });
    }

    function showFormNotice(type, message) {
        const notice = $('#cpc-form-notice');
        notice.removeClass('cpc-notice-success cpc-notice-error')
              .addClass('cpc-notice-' + type)
              .html(message)
              .slideDown(300);

        setTimeout(function() {
            notice.slideUp(300);
        }, 5000);
    }

    function resetCalculator() {
        rooms = [];
        roomCounter = 0;
        $('#cpc-rooms-accordion').empty();
        $('#cpc-no-rooms').show();
        updateGrandTotal();
    }

})(jQuery);