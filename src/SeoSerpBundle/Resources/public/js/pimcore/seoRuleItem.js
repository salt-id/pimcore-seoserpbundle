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
        var metaKeyType = new Ext.data.Store({
            fields: ["i", "a"],
            data: [
                {"i": "property", "a": "property"},
                {"i": "name", "a": "name"}
            ]
        });

        var addMetaData = function (value) {

            if(typeof value != "object") {
                value = "";
            }
            var buttonCount = this.metaDataPanel.query("button").length+1;

            var count = buttonCount - 2;

            var compositeField = new Ext.form.FieldContainer({
                layout: 'hbox',
                hideLabel: true,
                items: [
                    {
                        xtype: 'combobox',
                        emptyText: "Key Type",
                        width: 136,
                        store: metaKeyType,
                        queryMode: "local",
                        displayField: "a",
                        valueField: "i",
                        value: value.keyType,
                        name: "metadata[" + count+ "][keyType]",
                    },
                    {
                        xtype: "textfield",
                        name: "metadata[" + count+ "][keyValue]",
                        value: value.keyValue,
                        width: 200,
                        emptyText: "Key Value",
                    },
                    {
                        xtype: "textfield",
                        name: "metadata[" + count+ "][content]",
                        value: value.content,
                        width: 300,
                        emptyText: "Content Default",
                    }
                ]
            });

            compositeField.add({
                xtype: "button",
                iconCls: "pimcore_icon_delete",
                handler: function (compositeField, el) {
                    this.metaDataPanel.remove(compositeField);
                    this.metaDataPanel.updateLayout();
                }.bind(this, compositeField)
            });

            this.metaDataPanel.add(compositeField);
            this.metaDataPanel.updateLayout();
        }.bind(this);

        try {
            if(typeof this.data.metadata == "object" && this.data.metadata.length > 0) {
                for(var r=0; r<this.data.metadata.length; r++) {
                    addMetaData(this.data.metadata[r]);
                }
            }
        } catch (e) {

        }

        this.metaDataPanel = new Ext.form.FieldSet({
            title: t("meta"),
            collapsible: false,
            autoHeight:true,
            width: 700,
            style: "margin-top: 20px;",
            items: [{
                xtype: "toolbar",
                style: "margin-bottom: 10px;",
                items: ["->", {
                    xtype: 'button',
                    iconCls: "pimcore_icon_add",
                    handler: addMetaData
                }]
            }]
        });

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
                    xtype: "combo",
                    fieldLabel: t('routeName'),
                    name: "routeName",
                    itemId: "routeName",
                    width: 350,
                    store: this.getRouteName(),
                    valueField: "id",
                    displayField: "name",
                    fields: ['id', 'name'],
                    mode: "local",
                    triggerAction: "all",
                    value: this.data.routeName,
                    listeners: {
                        afterrender: function(field,b,c) {
                            if (field.initialValue !== null) {
                                this.settingsForm.getComponent("routeVariable").setStore(this.getRouteVariable(field.initialValue));
                            }
                        }.bind(this),
                        change: function(field, value) {
                            this.settingsForm.getComponent("routeVariable").setStore(this.getRouteVariable(value));
                        }.bind(this)
                    }
                },
                {
                    xtype: "combo",
                    fieldLabel: t('routeVariable'),
                    name: "routeVariable",
                    itemId: "routeVariable",
                    width: 350,
                    valueField: "name",
                    displayField: "name",
                    fields: ['name'],
                    mode: "local",
                    triggerAction: "all",
                    value: this.data.routeVariable
                },
                {
                    xtype: "combo",
                    fieldLabel: t('className'),
                    name: "className",
                    itemId: "className",
                    width: 350,
                    valueField: "name",
                    displayField: "name",
                    fields: ['name'],
                    mode: "local",
                    triggerAction: "all",
                    value: this.data.className,
                    store: this.getPimcoreClass(),
                    listeners: {
                        afterrender: function(field,b,c) {
                            if (field.initialValue !== null) {
                                this.settingsForm.getComponent("classField").setStore(this.getPimcoreClassFields(field.initialValue));
                            }
                        }.bind(this),
                        change: function(field, value) {
                            this.settingsForm.getComponent("classField").setStore(this.getPimcoreClassFields(value));
                        }.bind(this)
                    }
                },
                {
                    xtype: "combo",
                    fieldLabel: t("classField"),
                    name: "classField",
                    itemId: "classField",
                    width: 350,
                    valueField: "name",
                    displayField: "name",
                    fields: ['name'],
                    mode: "local",
                    triggerAction: "all",
                    value: this.data.classField,
                },
                {
                    name: "active",
                    fieldLabel: t("active"),
                    xtype: "checkbox",
                    checked: this.data.active
                },
                this.metaDataPanel
            ]
        });

        return this.settingsForm;
    },

    getRouteName: function() {
        var routeNameStore = new Ext.data.Store({
            autoDestroy: false,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: '/saltid/seoserp/route/list',
                reader: {
                    type: 'json',
                }
            }
        });

        return routeNameStore;
    },

    getRouteVariable: function(routeId) {
        var routeVariableStore = new Ext.data.Store({
            autoDestroy: false,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: '/saltid/seoserp/route/variables',
                extraParams: { id: routeId },
                reader: {
                    type: 'json',
                }
            }
        });

        return routeVariableStore;
    },

    getPimcoreClass: function() {
      var pimcoreClass = new Ext.data.Store({
          autoDestroy: false,
          autoLoad: true,
          proxy: {
              type: 'ajax',
              url: '/saltid/seoserp/class/list',
              reader: {
                  type: 'json',
              }
          }
      });

      return pimcoreClass;
    },

    getPimcoreClassFields: function(pimcoreClassId) {
        var pimcoreClassFields = new Ext.data.Store({
            autoDestroy: false,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: '/saltid/seoserp/class/fields',
                extraParams: { id: pimcoreClassId },
                reader: {
                    type: 'json',
                }
            }
        });

        return pimcoreClassFields;
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
            success: function (response) {
                res = Ext.decode(response.responseText);

                if (res.success) {
                    pimcore.helpers.showNotification(t("success"), res.message, "success");
                }
                if (!res.success) {
                    pimcore.helpers.showNotification(t("warning"), res.message, "warning");
                }

                this.parent.getTree().getStore().load();
            }.bind(this)
        });
    }
});