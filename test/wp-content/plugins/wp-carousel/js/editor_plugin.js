(function() {
    tinymce.create('tinymce.plugins.WPCarousel', {
        init : function(ed, url) {
            ed.addButton('wp_carousel', {
                title : 'WP Carousel',
                image : url + '/wp_carousel_tinymce_icon.png',
                onclick : function() {
                   ed.execCommand('mceInsertContent', false, '[wp_carousel]ID[/wp_carousel]');
				   /*
				   ed.windowManager.open({
						file : url + '/get_tinymce_db.php',
						width : 400 + ed.getLang('example.delta_width', 0),
						height : 200 + ed.getLang('example.delta_height', 0),
						inline : 1
					}, {
						plugin_url : url, // Plugin absolute URL
						some_custom_arg : 'custom arg' // Custom argument
					});
					*/
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "WP Carousel Shortcode",
                author : 'Lluis Ulzurrun de Asanza Saez (Sumolari)',
                authorurl : 'http://sumolari.com/',
                infourl : 'http://sumolari.com/',
                version : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('wp_carousel', tinymce.plugins.WPCarousel);
})();
