(function ($) {
    $.fn.selectChain = function (options) {
        var defaults = {
            key: "id",
            value: "label",
            selected: '',
            beforeSend: function () {},
            afterSuccess: function () {}
        };

        var settings = $.extend({}, defaults, options);

        if (!(settings.target instanceof $))
            settings.target = $(settings.target);

        return this.each(function () {
            var $$ = $(this);
            $$.change(function () {
                var data = null;
                if (typeof settings.data == 'string') {
                    data = settings.data + '&' + this.name + '=' + $$.val();
                } else if (typeof settings.data == 'object') {
                    data = new Array();
                    $.each(settings.data, function (k, v) {
                        // console.log("k : "+k+" v : "+v);
                        if(v=='batch_no')
                        {
                            data += k + '=' + encodeURIComponent($('#' + v).val()) + '&';
                        }
                        else
                        {
                            data += k + '=' + $('#' + v).val() + '&';
                        }
                    });

                }
                settings.target.empty();
                $.ajax({
                    url: settings.url,
                    data: data,
                    type: (settings.type || 'get'),
                    beforeSend: function () {
                        settings.beforeSend()
                    },
                    dataType: 'json',
                    success: function (j) {
                        var options = [], i = 0, o = null;
                        //console.log(j[1]);
                        if(j.hasOwnProperty(1)){
                            if (j[1].id === '') {
                                $("#batch_no").prop("disabled", true);
                                $("#expire_date").prop("disabled", true);
                                $("#expire_date option:first").attr('selected', 'selected');
                                $('.challan_qty').html(j[1].challan_qty);
                                $('#challan_qty').val(j[1].challan_qty);
                                $("#expire_date option:last").remove();
                                //$("#challan_qty").attr("value", j[1].challan_qty).text(j[1].challan_qty);
                            } else {
                                $("#batch_no").prop("disabled", false);
                                $("#expire_date").prop("disabled", false);
                                $('.challan_qty').html('');
                                $('#challan_qty').val('');
                            }
                        }

                        for (i = 0; i < j.length; i++) {
                            // required to get around IE bug (http://support.microsoft.com/?scid=kb%3Ben-us%3B276228)
                            o = document.createElement("OPTION");
                            o.value = typeof j[i] == 'object' ? j[i][settings.key] : j[i];
                            o.text = typeof j[i] == 'object' ? j[i][settings.value] : j[i];
                            settings.target.get(0).options[i] = o;
                        }

                        // hand control back to browser for a moment
                        setTimeout(function () {
                            // settings.targe.trigger('change');
                        }, 0);
                        settings.afterSuccess();
                    },
                    error: function (xhr, desc, er) {
                        // add whatever debug you want here.
                        alert("an error occurred");
                    }
                });
            });
        });
    };
})(jQuery);
