/**
 * Pimcore
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.pimcore.org/license
 *
 * @copyright  Copyright (c) 2009-2010 elements.at New Media Solutions GmbH (http://www.elements.at)
 * @license    http://www.pimcore.org/license     New BSD License
 */

pimcore.registerNS("pimcore.document.tags.textarea");
pimcore.document.tags.textarea = Class.create(pimcore.document.tag, {

    initialize: function(id, name, options, data) {
        this.id = id;
        this.name = name;
        this.setupWrapper();
        if (!options) {
            options = {};
        }
        if (!data) {
            data = "";
        }

        options.value = data;
        options.name = id + "_editable";

        if(!options.width) {
            options.width = Ext.get(id).getWidth()-2;
        }

        this.element = new Ext.form.TextArea(options);
        this.element.render(id);
    },

    getValue: function () {
        return this.element.getValue();
    },

    getType: function () {
        return "textarea";
    }
});