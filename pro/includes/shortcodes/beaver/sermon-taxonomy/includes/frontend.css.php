<?php if( 'grid' == $settings->taxonomy_layout ) : ?>
.fl-term-container-grid .wpfc-term-grid-image {
    margin-bottom: <?php echo $settings->term_image_padding; ?>px;
}

.wpfc-term-inner.fl-term-grid {
    background-color: #<?php echo $settings->bg_color; ?>;
    border: <?php echo $settings->border_size; ?>px <?php echo $settings->border_type; ?> #<?php echo $settings->border_color; ?>;
    width: 100%;
}

.wpfc-term-inner.fl-term-grid .wpfc-term-title {
    text-align: <?php echo $settings->title_alignment; ?>;
}

.wpfc-term-inner.fl-term-grid .wpfc-term-description {
    font-size: <?php echo $settings->description_font_size; ?>px;
    color: #<?php echo $settings->description_color; ?>;
    padding-bottom: <?php echo $settings->description_padding; ?>px;
    text-align: <?php echo $settings->description_alignment; ?>;
    display: block;
}

.wpfc-term-inner.fl-term-grid .wpfc-term-description-read-more {
    text-align: <?php echo $settings->description_alignment; ?>;
}

.wpfc-term-inner.fl-term-grid .wpfc-term-content {
    padding: <?php echo $settings->content_spacing; ?>px;
}

<?php endif ?>
<?php if( 'list' == $settings->taxonomy_layout ) : ?>
.wpfc-term-first-letter {
    font-size: <?php echo $settings->letter_font_size; ?>px;
    color: #<?php echo $settings->letter_color; ?>;
    padding-bottom: <?php echo $settings->letter_bottom_padding; ?>px;
    padding-top: <?php echo $settings->letter_top_padding; ?>px;
    display: block;

}

<?php endif ?>
.wpfc-term-inner.fl-term-grid.fl-term-column-<?php echo $settings->term_columns; ?> {
    width: calc((100% - <?php echo $settings->term_spacing * ( $settings->term_columns - 1 ); ?>px) / <?php echo $settings->term_columns; ?>);
    float: left;
    margin-right: <?php echo $settings->term_spacing; ?>px;
}

.wpfc-term-inner.fl-term-grid.fl-term-column-<?php echo $settings->term_columns; ?> {
    margin-bottom: <?php echo $settings->term_margin; ?>px;
}

.wpfc-term-inner.fl-term-grid.fl-term-column-<?php echo $settings->term_columns; ?>:nth-child(<?php echo $settings->term_columns; ?>n) {
    margin-right: 0;
}

.fl-term-container-list .fl-term-list.fl-term-column-<?php echo $settings->term_columns; ?> {
    width: calc((100% - <?php echo $settings->term_spacing * ( $settings->term_columns - 1 ); ?>px) / <?php echo $settings->term_columns; ?>);
    float: left;
    margin-right: <?php echo $settings->term_spacing; ?>px;
}

.fl-term-container-list .fl-term-list.fl-term-column-<?php echo $settings->term_columns; ?>:nth-child(<?php echo $settings->term_columns; ?>n) {
    margin-right: 0;
}

.wpfc-term-inner .wpfc-term-title {
    font-size: <?php echo $settings->title_font_size; ?>px;
    color: #<?php echo $settings->title_color; ?>;
	padding-bottom: <?php echo $settings->title_padding; ?>px;
    display: block;
}

.wpfc-term-pagination {
	text-align: <?php echo $settings->term_pagination_alignment; ?>;
}

@media screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) and (min-width: <?php echo $global_settings->responsive_breakpoint+1; ?>px) {
    .wpfc-term-inner.fl-term-grid.fl-term-column-<?php echo $settings->term_columns; ?> {
        width: calc((100% - <?php echo $settings->term_spacing * ( $settings->term_columns_medium - 1 ); ?>px) / <?php echo $settings->term_columns_medium; ?>);
        margin-right: <?php echo $settings->term_spacing; ?>px !important;
    }

    .wpfc-term-inner.fl-term-grid.fl-term-column-<?php echo $settings->term_columns; ?>:nth-child(<?php echo $settings->term_columns_medium; ?>n) {
        margin-right: 0 !important;
    }

    .fl-term-container-list .fl-term-list.fl-term-column-<?php echo $settings->term_columns; ?> {
        width: calc((100% - <?php echo $settings->term_spacing * ( $settings->term_columns_medium - 1 ); ?>px) / <?php echo $settings->term_columns_medium; ?>);
        margin-right: <?php echo $settings->term_spacing; ?>px;
    }

    .fl-term-container-list .fl-term-list.fl-term-column-<?php echo $settings->term_columns; ?>:nth-child(<?php echo $settings->term_columns_medium; ?>n) {
        margin-right: 0;
    }
}

@media screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
    .wpfc-term-inner.fl-term-grid.fl-term-column-<?php echo $settings->term_columns; ?> {
        width: calc((100% - <?php echo $settings->term_spacing * ( $settings->term_columns_responsive - 1 ); ?>px) / <?php echo $settings->term_columns_responsive; ?>);
        margin-right: 0px !important;
    }

    .fl-term-container-list .fl-term-list.fl-term-column-<?php echo $settings->term_columns; ?> {
        width: calc((100% - <?php echo $settings->term_spacing * ( $settings->term_columns_responsive - 1 ); ?>px) / <?php echo $settings->term_columns_responsive; ?>);
        margin-right: 0px;
    }
}


























