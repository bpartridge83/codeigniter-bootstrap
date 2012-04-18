$(function(){
    
    $('.dropdown-toggle').dropdown();
    
    // Custom debug.error logging through Airbrake
    amplify.subscribe('debug.error', function(data) {
        window.Airbrake && Airbrake.notify(data[0], data[1][0], data[1][1]);
    });
    
    $('a[data-track]').each(function(){
        var config = $(this).data('track');
        
        if (config === true) {
            config = {
                'name': sprintf('Click: %s', $(this).text())
            };
        }
        
        if (config.name.indexOf('%s') > -1) {
            config.name = sprintf(config.name, $(this).text());
        }
        
        config.id = _.uniqueId('mixpanel_');
        $(this).attr('id', config.id).removeAttr('data-track');
        
        config.selector = sprintf('a#%s', config.id);
        
        debug.log(config);
        
        mixpanel.track_links(config.selector, config.name);
    });
    
    
    $('html:not(.homepage) #header').css({
        height: $('#header').height()
    }).find('.wrapper').addClass('fixed').css({
        top: $('.navbar-inner').height(),
    });
    
    $('#container').masonry({
        itemSelector: '.column'
    });
    
    KeyboardJS.bind.key('Shift + S', null, function() {
        $('#search input').focus();
        return false;
    });
    
	var years = [],
		keys = [];
		
    $('[data-year]').each(function(){
        years.push($(this).data('year'));
    });
    
    $('#search input').bind('focus', function(){
        $(this).parent().find('a.examples').hide();
    }).bind('blur', function(){
        if ($(this).val() == '') {
            $(this).parent().find('a.examples').show();
        }
    });
    
    $('[data-search]').on('click', function(){
        var value = $(this).text();
        mixpanel.track('Click: Search Helper', {'terms':value}, function() {
            debug.log(value);
            $('#search input').val(value).closest('form').trigger('submit');
        });
        return false;
    });

    /*
    $(document).live('keydown', function() {
        var active = KeyboardJS.activeKeys();
        
        for (i = 0; i < active.length; i++) {
            keys.push(active[i]);
        }
        
        if (keys.length > 3) {
            var last = _.last(keys, 4).join('');
            for (i = 0; i < years.length; i++) {
                if (years[i] == last) {
                    debug.log($('[data-year="'+last+'"]'));
                    window.location = $('[data-year="'+last+'"]').attr('href');
                }
            }
        }
    });
    */

    KeyboardJS.bind.key('Shift + L', function() {
        if (!$('input:focus').length) {
            window.location = '/login/admin';
        }
    });
    
    KeyboardJS.bind.key('Shift + H', function() {
        if (!$('input:focus').length) {
            window.location = '/';
        }
    });
    
    /*
    $('table [data-pie]').each(function() {
    
        $(this).css({
            display: 'inline-block',
            height: '16px',
            width: '16px',
            marginLeft: '4px',
            verticalAlign: 'text-top'
        }).attr('id', Math.floor(Math.random()*10001));
        
        var r = Raphael($(this).attr('id')),
            data = $(this).data('pie');
        
        r.piechart(8, 8, 8, 100, { colors: '#cc0000' });
        
    });
    */
    
});