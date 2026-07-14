import edit from './components/edit';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

import './style.scss';
import './editor.scss';

registerBlockType( 'sermons-blog-layout/sermons-grid', {
    title: __('Sermons'),
    icon: 'book',
    category: 'common',
    keywords: [
        __('Sermons'),
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
