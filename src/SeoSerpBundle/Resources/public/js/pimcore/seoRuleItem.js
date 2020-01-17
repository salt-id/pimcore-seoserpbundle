pimcore.registerNS("saltid.seoserp.seo.rule.item");
saltid.seoserp.seo.rule.item = Class.create({

    initialize: function(parent, data) {
        this.parent = parent;
        this.data = data;

        this.currentIndex = 0;
        var panel = this.getSettings();

        this.parent.panel.add(panel);
        this.parent.panel.setActiveTab(panel);
        this.parent.panel.updateLayout();
    },

    getSettings: function () {
        this.settingsForm = new Ext.form.FormPanel({
            id: "pimcore_target_groups_panel_" + this.data.id,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            bodyStyle: "padding:10px;",
            autoScroll: true,
            border:false,
            buttons: [{
                text: t("save"),
                iconCls: "pimcore_icon_apply",
                handler: this.save.bind(this)
            }],
            items: [
                {
                    xtype: "textfield",
                    fieldLabel: t("name"),
                    name: "name",
                    width: 350,
                    disabled: true,
                    value: this.data.name
                },
                {
                    xtype: "textfield",
                    fieldLabel: t('routeName'),
                    name: "routeName",
                    width: 350,
                    value: this.data.routeName
                },
                {
                    xtype: "textfield",
                    fieldLabel: t('routeVariable'),
                    name: "routeVariable",
                    width: 350,
                    value: this.data.routeVariable
                },
                {
                    xtype: "textfield",
                    fieldLabel: t('className'),
                    name: "className",
                    width: 350,
                    value: this.data.className
                },
                {
                    xtype: "textfield",
                    fieldLabel: t('classField'),
                    name: "classField",
                    width: 350,
                    value: this.data.classField
                },
                {
                    name: "active",
                    fieldLabel: t("active"),
                    xtype: "checkbox",
                    checked: this.data["active"]
                }
            ]
        });

        return this.settingsForm;
    },

    save: function () {
        var saveData = {
            settings: this.settingsForm.getForm().getFieldValues()
        };

        Ext.Ajax.request({
            url: "/saltid/seoserp/seorule/save",
            method: 'PUT',
            params: {
                id: this.data.id,
                data: Ext.encode(saveData)
            },
            success: function () {
                this.parent.getTree().getStore().load();
                pimcore.helpers.showNotification(t("success"), t("saved_successfully"), "success");
            }.bind(this)
        });
    }
});