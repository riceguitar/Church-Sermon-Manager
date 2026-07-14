import edit from './components/edit';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

import './style.scss';
import './editor.scss';

registerBlockType( 'sermons-taxonomy-layout/sermons-grid', {
    title: __('Sermons Taxonomy'),
    icon: 'book',
    category: 'common',
    keywords: [
        __('Sermons Taxonomy'),
        __('Grid'),
    ],
    getEditWrapperProps( { postBlockWidth } ) {
        if ( 'wide' === postBlockWidth || 'full' === postBlockWidth ) {
            return { 'data-align': postBlockWidth };
        }
    },
    edit,
    save() {
        // Rendering in PHP
        return null;
    },
    
});
