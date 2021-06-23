/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

$(document).ready(function () {
    $('.bootboxconfirm').on('click', function (e) {
        e.preventDefault();
        var hrefTo = $(this).attr('data-href');
        var dataTitle = $(this).attr('data-poptitle');
        var dataMsg = $(this).attr('data-popmsg');
        var dialog = bootbox.confirm({
            centerVertical: true,
            title: dataTitle,
            message: dataMsg,
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Отменить',
                    className: 'btn-primary'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Подтвердить',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if (result) {
                    window.location = hrefTo;
                }
            }
        });
        dialog.on('shown.bs.modal', function () {
            dialog.find(".btn-primary:first").focus();
        });
    });

    $('.bootboxformconfirm').on('click', function (e) {
        e.preventDefault();
        var currentForm = $(this).attr('data-form');
        var dataTitle = $(this).attr('data-poptitle');
        var dataMsg = $(this).attr('data-popmsg');
        var dialog = bootbox.confirm({
            centerVertical: true,
            title: dataTitle,
            message: dataMsg,
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel',
                    className: 'btn-primary'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if (result) {
                    $('#' + currentForm).submit();
                }
            }
        });
        dialog.on('shown.bs.modal', function () {
            dialog.find(".btn-primary:first").focus();
        });
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $('.openPopup').click(function () {
        var Base64 = {
            // private property
            _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
            // public method for decoding
            decode: function (input) {
                var output = "";
                var chr1, chr2, chr3;
                var enc1, enc2, enc3, enc4;
                var i = 0;

                input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

                while (i < input.length) {
                    enc1 = this._keyStr.indexOf(input.charAt(i++));
                    enc2 = this._keyStr.indexOf(input.charAt(i++));
                    enc3 = this._keyStr.indexOf(input.charAt(i++));
                    enc4 = this._keyStr.indexOf(input.charAt(i++));
                    chr1 = (enc1 << 2) | (enc2 >> 4);
                    chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                    chr3 = ((enc3 & 3) << 6) | enc4;

                    output = output + String.fromCharCode(chr1);

                    if (enc3 != 64) {
                        output = output + String.fromCharCode(chr2);
                    }
                    if (enc4 != 64) {
                        output = output + String.fromCharCode(chr3);
                    }
                }

                output = Base64._utf8_decode(output);
                return output;
            },
            // private method for UTF-8 decoding
            _utf8_decode: function (utftext) {

                var string = "";
                var i = 0;
                var c, c2, c3;

                while (i < utftext.length) {
                    c = utftext.charCodeAt(i);

                    if (c < 128) {
                        string += String.fromCharCode(c);
                        i++;
                    } else if ((c > 191) && (c < 224)) {
                        c2 = utftext.charCodeAt(i + 1);
                        string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                        i += 2;
                    } else {
                        c2 = utftext.charCodeAt(i + 1);
                        c3 = utftext.charCodeAt(i + 2);
                        string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                        i += 3;
                    }

                }

                return string;
            }
        };

        var dataURL = $(this).attr('data-href');
        var dataIMG = $(this).attr('data-img');
        var dataLINK = $(this).attr('data-link');
        var dataID = $(this).attr('data-id');
        var dataTitle = $(this).attr('data-poptitle');
        var dataBase64 = $(this).attr('data-encbase64');
        var databody = '';
        $('.modal-title').html(dataTitle);
        if (dataIMG) {
            databody = "<img src=" + dataIMG + " class='img-fluid'>";
            if (dataLINK) {
                databody = databody + "<hr><div class='text-center mt-2'><a href='index.php?hal=" + dataLINK + "&getId=" + dataID + "' class='btn btn-success'>Member Detail and Manual Approval</a> <a href='javascript:;' data-dismiss='modal' class='btn btn-danger'>Dismiss</a></div>";
            }
            $('.modal-body').html(databody);
        }
        if (dataURL) {
            $('.modal-body').load(dataURL);
        }
        if (dataBase64) {
            $('.modal-body').html(Base64.decode(dataBase64));
        }
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    });

    $('#summernotemini').summernote({
        height: 128,
        maxHeight: null,
        dialogsInBody: true,
        toolbar: [
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['view', ['codeview']],
        ]
    });
    $('#summernote').summernote({
        height: 256,
        maxHeight: null,
        dialogsInBody: true,
        toolbar: [
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview']],
        ]
    });
    $('#summernotemaxi').summernote({
        height: 480,
        maxHeight: null,
        dialogsInBody: true,
        toolbar: [
            ['view', ['undo', 'redo']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'hr']],
            ['view', ['fullscreen', 'codeview']],
        ]
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
    });

    var activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        $('.nav-pills a[href="' + activeTab + '"]').tab('show');
    }

});

function checkMember(agv, dataval, dataid) {
    $("#resultGetMbr" + dataid).html("<i class='fas fa-cog fa-spin'></i>");
    jQuery.ajax({
        url: "../common/index.php",
        data: 'agv=' + agv + '-' + dataval,
        type: "POST",
        success: function (data) {
            $("#resultGetMbr" + dataid).html(data);
        },
        error: function () {
        }
    });
}

function checkBoxCnt(checkboxid, showelement, toggleelement) {
    $('#' + checkboxid).change(function () {
        if (this.checked) {
            $('#' + showelement).removeClass().addClass('d-block');
            if (toggleelement)
                $('#' + toggleelement).removeClass().addClass('d-none');
            else
                $('#' + toggleelement).removeClass().addClass('d-block');
        } else {
            $('#' + showelement).removeClass().addClass('d-none');
            if (toggleelement)
                $('#' + toggleelement).removeClass().addClass('d-block');
            else
                $('#' + toggleelement).removeClass().addClass('d-none');
        }
    });
}

function getinitdo(urldo, param) {
    $("#newvernum").text('...');
    $.ajax({
        type: 'POST',
        url: urldo,
        data: "initdo=" + param,
        success: function (result) {
            if (result !== '') {
                $("#newverstr").text('New version available!');
                $("#newvernum").text('v' + result);
            } else {
                $("#newvernum").text('');
            }
        }
    });
}

function dowithdrawfee(fees, fcap, cursym) {
    var txamount = document.getElementById("txamount").value;
    var feestr = (parseFloat(txamount) * parseFloat(fees)) /100;
    if (feestr > fcap && fcap > 0) {
        //feestr = fcap;
    }
    var isfee = parseFloat(feestr).toFixed(2);
    var amount2get = parseFloat(txamount - isfee);
    document.getElementById("txamountstr1").innerHTML = 'Комиссия: ' + cursym + isfee;
    document.getElementById("txamountstr2").innerHTML = 'Для получения: ' + cursym + amount2get;
}

function fillBalance() {
    var amount = document.getElementById('fillAmount').value;
    $.ajax({
        type: 'POST',
        url: '../member/actions/payeer_withdraw.php',
        data: "amount=" + amount,
        success: function (result) {
            var form = document.getElementById('fillBalanceForm').parentElement;
            var fr = document.createElement("div");
            fr.innerHTML = result;
            form.appendChild(fr);
            document.getElementById('FillBalanceFormPay').submit();
        },
        error: function (error) {

        }
    });
}

function payTariff(type) {
    $.ajax({
        type: 'POST',
        data: "type=" + type,
        url: '../member/actions/tariffpay.php',
        success: function (result) {
            var json = JSON.parse(result);
            if (json[0] == 1) {
                window.location = json[1];
            } else {
                var form = document.getElementById('dopayform').parentElement;
                var fr = document.createElement("div");
                fr.innerHTML = json[1];
                form.appendChild(fr);
                document.getElementById('FillBalanceFormPay').submit();
            }
        },
        error: function (error) {

        }
    });
}

$("#fillBalanceForm").on('submit', function (e) {
    fillBalance();
    e.preventDefault();
});

function subscribeSelected(selection) {
    selection = selection[0];
    var price = selection.getAttribute('data-price');
    document.getElementById('subscribeplanprice_span').innerText = "₽ " + (Math.round(price * 100) / 100).toFixed(2);
}

function paySubscribe() {
    var index = document.getElementById('subscribePlans').selectedIndex - 1;
    if (index == -1)
        return;
    $.ajax({
        type: 'POST',
        data: 'type=' + index,
        url: '../member/actions/subscribepay.php',
        success: function (result) {
            var json = JSON.parse(result);
            if (json[0] == 1) {
                window.location = json[1];
                location.reload();
            } else {
                var form = document.getElementById('subscribePlans').parentElement;
                var fr = document.createElement("div");
                fr.innerHTML = json[1];
                form.appendChild(fr);
                document.getElementById('FillBalanceFormPay').submit();
            }
        },
        error: function (error) {

        }
    });
}

function moveToTariff(id) {
    $.ajax({
        type: 'POST',
        data: 'type=' + id,
        url: '../member/actions/changetariff.php',
        success: function (result) {
            var json = JSON.parse(result);
            if (json[0] == 1) {
                window.location = json[1];
                location.reload();
            } else {
                var form = document.getElementById('subscribePlans').parentElement;
                var fr = document.createElement("div");
                fr.innerHTML = json[1];
                form.appendChild(fr);
                document.getElementById('FillBalanceFormPay').submit();
            }
        },
        error: function (error) {

        }
    });
}
function generateNewKey() {
    $.ajax({
        type: 'POST',
        url: '../member/actions/generate_new_telegram_key.php',
        success: function (result) {
            var json = JSON.parse(result);
            if (json[0] == 1) {
                document.getElementById('telegramhash').value = json[1];
            } else {
                var form = document.getElementById('subscribePlans').parentElement;
                var fr = document.createElement("div");
                fr.innerHTML = json[1];
                form.appendChild(fr);
                document.getElementById('FillBalanceFormPay').submit();
            }
        },
        error: function (error) {

        }
    });
}

$('button[type="submit"]').on('click', function() {
    // skipping validation part mentioned above
});