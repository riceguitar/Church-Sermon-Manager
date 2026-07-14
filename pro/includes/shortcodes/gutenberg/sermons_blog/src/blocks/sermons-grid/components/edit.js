const { Component } = wp.element;
const { InspectorControls, BlockControls, AlignmentToolbar, BlockAlignmentToolbar } = wp.editor;
const { Fragment } = wp.element;
const { withSelect } = wp.data;
const { __ } = wp.i18n;
const { QueryControls, PanelBody, Spinner, Placeholder, RangeControl, SelectControl, Toolbar, ToggleControl, TextControl } = wp.components;
const { decodeEntities } = wp.htmlEntities;

class PostGridEdit extends Component{

    constructor(props) {
        super(...arguments);
    }
    
    render(){

        const { attributes, categoriesList, setAttributes, latestPosts, className, postTypes, media } = this.props;
        const { post_type, categories, order, orderBy, postscount, columns, imagePosition, equalHeight, displayMasonry, featuredType, postLayout, displayFilters, displayFilterTopics, displayFilterSeries, displayFilterPreacher, displayFilterBook, displayFilterServiceType, displayFilterDates, displaySermonFeaturedType, displaySermonSeries, displayTitle, displaySermonDate, displaySermonDescription, displaySermonReadMoreButton, displaySermonAudio, displaySermonPreacher, displaySermonBiblePassage, displaySermonServiceType, postReadMoreButtonText, displayPagination, paginationTotalPages, displayPrevNext, previousLabel, nextLabel, paginationAlignment, titlePadding, descriptionPadding, align, gridLayoutStyle, columnGap} = attributes;
        const hasPosts = Array.isArray(latestPosts) && latestPosts.length;
        const hasPostTypes = Array.isArray(postTypes) && postTypes.length;
        const mediaItems = Array.isArray(media) && media.length;
        
		if( !hasPosts || !hasPostTypes || !mediaItems){ 
            return(
                <Fragment>
                    <Placeholder
                        icon="admin-post"
                        label={ __( 'No Semons Available' ) }
                    >
                        {
                            !Array.isArray(latestPosts) || !Array.isArray(hasPostTypes) || !Array.isArray(mediaItems) ? <Spinner /> : __( 'No sermons found.' )
                        }
                    </Placeholder>
                </Fragment>
            );
        }
        
        const displayPosts = latestPosts.length > postscount ? latestPosts.slice(0, postscount) : latestPosts;
        const layoutControls = [
            {
                icon: 'grid-view',
                title: __( 'Grid View' ),
                onClick: () => setAttributes( { postLayout: 'grid' } ),
                isActive: postLayout === 'grid',
            },
            {
                icon: 'list-view',
                title: __( 'List View' ),
                onClick: () => setAttributes( { postLayout: 'list' } ),
                isActive: postLayout === 'list',
            },
        ];
        
        const gridView = (postLayout === 'grid' ) ? `gpl-column-${columns}` : ``;
        const gridViewWrapper = (postLayout === 'list' ) ? `list-layout` : ``;
		const gridEqualHeight = equalHeight ? `equal-height` : ``;
        const itemStyle = {
            '--item-padding-left-right' : columnGap ? `${columnGap}px` : undefined,
            '--item-margin-bottom' : columnGap ? `${columnGap*2}px` : undefined,
            '--item-height' : columnGap ? `${300-columnGap}px` : undefined,
        };
		const defaultFeaturedTypes =  [
            { label: __('Image'), value: 'image'  },
            { label: __('Video'), value: 'video'  },
        ];
		const numbers = [];
		for (var i = 2; i < paginationTotalPages + 1; i++) {
			numbers.push(i);
		}
		const listItems = numbers.map((numbers) =>
				<a className="page-numbers" href="#">{numbers}</a>
			);
		
		return (
            <Fragment>
                <BlockControls>
                     <AlignmentToolbar
                         value={align}
                         onChange={(nextAlign) => {
                             setAttributes({align: nextAlign});
                         }}
                     />
               
                    <Toolbar controls={ layoutControls } />
                    
                </BlockControls>
                <InspectorControls key="inspector">
                    <PanelBody title={ __( 'Layout Settings' ) }>
                        
                        { postLayout === 'grid' &&
                            <RangeControl
                                label={__('Columns')}
                                value={columns}
                                onChange={(value) => setAttributes({columns: value})}
                                min={1}
                                max={6}
                            />
                        }

                        <RangeControl
                            label = { __('Column Gap' ) }
                            value = { columnGap }
                            min = { 0 }
                            max = { 20 }
                            onChange = { ( value ) => setAttributes({ columnGap: value }) }
                        />
						
						{postLayout === 'grid' && !displayMasonry &&
							<ToggleControl
                                label={__('Equal Height')}
                                checked={!!equalHeight}
                                onChange={(value) => setAttributes({equalHeight: value})}
                            />

                        }
						
						{postLayout === 'grid' && !equalHeight &&
							<ToggleControl
                                label={__('Show Masonry')}
                                checked={!!displayMasonry}
                                onChange={(value) => setAttributes({displayMasonry: value})}
                            />

                        }
						
						{postLayout === 'list' &&
						  <SelectControl
                            label={__('Image position')}
                            options={[
								{ label: __('Left'), value: 'left'  },
								{ label: __('Right'), value: 'right'  },
								{ label: __('Top'), value: 'top'  },
							]}
                            value={imagePosition}
                            onChange={(newValue) => {
                                setAttributes({imagePosition: newValue })
                            }}
                        />	
						}	  	

                       
                        
                    </PanelBody>

                    <PanelBody title={ __( 'Query Settings' ) } initialOpen={ false }>
                        <QueryControls
                            { ...{ order, orderBy }}
                            numberOfItems={postscount}
                            categoriesList={ categoriesList }
                            selectedCategoryId = {categories}
                            onOrderChange = { ( value ) => setAttributes({ order: value })}
                            onOrderByChange={ ( value ) => setAttributes( { orderBy: value } ) }
                            onCategoryChange={ ( value ) => setAttributes( {
                                categories: '' !== value ? value : undefined
                            }) }
                            onNumberOfItemsChange={ (value) => setAttributes({ postscount: value }) }
                            
                        />
                    </PanelBody>

					
					<PanelBody title={ __( 'Filters' ) } initialOpen={ false }>
                        
                        <ToggleControl
                            label = { __('Display Filters') }
                            checked = { !!displayFilters }
                            onChange = { (value) => setAttributes( { displayFilters: value } ) }
                        />
												
						{displayFilters &&
                            <ToggleControl
                                label={__('Display Filter Preacher')}
                                checked={!!displayFilterPreacher}
                                onChange={(value) => setAttributes({displayFilterPreacher: value})}
                            />

                        }
						
						{displayFilters &&
                            <ToggleControl
                                label={__('Display Filter Series')}
                                checked={!!displayFilterSeries}
                                onChange={(value) => setAttributes({displayFilterSeries: value})}
                            />

                        }
						
						{displayFilters &&
                            <ToggleControl
                                label={__('Display Filter Topics')}
                                checked={!!displayFilterTopics}
                                onChange={(value) => setAttributes({displayFilterTopics: value})}
                            />

                        }
						
						{displayFilters &&
                            <ToggleControl
                                label={__('Display Filter Book')}
                                checked={!!displayFilterBook}
                                onChange={(value) => setAttributes({displayFilterBook: value})}
                            />

                        }
						
						{displayFilters &&
                            <ToggleControl
                                label={__('Display Filter Service Type')}
                                checked={!!displayFilterServiceType}
                                onChange={(value) => setAttributes({displayFilterServiceType: value})}
                            />

                        }    

						{displayFilters &&
                            <ToggleControl
                                label={__('Display Filter Dates')}
                                checked={!!displayFilterDates}
                                onChange={(value) => setAttributes({displayFilterDates: value})}
                            />

                        }						
                    	
                    </PanelBody>
					

                    <PanelBody title={ __( 'Additional Settings' ) } initialOpen={ false }>
                        
                        <ToggleControl
                            label = { __('Display Featured Image/Video') }
                            checked = { !!displaySermonFeaturedType }
                            onChange = { (value) => setAttributes( { displaySermonFeaturedType: value } ) }
                        />
						
						{displaySermonFeaturedType &&
							<SelectControl
                            label={__('Featured Type')}
                            options={defaultFeaturedTypes}
                            value={featuredType}
                            onChange={(newValue) => {
                                setAttributes({featuredType: newValue })
                            }}
                        />
						}
						
						<ToggleControl
                            label={__('Display Sermon Series')}
                            checked={!!displaySermonSeries}
                            onChange={(value) => setAttributes({displaySermonSeries: value})}
                        />
                    
						<ToggleControl
                            label={__('Display Title')}
                            checked={!!displayTitle}
                            onChange={(value) => setAttributes({displayTitle: value})}
                        />

						{displayTitle &&
                            <RangeControl
                            label = { __('Title Padding' ) }
                            value = { titlePadding }
                            min = { 0 }
                            max = { 30 }
                            onChange = { ( value ) => setAttributes({ titlePadding: value }) }
                        />
                        }
                      
                        <ToggleControl
                            label={__('Display Sermon Date')}
                            checked={!!displaySermonDate}
                            onChange={(value) => setAttributes({displaySermonDate: value})}
                        />

                        <ToggleControl
                            label={__('Display Sermon Description')}
                            checked={!!displaySermonDescription}
                            onChange={(value) => setAttributes({displaySermonDescription: value})}
                        />
                        
                        {displaySermonDescription &&
                            <RangeControl
                            label = { __('Description Padding' ) }
                            value = { descriptionPadding }
                            min = { 0 }
                            max = { 30 }
                            onChange = { ( value ) => setAttributes({ descriptionPadding: value }) }
                        />
                        }
                        
                        <ToggleControl
                            label={__('Display Sermon Read More Button')}
                            checked={!!displaySermonReadMoreButton}
                            onChange={(value) => setAttributes({displaySermonReadMoreButton: value})}
                        />
                                                
                        {displaySermonReadMoreButton &&
                            <TextControl
                                label={__('Read More Button Text')}
                                type="text"
                                value={postReadMoreButtonText}
                                onChange={(value) => setAttributes({postReadMoreButtonText: value})}
                            />
                        }
						
						<ToggleControl
                            label={__('Display Sermon Audio')}
                            checked={!!displaySermonAudio}
                            onChange={(value) => setAttributes({displaySermonAudio: value})}
                        />
                        
                        <ToggleControl
                            label={__('Display Sermon Preacher')}
                            checked={!!displaySermonPreacher}
                            onChange={(value) => setAttributes({displaySermonPreacher: value})}
                        />
                        
                        <ToggleControl
                            label={__('Display Sermon Bible Passage')}
                            checked={!!displaySermonBiblePassage}
                            onChange={(value) => setAttributes({displaySermonBiblePassage: value})}
                        />
                        
                        <ToggleControl
                            label={__('Display Sermon Service Type')}
                            checked={!!displaySermonServiceType}
                            onChange={(value) => setAttributes({displaySermonServiceType: value})}
                        />
                        
                    </PanelBody>
					
					<PanelBody title={ __( 'Pagination' ) } initialOpen={ false }>
                        
                       <RangeControl
							label = { __('Sermons Per Page' ) }
                            value = { postscount }
                            min = { 0 }
                            max = { 24 }
                            onChange = { ( value ) => setAttributes({ postscount: value }) }
						/>
						
						<ToggleControl
                            label={__('Show Pagination')}
                            checked={!!displayPagination}
                            onChange={(value) => setAttributes({displayPagination: value})}
                        />
						
						{displayPagination &&
							<RangeControl
								label = { __('Pagination Total Pages' ) }
								value = { paginationTotalPages }
								min = { 0 }
								max = { 100 }
								onChange = { ( value ) => setAttributes({ paginationTotalPages: value }) }
							/>

                        }
						
						{displayPagination &&
                            <ToggleControl
                                label={__('Show Prev/Next Links')}
                                checked={!!displayPrevNext}
                                onChange={(value) => setAttributes({displayPrevNext: value})}
                            />

                        }
						
						{displayPagination && displayPrevNext &&
                            <TextControl
                                label={__('Previous Label')}
                                type="text"
                                value={previousLabel}
                                onChange={(value) => setAttributes({previousLabel: value})}
                            />
                        }  

						{displayPagination && displayPrevNext &&
                            <TextControl
                                label={__('Next Label')}
                                type="text"
                                value={nextLabel}
                                onChange={(value) => setAttributes({nextLabel: value})}
                            />
                        }    

						{displayPagination &&
						  <SelectControl
                            label={__('Pagination Alignment')}
                            options={[
								{ label: __('Left'), value: 'left'  },
								{ label: __('Center'), value: 'center'  },
								{ label: __('Right'), value: 'right'  },
							]}
                            value={paginationAlignment}
                            onChange={(newValue) => {
                                setAttributes({paginationAlignment: newValue })
                            }}
                        />	
						}	  						
                    	
                    </PanelBody>
									
                </InspectorControls>
                
                   <div className={`${ className } sermons-grid-view gpl-d-flex gpl-flex-wrap ${gridLayoutStyle} ${gridViewWrapper}`} style={itemStyle}>
                       
						<div className={`gutenber-filtering` }>
							{ displayFilters && displayFilterPreacher &&
								displayPosts.slice(0, 1).map( ( post, i ) => { return <div dangerouslySetInnerHTML={ {   __html: post.sermons_blog_filters_preachers   } }/>; } )
							} 
							{ displayFilters && displayFilterSeries &&
								displayPosts.slice(0, 1).map( ( post, i ) => { return <div dangerouslySetInnerHTML={ {   __html: post.sermons_blog_filters_series   } }/>; } )
							} 
							{ displayFilters && displayFilterTopics &&
								displayPosts.slice(0, 1).map( ( post, i ) => { return <div dangerouslySetInnerHTML={ {   __html: post.sermons_blog_filters_topics   } }/>; } )
							} 
							{ displayFilters && displayFilterBook &&
								displayPosts.slice(0, 1).map( ( post, i ) => { return <div dangerouslySetInnerHTML={ {   __html: post.sermons_blog_filters_books   } }/>; } )
							} 
							{ displayFilters && displayFilterServiceType &&
								displayPosts.slice(0, 1).map( ( post, i ) => { return <div dangerouslySetInnerHTML={ {   __html: post.sermons_blog_filters_service_types   } }/>; } )
							} 
							{ displayFilters && displayFilterDates &&
								displayPosts.slice(0, 1).map( ( post, i ) => { return <div dangerouslySetInnerHTML={ {   __html: post.sermons_blog_filters_dates   } }/>; } )
							} 						
						</div>				
					    <div className={`sm-inner-grid gpl-column-12 gpl-d-flex gpl-flex-wrap` } data-masonry={ `{ "gutter": 0 }` } >
					   
							{ displayPosts.map( ( post, i ) => {
								
						        let article = <article
                                    className={`post-item wpfc-sermon gpl-mb-30 ${gridView} ${gridLayoutStyle}`}>
                        										
                                    <div className={`wpfc-sermon-inner ${align} ${gridEqualHeight} image-position-${imagePosition}`}>
                                    	{displaySermonFeaturedType && featuredType == 'image' &&
											<div className="post-image wpfc-sermon-image">
                                                <a href={post.link} target="_blank" rel="bookmark">
                                                    <img src={post.sermons_blog_image_url}/>
													<div className="wpfc-sermon-image-img" style={{backgroundImage: 'url(' + post.sermons_blog_image_url + ')'}}></div>
                                                </a>
                                            </div>
                                        }
										{displaySermonFeaturedType && featuredType == 'video' && post.sermons_blog_video &&
											<div className="wpfc-sermon-video wpfc-sermon-video-link" dangerouslySetInnerHTML={
                                                {
                                                    __html: post.sermons_blog_video
                                                }
											}/>
                                        }
									    <div className={'wpfc-sermon-main '}>
											<div className="wpfc-sermon-header">
												<div className="wpfc-sermon-header-main">
													{post.sermons_blog_series && displaySermonSeries &&
														<div className="wpfc-sermon-meta-item wpfc-sermon-meta-series" dangerouslySetInnerHTML={
															{
																__html: post.sermons_blog_series
															}
														}/>
													}
													{displayTitle &&
													<h3 className="wpfc-sermon-title" style={{ paddingBottom: `${titlePadding}px`}}>
														<a className="wpfc-sermon-title-text" href={post.link}>
															{
																post.type !== 'wp_block' &&
																decodeEntities(post.title.rendered.trim()) || __('Untitled')
															}
														</a>
													</h3>
													}
													{displaySermonDate && post.date_gmt &&
														<div className="wpfc-sermon-meta-item wpfc-sermon-meta-date" datetime={moment(post.date_gmt).utc().format()}>
															  {moment(post.date_gmt).local().format('MMMM DD, Y')}
														</div>
													}
												</div>
										    </div>
											<div className="wpfc-sermon-description"> 
												{displaySermonDescription && post.sermons_blog_meta_sermon_description && post.type !== 'wp_block' &&
												<div className="sermon-description-content" style={{ paddingBottom: `${descriptionPadding}px`}}>
													<div dangerouslySetInnerHTML={
														{
															__html: post.sermons_blog_meta_sermon_description
														}
													}/>
												</div>
												}
												{displaySermonReadMoreButton && post.sermons_blog_show_readmore &&
													<div className="wpfc-sermon-description-read-more">
														<a className="post-read-moore" href={post.link} target="_blank"
															rel="bookmark">{postReadMoreButtonText}</a>
													</div>
												}
											</div>
											{post.sermons_blog_audio && displaySermonAudio &&
												<div className="wpfc-sermon-audio" dangerouslySetInnerHTML={
													{
														__html: post.sermons_blog_audio
													}
												}/>
											}
											{ ( (post.sermons_blog_preacher && displaySermonPreacher) || (post.sermons_blog_bible_passage && displaySermonBiblePassage) || (post.sermons_blog_service_type && displaySermonServiceType) ) &&
												<div className="wpfc-sermon-footer">
													{post.sermons_blog_preacher && displaySermonPreacher &&
													<div className="wpfc-sermon-meta-item wpfc-sermon-meta-preacher">
														<span dangerouslySetInnerHTML={
															{
																__html: post.sermons_blog_preacher_image
															}
														}/>
														<span className="wpfc-sermon-meta-prefix"> Preacher : </span>
														<span className="wpfc-sermon-meta-text" dangerouslySetInnerHTML={
														{
															__html: post.sermons_blog_preacher
														}
														}/>
													</div>
													}
													{post.sermons_blog_bible_passage && displaySermonBiblePassage &&
													<div className="wpfc-sermon-meta-item wpfc-sermon-meta-passage">
														<span className="wpfc-sermon-meta-prefix">Passage : </span>
														<span className="wpfc-sermon-meta-text">{post.sermons_blog_bible_passage}</span>
													</div>
													}
													{post.sermons_blog_service_type && displaySermonServiceType &&
													<div className="wpfc-sermon-meta-item wpfc-sermon-meta-service">
														<span className="wpfc-sermon-meta-prefix">Service Type : </span>
														<span className="wpfc-sermon-meta-text" dangerouslySetInnerHTML={
														{
															__html: post.sermons_blog_service_type
														}
														}/>
													</div>
													}
												</div>
											}
										</div>
                                    </div>
                                </article>
                                
								return article;
                                                                
								}
								)
							}
                        </div>
                    	{displayPagination &&
							<nav className="gutenberg-pagination" style={{textAlign: `${paginationAlignment}`, width: "100%"}}>
								<p>
									<span aria-current="page" className="page-numbers current">1</span>
									{listItems}
									{displayPrevNext && <a className="next page-numbers" href="#">{nextLabel}</a> }
								</p>
							</nav>
						}
					</div>
            </Fragment>
        );

    }
}

export default withSelect( ( select, props ) => {
    const { categories, order, orderBy, postscount, post_type} = props.attributes;
    
    const { getEntityRecords, getPostTypes, getTaxonomies, getMediaItems} = select( 'core' );
    let regCategories = getTaxonomies();
    
    const hasCategories = Array.isArray(regCategories) && regCategories.length;
    
    if(!hasCategories){
        return;
    }
    
    var taxonomy_name = [];
    let restBase = null;
    
    regCategories.map( (item, index ) => {
        if (item.types.includes(post_type)){
            taxonomy_name.push(item.slug);

            if (taxonomy_name.length === 1) {
                restBase = item.rest_base;
            }
        }
    });
    
    const latestPostsQuery = {
        order,
        orderby: orderBy,
        per_page: postscount,
    };
    
    if (categories && restBase) {
        latestPostsQuery[restBase] = categories;
    }

    const query = { per_page: 100 };

    return {
        latestPosts: getEntityRecords( 'postType', post_type, latestPostsQuery ),
        categoriesList: getEntityRecords( 'taxonomy', taxonomy_name[0], query ),
        postTypes: getPostTypes(),
        media: getMediaItems(),
    };
} )( PostGridEdit );