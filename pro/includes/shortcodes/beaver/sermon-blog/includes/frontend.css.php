<?php if( 'columns' == $settings->layout ) : ?>
article.wpfc_sermon.fl-sermon-columns-<?php echo $settings->sermon_columns; ?> {
    width: calc((100% - <?php echo $settings->sermon_spacing * ( $settings->sermon_columns - 1 ); ?>px) / <?php echo $settings->sermon_columns; ?>);
    float: left;
    margin-right: <?php echo $settings->sermon_spacing; ?>px;
    margin-bottom: <?php echo $settings->sermon_margin; ?>px;
}

.fl-sermon-container-columns .fl-sermon-columns-<?php echo $settings->sermon_columns; ?>:nth-child(<?php echo $settings->sermon_columns; ?>n) {
    margin-right: 0;
}

.fl-sermon-columns .wpfc-sermon-image {
    flex: none;
    margin-top: <?php echo $settings->image_spacing_top; ?>px;
	margin-bottom: <?php echo $settings->image_spacing_bottom; ?>px;
	margin-left: <?php echo $settings->image_spacing_left; ?>px;
	margin-right: <?php echo $settings->image_spacing_right; ?>px;
}

article.wpfc_sermon.fl-sermon-columns-masonry.fl-sermon-columns-<?php echo $settings->sermon_columns; ?> {
	width: calc((100% - <?php echo (24 * ( $settings->sermon_columns - 1  )); ?>px) / <?php echo $settings->sermon_columns; ?>);
    margin-right: 0;
    margin-bottom: 24px;
}

<?php endif ?>
<?php if( 'list' == $settings->layout ) : ?>
.fl-sermon-container .wpfc-sermon-image {
    margin-top: <?php echo $settings->image_spacing_top; ?>px;
	margin-bottom: <?php echo $settings->image_spacing_bottom; ?>px;
	margin-left: <?php echo $settings->image_spacing_left; ?>px;
	margin-right: <?php echo $settings->image_spacing_right; ?>px;
}

article.wpfc_sermon.fl-sermon-list {
    margin-bottom: <?php echo $settings->list_sermon_spacing; ?>px;
}

<?php endif ?>
.fl-module-content .sm-filtering {
    margin-bottom: <?php echo $settings->filter_spacing; ?>px;
}

.fl-sermon-container .wpfc-sermon-inner {
    background-color: #<?php echo $settings->bg_color; ?>;
    border: <?php echo $settings->border_size; ?>px <?php echo $settings->border_type; ?> #<?php echo $settings->border_color; ?>;
    width: 100%;
}

.fl-sermon-container .wpfc-sermon-title-text {
    font-size: <?php echo $settings->title_font_size; ?>px;
    color: #<?php echo $settings->title_color; ?>;
    padding-bottom: <?php echo $settings->title_padding; ?>px;
    display: block;
}

.fl-sermon-container .sermon-description {
    font-size: <?php echo $settings->description_font_size; ?>px;
    color: #<?php echo $settings->description_color; ?>;
    padding-bottom: <?php echo $settings->description_padding; ?>px;
}

.fl-sermon-container a {
    color: #<?php echo $settings->link_color; ?>;
}

.fl-sermon-container a:hover {
    color: #<?php echo $settings->link_hover_color; ?>;
}

.custom-pagination {
	display: block;
	text-align: <?php echo $settings->pagination_alignment; ?>;
}

@media screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) and (min-width: <?php echo $global_settings->responsive_breakpoint+1; ?>px) {
<?php if( 'columns' == $settings->layout ) : ?>
    article.wpfc_sermon.fl-sermon-columns-<?php echo $settings->sermon_columns; ?> {
        width: calc((100% - <?php echo $settings->sermon_spacing * ( $settings->sermon_columns_medium - 1 ); ?>px) / <?php echo $settings->sermon_columns_medium; ?>);
        margin-right: <?php echo $settings->sermon_spacing; ?>px !important;
    }

	article.wpfc_sermon.fl-sermon-columns-masonry.fl-sermon-columns-<?php echo $settings->sermon_columns; ?> {
	   width: calc((100% - <?php echo 24 * ( $settings->sermon_columns_medium - 1 ); ?>px) / <?php echo $settings->sermon_columns_medium; ?>);
	   margin-right: 0 !important;
	} 
	
    .fl-sermon-container-columns .fl-sermon-columns-<?php echo $settings->sermon_columns; ?>:nth-child(<?php echo $settings->sermon_columns_medium; ?>n) {
        margin-right: 0 !important;
    }

    .fl-sermon-container-masonry.fl-sermon-container-columns {
        -webkit-column-count: <?php echo $settings->sermon_columns_medium; ?>;
        -moz-column-count: <?php echo $settings->sermon_columns_medium; ?>;
        column-count: <?php echo $settings->sermon_columns_medium; ?>;
    }

<?php endif ?>
}

@media screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
<?php if( 'columns' == $settings->layout ) : ?>
    article.wpfc_sermon.fl-sermon-columns-<?php echo $settings->sermon_columns; ?> {
        width: calc((100% - <?php echo $settings->sermon_spacing * ( $settings->sermon_columns_responsive - 1 ); ?>px) / <?php echo $settings->sermon_columns_responsive; ?>);
        margin-right: 0 !important;
    }
	
	article.wpfc_sermon.fl-sermon-columns-masonry.fl-sermon-columns-<?php echo $settings->sermon_columns; ?> {
	   width: calc((100% - <?php echo 24 * ( $settings->sermon_columns_responsive - 1 ); ?>px) / <?php echo $settings->sermon_columns_responsive; ?>);
	   margin-right: 0 !important;
	} 

    .fl-sermon-container-masonry.fl-sermon-container-columns {
        -webkit-column-count: <?php echo $settings->sermon_columns_responsive; ?>;
        -moz-column-count: <?php echo $settings->sermon_columns_responsive; ?>;
        column-count: <?php echo $settings->sermon_columns_responsive; ?>;
    }

<?php endif ?>
}
