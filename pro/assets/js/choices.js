jQuery(window).on('window -> elementor/init', function () {
    // Create our extension.
    let ControlChoices = elementor.modules.controls.BaseData.extend({
        // When the element loads.
        onReady: function () {
            // Init the script. `this.el` is our element.
            this._choices = new Choices(jQuery(this.el).find('select')[0], {
                removeItemButton: true,
            });
        },

        // On Elementor save.
        saveValue: function () {
            this.setValue(this.ui.select.val() == null ? [] : this.ui.select.val());
        },

        // When control is being unloaded.
        onBeforeDestroy: function () {
            this.saveValue();
            this._choices.destroy();
        },
    });

    // Register our extension.
    elementor.addControlView('choices', ControlChoices);
});
