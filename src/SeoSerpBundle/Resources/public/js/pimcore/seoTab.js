pimcore.registerNS("saltid.seoserp.seo.Tab");
saltid.seoserp.seo.Tab = Class.create({

    initialize: function(object, type, data) {
        this.object = object;
        this.type = type;
        this.data = data;
    },

    load: function () {},

    getLayout: function () {
        if (this.layout == null) {

            let seoTitleLength = 0;
            let seoDescriptionLength = 0;

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
                            emptyText: "Content",
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

            try {
                seoTitleLength = this.data.seoTitle.length;
                seoDescriptionLength = this.data.seoDescription.length;

                if(typeof this.data.metadata == "object" && this.data.metadata.length > 0) {
                    for(var r=0; r<this.data.metadata.length; r++) {
                        addMetaData(this.data.metadata[r]);
                    }
                }
            } catch (e) {

            }

            var toolbarConfig = [
                {
                    xtype: 'button',
                    text: t('save'),
                    iconCls: "pimcore_icon_apply",
                    handler: function () {
                        this.seoSaveHandler();
                    }.bind(this)
                }
            ];

            this.seoForm = new Ext.form.Panel({
                autoScroll: true,
                xtype: 'tabpanel',
                items: [
                    {
                        xtype: 'fieldset',
                        title: "Title, Description & Metadata",
                        collapsible: true,
                        width: "100%",
                        border: false,
                        defaults: {
                            labelWidth: 200
                        },
                        items: [
                            {
                                xtype: "hiddenfield",
                                name: 'objectId',
                                value: this.data == null ? this.object.data.general.o_id : this.data.objectId,
                            },
                            {
                                fieldLabel: t('title') + " (" + seoTitleLength + ")",
                                xtype: "textfield",
                                name: 'seoTitle',
                                itemId: 'seoTitle',
                                maxLength: 255,
                                height: 51,
                                width: 700,
                                value: this.data ? this.data.seoTitle : null,
                                enableKeyEvents: true,
                                listeners: {
                                    "keyup": function (el) {
                                        el.labelEl.update(t("title") + " (" + el.getValue().length + "):");
                                    }
                                },
                                style: "font-family: 'Courier New', Courier, monospace;",
                            },
                            {
                                fieldLabel: t('description') + " (" + seoDescriptionLength + ")",
                                xtype: "textarea",
                                name: 'seoDescription',
                                itemId: 'seoDescription',
                                maxLength: 350,
                                height: 51,
                                width: 700,
                                value: this.data ? this.data.seoDescription : null,
                                enableKeyEvents: true,
                                listeners: {
                                    "keyup": function (el) {
                                        el.labelEl.update(t("description") + " (" + el.getValue().length + "):");
                                    }
                                },
                                style: "font-family: 'Courier New', Courier, monospace;",
                            },
                            this.metaDataPanel
                        ]
                    },
                ]
            })

            this.layout = new Ext.Panel({
                border: false,
                layout: "fit",
                iconCls: "pimcore_nav_icon_seo",
                tbar: toolbarConfig,
                tooltip: t('seo'),
                width: "90%",
                bodyStyle: 'margin-left: 10px; margin-right: 10px; padding-right: 20px',
                items: [
                    this.seoForm
                ],
                listeners: {
                    afterrender: function () {
                        pimcore.layout.refresh();
                    }.bind(this)
                }
            });
        }
        return this.layout;
    },

    seoSaveHandler: function () {
        parameters = this.seoForm.form.getFieldValues();

        Ext.Ajax.request({
            url: "/saltid/seoserp/seo/put",
            method: 'PUT',
            params: parameters,
            success: function (response) {
                try {
                    var data = Ext.decode(response.responseText);
                    if(data.success) {
                        pimcore.helpers.showNotification(t("success"), t("saved_successfully"), "success");
                    } else {
                        throw "save error";
                    }
                } catch (e) {
                    pimcore.helpers.showNotification(t("error"), t("saving_failed"), "error");
                }
            },
            failure: function(response) {
                pimcore.helpers.showNotification(t("error"), "something went wrong", "error", null);
            }
        });

    }
});