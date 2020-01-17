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
            text: t('seoRule'),
            iconCls: "pimcore_nav_icon_seo",
            handler: this.showSeoRule
        });

        seoserpMenu.push({
            text: t('serp'),
            iconCls: "pimcore_nav_icon_log_admin",
            handler: function () {
                Ext.Msg.alert('Information', 'Will be available in next release ! :)');
            }
        });

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
    },

    showSeoRule: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        try {
            tabPanel.setActiveTab(pimcore.globalmanager.get("saltid_seoserp_seo_rule_panel").getLayout());
        }
        catch (e) {
            var seoRulePanel = new saltid.seoserp.seo.rule.panel();
            pimcore.globalmanager.add("saltid_seoserp_seo_rule_panel", seoRulePanel);

            tabPanel.add(seoRulePanel.getLayout());
            tabPanel.setActiveTab(seoRulePanel.getLayout());

            seoRulePanel.getLayout().on("destroy", function () {
                pimcore.globalmanager.remove("saltid_seoserp_seo_rule_panel");
            }.bind(this));

            pimcore.layout.refresh();
        }
    },

    postOpenObject: function (object, type) {
        var objectData = object;

        var classId = objectData.data.general.o_classId;
        var className = objectData.data.general.o_className;
        var oId = objectData.data.general.o_id;

        let parameters = {
            'objectId': oId,
            'classId': classId,
            'className': className
        };

        Ext.Ajax.request({
            url: "/saltid/seoserp/seo/get",
            method: 'GET',
            params: parameters,
            success: function (response) {
                let responseData = Ext.JSON.decode(response.responseText);
                let data = responseData.data;
                if (responseData.hasSeoAbleTrait) {
                    var configTab = new saltid.seoserp.seo.Tab(objectData, type, data);
                    var objectTabPanel = object.tab.items.items[1];

                    objectTabPanel.insert(objectTabPanel.items.length, configTab.getLayout());
                    pimcore.layout.refresh();
                }
            },
            failure: function(response) {
                pimcore.helpers.showNotification(t("error"), "something went wrong", "error", null);
            }
        });
    }
});

var SeoSerpBundlePlugin = new pimcore.plugin.SeoSerpBundle();
