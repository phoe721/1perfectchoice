$(document).ready(function() {
    // Set up jQuery Validation defaults
    jQuery.validator.setDefaults({
        debug: false,
        success: 'valid'
    });

    // Form 1 validation
    var form = $('#form');
    form.validate({
        rules: {
            file: {
                required: true
            }
        }
    });

    // Form 2 validation
    var form2 = $('#form2');
    form2.validate({
        rules: {
            input: {
                required: true,
                pattern: /^[A-Za-z0-9\-+x\/\._\&]+$/
            }
        }
    });

    // Get UID
    $.post('script/getUID.php', { getUID: 'yes' }, function(id) {
        if (typeof id != 'undefined') {
            $('#uid').val(id);
        } else {
            console.log('Unable to get ID for this process!');
        }
    });

    // Get Task List
    $.getJSON('script/getTaskList.php', function(data) {
        $.each(data, function(key, value) {
            $('#task2').append('<option value=' + key + '>' + value + '</option>');
        });
    });

    // Dropzone configuration
    if (window.Dropzone) {
        Dropzone.options.box3 = {
            url: 'script/uploadImg.php',
            paramName: 'file',
            maxFilesize: 2,
            init: function() {
                this.on('sending', function(file, xhr, formData) {
                    var uid = $('#uid').val();
                    formData.append('uid', uid);
                });
            }
        };
    }

    // UI Setup
    $('#box1').hide();
    $('#box2').hide();
    $('#run').attr('disabled', true);

    // Buttons/events
    $('#reset').click(function() {
        location.reload();
    });

    $('#upload_button').click(function() {
        $('#box1').toggle('slow');
    });

    $('#check_button').click(function() {
        $('#box2').toggle('slow');
    });

    $('#run').click(function() {
        $.post('script/runQueue.php');
    });

    $('#inventory_percentage').click(function() {
        $.post('script/checkInventoryPercentageByVendor.php', function(data) {
            alert(data);
        });
    });

    $('#clear_inventory').click(function() {
        if (confirm("Are you sure you want to clear inventory?")) {
            $.post('script/truncateInventory.php');
            alert("Inventory cleared!");
        } else {
            alert("Inventory not cleared!");
        }
    });

    // Validate file selection
    $('#file').change(function() {
        $('#output2').html('');
        if (!form.valid()) {
            console.log('Invalid File!');
        }
    });

    // Show/Hide Dropzone
    $('#dropzone').click(function() {
        $("div#box3").toggle('slow');
    });

    // Upload file handler
    $('#upload').click(function() {
        if (form.valid()) {
            var $f1 = $('#file');
            var uid = $('#uid').val();
            var task = $('#task2').val();
            var formData = new FormData();
            formData.append('uid', uid);
            formData.append('task', task);
            if ($f1.val()) formData.append('file', $f1.get(0).files[0]);

            // Upload file
            $.ajax({
                url: 'script/upload.php',
                data: formData,
                type: 'POST',
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(output) {
                    $('#output2').append(output + '<br>');
                }
            });

            // Enable run button
            $('#run').attr("disabled", false);

            // Wait for result
            var time = 10000;
            var prev = '', cur = '';
            var check = setInterval(function() {
                $.post('script/getStatus.php', { uid: uid }, function(result) {
                    cur = result.status;
                    if (cur.match(/Done/)) {
                        $('#output2').html('').append(result.status + ' ');
                        $('#output2').append('<a href="' + result.link + '" target="_blank" download>result.txt</a><br>');
                        clearInterval(check);
                    } else if (prev !== cur) {
                        $('#output22').append(result.status + '<br>');
                        prev = cur;
                    }
                }, 'json');
            }, time);
        }
    });
});
