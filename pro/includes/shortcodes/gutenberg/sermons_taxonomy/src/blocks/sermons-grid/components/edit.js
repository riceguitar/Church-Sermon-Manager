const { Component } = wp.element;
const { InspectorControls, BlockControls, AlignmentToolbar, BlockAlignmentToolbar } = wp.editor;
const { Fragment } = wp.element;
const { withSelect } = wp.data;
const { __ } = wp.i18n;
const { QueryControls, PanelBody, Spinner, Placeholder, RangeControl, SelectControl, Toolbar, ToggleControl, TextControl } = wp.components;
const { decodeEntities } = wp.htmlEntities;

class TaxonomyGridEdit extends Component{

    constructor(props) {
        super(...arguments);
    }
    
    render(){

        const { attributes, categoriesList, setAttributes, latestPosts, className, postTypes, media } = this.props;
        const { post_type, categories, order, orderBy, postLayout, align, postscount, columns, gridLayoutStyle, columnGap, showTaxonomy, displayAlphabeticalList, letterTopPadding, letterBottomPadding, displayTermImage, termImagePadding, displayTermTitle, displayTermDescription, termTitlePadding, termDescriptionPadding, displayTermReadMoreButton, termReadMoreButtonText, displayPagination, paginationTotalPages, displayPrevNext, previousLabel, nextLabel, paginationAlignment} = attributes;
        
		const hasPosts = Array.isArray(latestPosts) && latestPosts.length;
        const hasPostTypes = Array.isArray(postTypes) && postTypes.length;
        const mediaItems = Array.isArray(media) && media.length;
        
		if( !hasPosts || !hasPostTypes || !mediaItems){ 
            return(
                <Fragment>
                    <Placeholder
                        icon="admin-post"
                        label={ __( 'No Semon Taxonomies Available' ) }
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
        
        const gridView = (postLayout === 'grid' ) ? `gpl-column-${columns} term-grid` : `gpl-column-${columns} term-list`;
        const gridViewWrapper = (postLayout === 'list' ) ? `wpfc-term-list` : `wpfc-term-grid`;
		        
        const itemStyle = {
            '--item-padding-left-right' : columnGap ? `${columnGap}px` : undefined,
            '--item-margin-bottom' : columnGap ? `${columnGap*2}px` : undefined,
            '--item-height' : columnGap ? `${300-columnGap}px` : undefined,
        };
		
		const defaultTaxonomies =  [
            { label: __('Series'), value: 'wpfc_sermon_series'  },
            { label: __('Preachers'), value: 'wpfc_preacher'  },
			{ label: __('Topics'), value: 'wpfc_sermon_topics'  },
			{ label: __('Books'), value: 'wpfc_bible_book'  },
			{ label: __('Service Types'), value: 'wpfc_service_type'  },
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
                        
                        <SelectControl
                            label={__('Source')}
                            options={defaultTaxonomies}
                            value={showTaxonomy}
                            onChange={(newValue) => {
                                setAttributes({showTaxonomy: newValue })
                            }}
                        />
						
						<RangeControl
                            label={__('Columns')}
                            value={columns}
                            onChange={(value) => setAttributes({columns: value})}
                            min={1}
                            max={6}
                        />
                        
                        <RangeControl
                            label = { __('Column Gap' ) }
                            value = { columnGap }
                            min = { 0 }
                            max = { 20 }
                            onChange = { ( value ) => setAttributes({ columnGap: value }) }
                        />
						
			        </PanelBody>

                    <PanelBody title={ __( 'Additional Settings' ) } initialOpen={ false }>
                        
						{postLayout === 'grid' &&
						<ToggleControl
							label = { __('Display Term Image') }
                            checked = { !!displayTermImage }
                            onChange = { (value) => setAttributes( { displayTermImage: value } ) }
                        />
						}
						
						{displayTermImage && postLayout === 'grid' &&
						<RangeControl
                            label = { __('Term Image Padding' ) }
                            value = { termImagePadding }
                            min = { 0 }
                            max = { 50 }
                            onChange = { ( value ) => setAttributes({ termImagePadding: value }) }
                        />
						}

                        {postLayout === 'grid' &&
                            <ToggleControl
                                label={__('Display Term Title')}
                                checked={!!displayTermTitle}
                                onChange={(value) => setAttributes({displayTermTitle: value})}
                            />
                        }
						
						{displayTermTitle && postLayout === 'grid' &&
						<RangeControl
                            label = { __('Term Title Padding' ) }
                            value = { termTitlePadding }
                            min = { 0 }
                            max = { 50 }
                            onChange = { ( value ) => setAttributes({ termTitlePadding: value }) }
                        />
						}
						
						{postLayout === 'grid' &&
                            <ToggleControl
                                label={__('Display Term Description')}
                                checked={!!displayTermDescription}
                                onChange={(value) => setAttributes({displayTermDescription: value})}
                            />
                        }
						
						{displayTermDescription && postLayout === 'grid' &&
						<RangeControl
                            label = { __('Term Description Padding' ) }
                            value = { termDescriptionPadding }
                            min = { 0 }
                            max = { 50 }
                            onChange = { ( value ) => setAttributes({ termDescriptionPadding: value }) }
                        />
						}
                        
                        {displayTermDescription && postLayout === 'grid' &&
						<ToggleControl
                            label={__('Display Term Description Read More Button')}
                            checked={!!displayTermReadMoreButton}
                            onChange={(value) => setAttributes({displayTermReadMoreButton: value})}
                        />
						}
                        
                        {displayTermDescription && postLayout === 'grid' && displayTermReadMoreButton &&
                            <TextControl
                                label={__('Read More Button Text')}
                                type="text"
                                value={termReadMoreButtonText}
                                onChange={(value) => setAttributes({termReadMoreButtonText: value})}
                            />
                        }
						
						{postLayout === 'list' &&
						<ToggleControl
                            label={__('Display Alphabetical List')}
                            checked={!!displayAlphabeticalList}
                            onChange={(value) => setAttributes({displayAlphabeticalList: value})}
                        />
						}
						
						{displayAlphabeticalList && postLayout === 'list' &&
						<RangeControl
                            label = { __('Letter Top Padding' ) }
                            value = { letterTopPadding }
                            min = { 0 }
                            max = { 30 }
                            onChange = { ( value ) => setAttributes({ letterTopPadding: value }) }
                        />
						}
						
						{displayAlphabeticalList && postLayout === 'list' &&
						<RangeControl
                            label = { __('Letter Bottom Padding' ) }
                            value = { letterBottomPadding }
                            min = { 0 }
                            max = { 30 }
                            onChange = { ( value ) => setAttributes({ letterBottomPadding: value }) }
                        />
						}
						
                    </PanelBody>
					
					<PanelBody title={ __( 'Pagination' ) } initialOpen={ false }>
                        
                       <RangeControl
							label = { __('Terms Per Page' ) }
                            value = { postscount }
                            min = { 0 }
                            max = { 100 }
                            onChange = { ( value ) => setAttributes({ postscount: value }) }
						/>
						
						{
							<ToggleControl
                                label={__('Show Pagination')}
                                checked={!!displayPagination}
                                onChange={(value) => setAttributes({displayPagination: value})}
                            />

                        }
						
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
                       
						<div className={`sm-inner-grid gpl-column-12 gpl-d-flex gpl-flex-wrap` }>
					   
					    {
                            displayPosts.map( ( post, i ) => {
								
								const first_letter = `${post.name[0]}`;
								  
								let article = <article

                                    className={`wpfc-term post-item wpfc-sermon gpl-mb-30 ${gridView} ${gridLayoutStyle}`}>
									
									{ displayAlphabeticalList && postLayout == 'list' && 
										<div className={`wpfc-term-first-letter ${align}`} style={{paddingBottom: `${letterBottomPadding}px`, paddingTop: `${letterTopPadding}px`}}>{first_letter}</div>
									}
															        
									{ postLayout == 'grid' && displayTermImage && post.sermons_taxonomy_image &&
										<a href={post.link} className={`wpfc-term-grid-image`} style={{backgroundImage: 'url(' + post.sermons_taxonomy_image + ')', marginBottom: `${termImagePadding}px` }}></a>
									}

									{ postLayout == 'grid' && displayTermImage && !post.sermons_taxonomy_image &&
										<a href={post.link} className={`wpfc-term-grid-image`} style={{backgroundColor: "#cecece",  marginBottom: `${termImagePadding}px`}}></a>
									}		

									<div className={`wpfc-term-inner ${align}`}   style={ (postLayout == 'grid' && displayTermDescription) ? { padding: `${termDescriptionPadding}px`} : null } >
										
										{ 	( ( postLayout == 'grid' && displayTermTitle ) || postLayout == 'list' ) &&

											<a href={post.link} className={`wpfc-term-title`} style={ postLayout == 'grid' ? { paddingBottom: `${termTitlePadding}px`} : null }>{post.name}</a>
										}
										
										
										{ postLayout == 'grid' && displayTermDescription &&

											<div className={`wpfc-term-description`}>{post.sermons_taxonomy_description}</div>
										}
											
										{postLayout == 'grid' && displayTermDescription && displayTermReadMoreButton && post.description &&
												<div className="wwpfc-term-description-read-more">
													<a href={post.link}>{termReadMoreButtonText}</a>
												</div>
										}

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
    const { postscount, post_type, showTaxonomy} = props.attributes;
    
    const { getEntityRecords, getPostTypes, getTaxonomies, getMediaItems} = select( 'core' );
       
    const latestPostsQuery = {
        per_page: postscount,
    };
    
    return {
        latestPosts: getEntityRecords( 'taxonomy', showTaxonomy, latestPostsQuery ),
        postTypes: getPostTypes(),
        media: getMediaItems(),
    };
} )( TaxonomyGridEdit );