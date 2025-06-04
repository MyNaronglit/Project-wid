jQuery(document).ready(function($) {
    /* Start */

    /* Gallery */
    $('.panel-heading').click(function() {
        if ($(this).hasClass('panel-active')) {
            return false;
        } else {
            $('.panel-heading').removeClass('panel-active');
            $(this).addClass('panel-active');
        }

    });

    $(".thumbnails").find("a.zoom").off('click');
    $('.thumbnails .zoom').on('click', function(e) {
        var photo_fullsize = $(this).attr('href');
        $('.woocommerce-main-image img').attr('src', photo_fullsize);
        $('a.woocommerce-main-image').attr('href', photo_fullsize);
        return false;
    });

    /* Sidebar */
    $('.current-cat-parent').parent().addClass('cat-on-submenu');

    $('.sidebar-cat > ul > li > a').removeAttr("href");
    $('.sidebar-cat > ul > li > ul').hide();
    $('.sidebar-cat > ul > li.current-cat-parent > ul').show();
    $('.sidebar-cat > ul > li.current-cat > ul').show();
    $('.sidebar-cat > ul > li').click(function() {
        $('.sidebar-cat > ul > li > ul').hide();
        $('.sidebar-cat > ul > li.current-cat-parent > ul').show();
        $(this).find('.children').show();
    });

    /* News */
    $('.shortcut-news').click(function() {
        $('.news-img div').removeClass('active');
        $('.news-img div:first-child').addClass('active');
    });
    $('.read-news').click(function() {
        $('.news-img div').removeClass('active');
        $('.news-img div:first-child').addClass('active');
    });

    $('#carousel-news').carousel({
        interval: false
    });

    $('#carousel-image').carousel({
        interval: false
    });

    /* Vehicle */
    $(".re_brand").hide();
    $(".acc_brand").hide();
    $("#products").change(function() {
        $(".all_brand").hide();
        $('#re_brands').prop('selectedIndex', 0);
        $('#acc_brands').prop('selectedIndex', 0);
        $('#models').prop('selectedIndex', 0);
        if ($(this).val() == 'replacement-parts') {
            $(".acc_brand").hide();
            $(".re_brand").show();
        } else if ($(this).val() == 'accessories') {
            $(".re_brand").hide();
            $(".acc_brand").show();
        }
    });

    $("#re_brands").change(function() {
        var $dropdown = $(this);
        $.getJSON("http://www.wdi.co.th/wp-content/themes/wdi/inc/vehicle.json", function(data) {

            var key = $dropdown.val();
            var vals = [];

            switch (key) {
                case 're_bus':
                    vals = data.re_bus.split(",");
                    break;
                case 're_chevrolet':
                    vals = data.re_chevrolet.split(",");
                    break;
                case 're_ford':
                    vals = data.re_ford.split(",");
                    break;
                case 're_hino':
                    vals = data.re_hino.split(",");
                    break;
                case 're_isuzu':
                    vals = data.re_isuzu.split(",");
                    break;
                case 're_kawasaki':
                    vals = data.re_kawasaki.split(",");
                    break;
                case 're_kia':
                    vals = data.re_kia.split(",");
                    break;
                case 're_komatsu':
                    vals = data.re_komatsu.split(",");
                    break;
                case 're_mazda':
                    vals = data.re_mazda.split(",");
                    break;
                case 're_mitsubishi':
                    vals = data.re_mitsubishi.split(",");
                    break;
                case 're_nissan':
                    vals = data.re_nissan.split(",");
                    break;
                case 're_suzuki':
                    vals = data.re_suzuki.split(",");
                    break;
                case 're_toyota':
                    vals = data.re_toyota.split(",");
                    break;
            }
            var $models = $("#models");
            $models.empty();
            $models.append("<option value=''>--- Choose Model ---</option>");
            $.each(vals, function(index, value) {
                var slug = value.replace(/ /g, "-").replace("-/-", "-").replace("-/-", "-");
                $models.append("<option value='re_" + slug + "'>" + value + "</option>");
            });
        });
    });

    $("#acc_brands").change(function() {
        var $dropdown = $(this);
        $.getJSON("http://www.wdi.co.th/wp-content/themes/wdi/inc/vehicle.json", function(data) {

            var key = $dropdown.val();
            var vals = [];

            switch (key) {
                case 'acc_universal':
                    vals = data.acc_universal.split(",");
                    break;
                case 'acc_chevrolet':
                    vals = data.acc_chevrolet.split(",");
                    break;
                case 'acc_ford':
                    vals = data.acc_ford.split(",");
                    break;
                case 'acc_honda':
                    vals = data.acc_honda.split(",");
                    break;
                case 'acc_isuzu':
                    vals = data.acc_isuzu.split(",");
                    break;
                case 'acc_lexus':
                    vals = data.acc_lexus.split(",");
                    break;
                case 'acc_mazda':
                    vals = data.acc_mazda.split(",");
                    break;
                case 'acc_mitsubishi':
                    vals = data.acc_mitsubishi.split(",");
                    break;
                case 'acc_nissan':
                    vals = data.acc_nissan.split(",");
                    break;
                case 'acc_toyota':
                    vals = data.acc_toyota.split(",");
                    break;
            }
            var $models = $("#models");
            $models.empty();
            $models.append("<option value=''>--- Choose Model ---</option>");
            $.each(vals, function(index, value) {
                var slug = value.replace(/ /g, "-").replace("-/-", "-").replace("-/-", "-");
                $models.append("<option value='acc_" + slug + "'>" + value + "</option>");
            });
        });
    });

    /* Disble button */
    var empty = true;

    $('#products, #re_brands, #acc_brands').change(function() {
        $('#products, #re_brands, #acc_brands').each(function() {
            empty = true;
        });
    });

    $('#models').change(function() {
        $('#products, #re_brands, #acc_brands, #models').each(function() {
            if ($(this).val() == '') {
                empty = true;
            } else {
                empty = false;
            }
        });
    });

    $('.vehicle-search-btn').click(function(e) {
        if (empty == true) {
            e.preventDefault();
            alert('All fields are required!');
        } else {

        }
    });

    /* End */
})