pimcore.registerNS("pimcore.plugin.SeoSerpBundle");

pimcore.plugin.SeoSerpBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.SeoSerpBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        var seoserpMenu = [];

        seoserpMenu.push({
            text: t('seo'),
            iconCls: "pimcore_nav_icon_seo",
            handler: null
        });

        seoserpMenu.push({
            text: t('serp'),
            iconCls: "pimcore_nav_icon_log_admin",
            handler: null
        })

        var extensionManagerMenu = new Ext.Action({
            text: t('seoserp'),
            iconCls: 'pimcore_nav_icon_seo',
            menu: {
                cls: "pimcore_navigation_flyout",
                shadow: false,
                items: seoserpMenu
            }
        });

        layoutToolbar.extensionManagerMenu.add(extensionManagerMenu);
    }
});

var SeoSerpBundlePlugin = new pimcore.plugin.SeoSerpBundle();
