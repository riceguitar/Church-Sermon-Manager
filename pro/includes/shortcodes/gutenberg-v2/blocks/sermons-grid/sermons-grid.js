/**
 * Gutenberg Blocks class.
 *
 * @type wp.blocks
 */
const {registerBlockType} = wp.blocks;

/**
 * WordPress's i18n.
 *
 * @type wp.i18n
 */
const {__} = wp.i18n;

/**
 * The block name in PHP and JS.
 *
 * @type {string}
 */
const blockName = 'smp/sermons-grid'; // The block name in PHP and JS.

// Register the block.
registerBlockType(blockName, {
    title: __('Sermons Grid'),
    icon: 'book',
    category: 'common',
    keywords: [
        __('Sermons'),
        __('Grid'),
    ],
    edit() {
        let data = {
            'action': blockName + '_render',
        };

        return jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data,
            async: false,
        }).responseText;
    },
    save() {
        return null;
    }
});
