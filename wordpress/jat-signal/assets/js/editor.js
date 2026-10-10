/**
 * JAT Signal: editor side of every theme block.
 *
 * All 36 blocks are server-rendered (render.php), so the editor shows a live ServerSideRender
 * preview and builds the settings sidebar from each block's attributes (block.json). Field types are
 * inferred from the attribute and its default; FIELDS below refines labels, choices, repeaters,
 * post pickers and image pickers where inference alone would be vague.
 *
 * No build step: plain ES2017 against the wp.* globals.
 */
( function ( wp ) {
	'use strict';
	if ( ! wp || ! wp.blocks ) {
		return;
	}
	const { createElement: el, Fragment, useState } = wp.element;
	const { registerBlockType, getBlockType } = wp.blocks;
	const { InspectorControls, useBlockProps, MediaUpload, MediaUploadCheck } = wp.blockEditor;
	const C = wp.components;
	const SSR = wp.serverSideRender;
	const { useSelect } = wp.data;
	const { useEntityProp } = wp.coreData;
	const { __, sprintf } = wp.i18n;

	const CH = ( window.JAT_EDITOR && window.JAT_EDITOR.channels ) || {};
	const CH_OPTS = Object.keys( CH ).map( ( k ) => ( { value: k, label: CH[ k ].id + ' · ' + CH[ k ].name } ) );
	const NONE = { value: '', label: __( 'None', 'jat-signal' ) };
	const AUTO = { value: '', label: __( 'Automatic', 'jat-signal' ) };

	/* Attributes the editor never shows as fields (handled by core supports). */
	const SKIP = [ 'align', 'className', 'anchor', 'style', 'lock', 'metadata' ];

	/* Kept for saved content from Developer Portfolio; this design doesn't display them. */
	const LEGACY = /^(sectionLabel|eduEmoji|achieveEmoji|stat\dEmoji|faviconId)$/;

	/* Per-block refinements. Keys are attribute names; values describe the control. */
	const FIELDS = {
		'developer-portfolio/hero-section': {
			name: { label: __( 'Headline', 'jat-signal' ) },
			role: { label: __( 'Role line', 'jat-signal' ), help: __( 'Wrap it in braces, like { Software Architect }, to keep the braces while it cycles.', 'jat-signal' ) },
			roles: { type: 'textarea', label: __( 'Roles to cycle through', 'jat-signal' ), help: __( 'Comma-separated. One role keeps the line still.', 'jat-signal' ) },
			badgeText: { label: __( 'Status readout', 'jat-signal' ), help: __( 'Top-right status text. Its dot turns green when the trace locks.', 'jat-signal' ) },
			primaryButtonUrl: { type: 'url', help: __( 'Leave empty to link to the contact page.', 'jat-signal' ) },
			note: { label: __( 'Small print under the buttons', 'jat-signal' ) },
			showChannels: { label: __( 'Show the four channels under the hero', 'jat-signal' ) },
		},
		'developer-portfolio/featured-projects': {
			layout: { type: 'select', options: [ { value: 'grid', label: __( 'Grid of cards', 'jat-signal' ) }, { value: 'feature', label: __( 'Feature the first project', 'jat-signal' ) } ] },
			postIds: { type: 'posts', postType: 'project', label: __( 'Pick projects', 'jat-signal' ), help: __( 'Leave empty to show the newest projects.', 'jat-signal' ) },
			count: { type: 'number', min: 1, max: 12 },
		},
		'developer-portfolio/latest-posts': {
			postIds: { type: 'posts', postType: 'post', label: __( 'Pick posts', 'jat-signal' ), help: __( 'Leave empty to show the newest posts.', 'jat-signal' ) },
			count: { type: 'number', min: 1, max: 12 },
			related: { label: __( 'Related to the current post', 'jat-signal' ), help: __( 'On a single post, prefer posts that share its categories.', 'jat-signal' ) },
		},
		'developer-portfolio/services-grid': {
			postIds: { type: 'posts', postType: 'service', label: __( 'Pick services', 'jat-signal' ), help: __( 'Leave empty to show popular services first.', 'jat-signal' ) },
			count: { type: 'number', min: 1, max: 12 },
			layout: { type: 'hidden' },
		},
		'jat/price-list': {
			postIds: { type: 'posts', postType: 'service', label: __( 'Pick services', 'jat-signal' ), help: __( 'Leave empty to list services from the category below.', 'jat-signal' ) },
			category: { type: 'term', taxonomy: 'service_category', label: __( 'Service category', 'jat-signal' ) },
			count: { type: 'number', min: 1, max: 20 },
			showFeatures: { label: __( 'Show what’s included under each price', 'jat-signal' ) },
		},
		'developer-portfolio/services-packages': {
			category: { type: 'term', taxonomy: 'service_category' },
			sectionTagType: { type: 'select', label: __( 'Channel', 'jat-signal' ), options: [ { value: 'wp', label: 'CH1 · Platforms' }, { value: 'ai', label: 'CH2 · Intelligence' }, { value: 'sec', label: 'CH3 · Security' }, { value: 'maint', label: 'CH4 · Operations' } ] },
		},
		'developer-portfolio/services-addons': { category: { type: 'term', taxonomy: 'service_category' } },
		'developer-portfolio/services-pricing-table': {
			category: { type: 'term', taxonomy: 'service_category' },
			volumeDiscountText: { type: 'textarea', help: __( 'Basic HTML like <strong> is allowed.', 'jat-signal' ) },
		},
		'developer-portfolio/services-retainers': {
			category: { type: 'term', taxonomy: 'service_category' },
			retainerTermsText: { type: 'textarea', help: __( 'Basic HTML like <strong> is allowed.', 'jat-signal' ) },
		},
		'developer-portfolio/services-hero': { chips: { type: 'list', label: __( 'Chips', 'jat-signal' ) } },
		'developer-portfolio/services-comparison': {
			columns: { type: 'list', label: __( 'Column headings', 'jat-signal' ), help: __( 'One per line. The first is the row-label column and is usually empty.', 'jat-signal' ) },
			rows: { type: 'repeater', title: 'label', fields: { label: { label: __( 'Row', 'jat-signal' ) }, cells: { type: 'list', label: __( 'Cells, one per line', 'jat-signal' ) }, types: { type: 'list', label: __( 'Cell types, one per line', 'jat-signal' ), help: 'gold · check · cross · partial · muted' } } },
		},
		'developer-portfolio/services-faq': { items: { type: 'repeater', title: 'q', fields: { q: { label: __( 'Question', 'jat-signal' ) }, a: { type: 'textarea', label: __( 'Answer', 'jat-signal' ) } } } },
		'developer-portfolio/services-market-rates': { cards: { type: 'repeater', title: 'label' } },
		'developer-portfolio/about-intro': {
			imageUrl: { type: 'media', altAttr: 'imageAlt', label: __( 'Portrait', 'jat-signal' ) },
			imageAlt: { type: 'hidden' },
		},
		'developer-portfolio/about-experience': {
			jobs: { type: 'repeater', title: 'role', fields: { role: {}, company: {}, period: {}, description: { type: 'textarea' } } },
			eduItems: { type: 'repeater', title: 'title' },
			achieveItems: { type: 'repeater', title: 'title' },
		},
		'developer-portfolio/projects-listing': {
			perPage: { type: 'number', min: 1, max: 48, label: __( 'Projects per page', 'jat-signal' ) },
			columns: { type: 'number', min: 1, max: 4 },
			splitProprietary: { label: __( 'List proprietary projects separately', 'jat-signal' ), help: __( 'Projects without a public repository or live link get their own section.', 'jat-signal' ) },
			proprietaryDescription: { type: 'textarea' },
		},
		'developer-portfolio/services-listing': { perPage: { type: 'number', min: 1, max: 48 }, columns: { type: 'number', min: 1, max: 4 } },
		'developer-portfolio/blog-listing': { perPage: { type: 'number', min: 1, max: 48, label: __( 'Posts per page', 'jat-signal' ) } },
		'developer-portfolio/contact-cta': {
			buttonUrl: { type: 'url', help: __( 'Leave empty to link to the contact page.', 'jat-signal' ) },
			showEmail: { label: __( 'Show the email address with a copy button', 'jat-signal' ) },
		},
		'developer-portfolio/site-header': {
			navMode: { type: 'select', label: __( 'Navigation', 'jat-signal' ), options: [ { value: 'auto', label: __( 'Automatic: Services, Projects, Approach, About, Blog', 'jat-signal' ) }, { value: 'menu', label: __( 'Appearance › Menus › Primary', 'jat-signal' ) }, { value: 'custom', label: __( 'Custom links (below)', 'jat-signal' ) } ] },
			navItems: { type: 'repeater', title: 'label', showIf: ( a ) => ( a.navMode || 'custom' ) === 'custom', fields: { label: {}, url: { type: 'url' } } },
			showLockup: { label: __( 'Show the JAT lockup', 'jat-signal' ), help: __( 'Off: show the logo below, or the site title.', 'jat-signal' ) },
			logoId: { type: 'mediaId', label: __( 'Logo', 'jat-signal' ) },
			ctaUrl: { type: 'url', help: __( 'Leave empty to link to the contact page.', 'jat-signal' ) },
			siteTitle: { help: __( 'Leave empty to use the site title.', 'jat-signal' ) },
		},
		'jat/site-footer': { blurb: { type: 'textarea' }, note: { help: __( 'Use {year} for the current year.', 'jat-signal' ) } },
		'jat/page-head': {
			title: { help: __( 'Leave empty to use the current page, post or archive title.', 'jat-signal' ) },
			lede: { type: 'textarea', help: __( 'Leave empty to use the subtitle or excerpt.', 'jat-signal' ) },
			channel: { type: 'select', options: [ AUTO ].concat( CH_OPTS ) },
			wave: { type: 'select', options: [ AUTO, { value: 'step', label: __( 'Step response', 'jat-signal' ) }, { value: 'burst', label: __( 'Burst', 'jat-signal' ) }, { value: 'pulse', label: __( 'Pulse train', 'jat-signal' ) }, { value: 'sine', label: __( 'Sine', 'jat-signal' ) } ] },
			showCrumbs: { label: __( 'Show breadcrumbs', 'jat-signal' ) },
			showMeta: { label: __( 'Show date, reading time and categories', 'jat-signal' ) },
		},
		'jat/channels': {
			layout: { type: 'select', options: [ { value: 'rows', label: __( 'Rows with promises and prices', 'jat-signal' ) }, { value: 'strip', label: __( 'Compact strip', 'jat-signal' ) } ] },
			items: { type: 'repeater', title: 'key', fields: { key: { type: 'select', options: CH_OPTS, label: __( 'Channel', 'jat-signal' ) }, promise: { type: 'textarea' }, points: { type: 'list' } } },
		},
		'jat/channel-intro': {
			channel: { type: 'select', options: CH_OPTS },
			items: { type: 'repeater', title: 'title', fields: { title: {}, outcome: { type: 'textarea' }, points: { type: 'list' } } },
			ctaUrl: { type: 'url', help: __( 'Leave empty to link to the contact page.', 'jat-signal' ) },
		},
		'jat/check-grid': {
			layout: { type: 'select', options: [ { value: 'grid', label: __( 'Grid', 'jat-signal' ) }, { value: 'list', label: __( 'List', 'jat-signal' ) } ] },
			channel: { type: 'select', options: [ NONE ].concat( CH_OPTS ) },
			items: { type: 'repeater', title: 'title', fields: { title: {}, outcome: {}, text: { type: 'textarea' }, points: { type: 'list' } } },
		},
		'jat/steps': { items: { type: 'repeater', title: 'title', fields: { title: {}, meta: {}, text: { type: 'textarea' }, note: {} } } },
		'jat/founder': {
			imageId: { type: 'media', urlAttr: 'imageUrl', altAttr: 'imageAlt', label: __( 'Portrait', 'jat-signal' ) },
			imageUrl: { type: 'hidden' },
			imageAlt: { type: 'hidden' },
			items: { type: 'repeater', title: 'y', fields: { y: { label: __( 'Year or period', 'jat-signal' ) }, text: { type: 'textarea' } } },
		},
		'jat/contact-form': {
			cf7: { label: __( 'Contact Form 7 form (optional)', 'jat-signal' ), help: __( 'A form ID or the full shortcode. Leave empty to use the built-in form, which emails you and keeps a copy under Inquiries.', 'jat-signal' ) },
			steps: { type: 'repeater', title: 'title', fields: { meta: {}, title: {}, text: { type: 'textarea' } } },
			showChannels: { label: __( 'Ask which channels they need', 'jat-signal' ) },
		},
		'jat/archive-list': {
			layout: { type: 'select', options: [ { value: 'auto', label: __( 'Match the content type', 'jat-signal' ) }, { value: 'posts', label: __( 'Post rows', 'jat-signal' ) }, { value: 'projects', label: __( 'Project cards', 'jat-signal' ) }, { value: 'services', label: __( 'Service rows', 'jat-signal' ) } ] },
		},
	};

	/* Shared presentation notes shown above the preview's controls. */
	const NOTES = {
		'jat/post-nav': __( 'Previous and next links for the current post, project or service.', 'jat-signal' ),
		'jat/project-facts': __( 'Reads the project’s links and terms from the Project details panel.', 'jat-signal' ),
		'jat/service-summary': __( 'Reads price, timeline and features from the Service details panel.', 'jat-signal' ),
		'developer-portfolio/tech-stack': __( 'The technologies are stored on this page. Edit them below.', 'jat-signal' ),
	};

	/* ---------- helpers ---------- */
	const human = ( k ) => {
		const s = k.replace( /([a-z0-9])([A-Z])/g, '$1 $2' ).replace( /_/g, ' ' ).toLowerCase();
		return ( s.charAt( 0 ).toUpperCase() + s.slice( 1 ) ).replace( /\burl\b/i, 'URL' ).replace( /\bcf7\b/i, 'CF7' ).replace( /\bids\b/i, 'IDs' );
	};
	const parse = ( v, fallback ) => {
		try {
			const j = JSON.parse( v );
			return j == null ? fallback : j;
		} catch ( e ) {
			return fallback;
		}
	};
	/* Short labels ("buttonText") stay one line; prose-like fields and long defaults get a textarea. */
	const isLong = ( k, d ) => ( /^(description|text|lede|intro|subtitle|blurb)$|^bio\d|Description$|^(left|right)Text$|TermsText$/.test( k ) ) || ( typeof d === 'string' && d.length > 80 );

	/* Infer a field spec from the attribute definition, then merge the refinement. */
	function specFor( name, key, def ) {
		const over = ( FIELDS[ name ] && FIELDS[ name ][ key ] ) || {};
		let type = over.type;
		const d = def.default;
		if ( ! type ) {
			if ( def.type === 'boolean' ) {
				type = 'toggle';
			} else if ( def.type === 'number' || def.type === 'integer' ) {
				type = 'number';
			} else if ( def.enum ) {
				type = 'select';
			} else if ( typeof d === 'string' && d.charAt( 0 ) === '[' ) {
				const j = parse( d, [] );
				type = j.length && typeof j[ 0 ] === 'object' ? 'repeater' : 'list';
			} else if ( /Url$/.test( key ) ) {
				type = 'url';
			} else {
				type = isLong( key, d ) ? 'textarea' : 'text';
			}
		}
		const spec = Object.assign( { key, type, label: human( key ) }, over );
		if ( type === 'select' && ! spec.options && def.enum ) {
			spec.options = def.enum.map( ( v ) => ( { value: v, label: human( String( v ) ) } ) );
		}
		if ( type === 'repeater' && ! spec.fields ) {
			const first = parse( d, [] )[ 0 ] || {};
			spec.fields = {};
			Object.keys( first ).forEach( ( k ) => {
				spec.fields[ k ] = Array.isArray( first[ k ] ) ? { type: 'list' } : { type: String( first[ k ] ).length > 60 ? 'textarea' : 'text' };
			} );
		}
		spec.json = def.type === 'string' && ( type === 'repeater' || type === 'list' ) && ! ( key === 'roles' );
		return spec;
	}

	/* ---------- controls ---------- */
	function ListControl( { label, help, value, onChange } ) {
		const text = ( Array.isArray( value ) ? value : [] ).join( '\n' );
		return el( C.TextareaControl, {
			label, help: help || __( 'One per line.', 'jat-signal' ), value: text, rows: Math.min( 8, Math.max( 3, text.split( '\n' ).length + 1 ) ),
			onChange: ( v ) => onChange( v.split( '\n' ).map( ( s ) => s.replace( /\s+$/, '' ) ).filter( ( s, i, a ) => s !== '' || i < a.length - 1 ) ),
			__nextHasNoMarginBottom: true,
		} );
	}

	function PostsControl( { label, help, postType, value, onChange } ) {
		const ids = ( Array.isArray( value ) ? value : [] ).map( Number ).filter( Boolean );
		const posts = useSelect( ( s ) => s( 'core' ).getEntityRecords( 'postType', postType, { per_page: 100, status: 'publish', orderby: 'title', order: 'asc', _fields: 'id,title' } ), [ postType ] );
		const title = ( p ) => ( p.title && ( p.title.raw || p.title.rendered ) ) || '#' + p.id;
		const byId = {};
		const byTitle = {};
		( posts || [] ).forEach( ( p ) => {
			byId[ p.id ] = title( p );
			byTitle[ title( p ) ] = p.id;
		} );
		return el( Fragment, null,
			el( C.FormTokenField, {
				label, value: ids.map( ( id ) => byId[ id ] || '#' + id ),
				suggestions: Object.keys( byTitle ), __experimentalExpandOnFocus: true,
				onChange: ( tokens ) => onChange( tokens.map( ( t ) => byTitle[ t ] || parseInt( String( t ).replace( '#', '' ), 10 ) ).filter( Boolean ) ),
				__next40pxDefaultSize: true, __nextHasNoMarginBottom: true,
			} ),
			help ? el( 'p', { className: 'components-base-control__help jat-help' }, help ) : null
		);
	}

	function TermControl( { label, help, taxonomy, value, onChange } ) {
		const terms = useSelect( ( s ) => s( 'core' ).getEntityRecords( 'taxonomy', taxonomy, { per_page: 100, _fields: 'id,name,slug' } ), [ taxonomy ] );
		if ( ! terms || ! terms.length ) {
			return el( C.TextControl, { label, help: help || __( 'Category slug.', 'jat-signal' ), value: value || '', onChange, __next40pxDefaultSize: true, __nextHasNoMarginBottom: true } );
		}
		const opts = [ { value: '', label: __( 'Any', 'jat-signal' ) } ].concat( terms.map( ( t ) => ( { value: t.slug, label: t.name } ) ) );
		if ( value && ! terms.some( ( t ) => t.slug === value ) ) {
			opts.push( { value, label: value + ' ' + __( '(not found)', 'jat-signal' ) } );
		}
		return el( C.SelectControl, { label, help, value: value || '', options: opts, onChange, __next40pxDefaultSize: true, __nextHasNoMarginBottom: true } );
	}

	function MediaControl( { label, attributes, setAttributes, spec } ) {
		const isId = spec.type === 'mediaId' || spec.key === 'imageId';
		const id = isId ? Number( attributes[ spec.key ] ) || 0 : 0;
		const url = spec.urlAttr ? attributes[ spec.urlAttr ] : ( isId ? '' : attributes[ spec.key ] );
		const media = useSelect( ( s ) => ( id ? s( 'core' ).getMedia( id ) : null ), [ id ] );
		const preview = ( media && ( ( media.media_details && media.media_details.sizes && media.media_details.sizes.medium && media.media_details.sizes.medium.source_url ) || media.source_url ) ) || url;
		const pick = ( m ) => {
			const next = {};
			if ( isId ) {
				next[ spec.key ] = m.id;
			} else {
				next[ spec.key ] = m.url;
			}
			if ( spec.urlAttr ) {
				next[ spec.urlAttr ] = m.url;
			}
			if ( spec.altAttr && ! attributes[ spec.altAttr ] ) {
				next[ spec.altAttr ] = m.alt || '';
			}
			setAttributes( next );
		};
		const clear = () => {
			const next = {};
			next[ spec.key ] = isId ? 0 : '';
			if ( spec.urlAttr ) {
				next[ spec.urlAttr ] = '';
			}
			setAttributes( next );
		};
		return el( C.BaseControl, { label, id: 'jat-media-' + spec.key, __nextHasNoMarginBottom: true },
			preview ? el( 'img', { src: preview, alt: '', className: 'jat-media-preview' } ) : null,
			el( MediaUploadCheck, null,
				el( MediaUpload, {
					onSelect: pick, allowedTypes: [ 'image' ], value: id || undefined,
					render: ( { open } ) => el( 'div', { className: 'jat-media-actions' },
						el( C.Button, { variant: 'secondary', onClick: open }, preview ? __( 'Replace', 'jat-signal' ) : __( 'Choose image', 'jat-signal' ) ),
						preview ? el( C.Button, { variant: 'tertiary', isDestructive: true, onClick: clear }, __( 'Remove', 'jat-signal' ) ) : null
					),
				} )
			),
			spec.altAttr ? el( C.TextareaControl, {
				label: __( 'Alternative text', 'jat-signal' ), help: __( 'Describe the image for people who can’t see it.', 'jat-signal' ), rows: 2,
				value: attributes[ spec.altAttr ] || '', onChange: ( v ) => setAttributes( { [ spec.altAttr ]: v } ), __nextHasNoMarginBottom: true,
			} ) : null
		);
	}

	function Field( { spec, value, onChange } ) {
		const common = { label: spec.label, help: spec.help, __nextHasNoMarginBottom: true };
		switch ( spec.type ) {
			case 'toggle':
				return el( C.ToggleControl, Object.assign( {}, common, { checked: !! value, onChange } ) );
			case 'number':
				return el( C.__experimentalNumberControl || C.TextControl, Object.assign( {}, common, { type: 'number', min: spec.min, max: spec.max, value: value == null ? '' : value, onChange: ( v ) => onChange( v === '' ? undefined : Number( v ) ), __next40pxDefaultSize: true } ) );
			case 'select':
				return el( C.SelectControl, Object.assign( {}, common, { value: value == null ? '' : value, options: spec.options || [], onChange, __next40pxDefaultSize: true } ) );
			case 'textarea':
				return el( C.TextareaControl, Object.assign( {}, common, { value: value || '', onChange, rows: 3 } ) );
			case 'url':
				return el( C.TextControl, Object.assign( {}, common, { type: 'text', inputMode: 'url', value: value || '', onChange, __next40pxDefaultSize: true } ) );
			case 'list':
				return el( ListControl, Object.assign( {}, common, { value, onChange } ) );
			case 'posts':
				return el( PostsControl, Object.assign( {}, common, { postType: spec.postType, value, onChange } ) );
			case 'term':
				return el( TermControl, Object.assign( {}, common, { taxonomy: spec.taxonomy, value, onChange } ) );
			case 'repeater':
				return el( Repeater, { spec, value, onChange } );
			default:
				return el( C.TextControl, Object.assign( {}, common, { value: value == null ? '' : String( value ), onChange, __next40pxDefaultSize: true } ) );
		}
	}

	function Repeater( { spec, value, onChange } ) {
		const items = Array.isArray( value ) ? value : [];
		const [ open, setOpen ] = useState( -1 );
		const keys = Object.keys( spec.fields );
		const blank = () => {
			const o = {};
			keys.forEach( ( k ) => {
				o[ k ] = ( spec.fields[ k ].type === 'list' ) ? [] : ( spec.fields[ k ].type === 'select' && spec.fields[ k ].options && spec.fields[ k ].options[ 0 ] ? spec.fields[ k ].options[ 0 ].value : '' );
			} );
			return o;
		};
		const set = ( i, k, v ) => onChange( items.map( ( it, j ) => ( j === i ? Object.assign( {}, it, { [ k ]: v } ) : it ) ) );
		const move = ( i, d ) => {
			const n = items.slice();
			const t = n[ i ];
			n[ i ] = n[ i + d ];
			n[ i + d ] = t;
			onChange( n );
			setOpen( i + d );
		};
		const titleOf = ( it, i ) => {
			let t = it && it[ spec.title || keys[ 0 ] ];
			if ( spec.fields[ spec.title ] && spec.fields[ spec.title ].options ) {
				const o = spec.fields[ spec.title ].options.find( ( x ) => x.value === t );
				t = o ? o.label : t;
			}
			return ( t && String( t ).trim() ) || sprintf( /* translators: %d item number */ __( 'Item %d', 'jat-signal' ), i + 1 );
		};
		return el( 'div', { className: 'jat-repeater' },
			el( 'p', { className: 'jat-repeater__label' }, spec.label, el( 'span', null, ' · ' + items.length ) ),
			spec.help ? el( 'p', { className: 'components-base-control__help jat-help' }, spec.help ) : null,
			items.map( ( it, i ) => el( 'div', { key: i, className: 'jat-repeater__item' + ( open === i ? ' is-open' : '' ) },
				el( 'button', { type: 'button', className: 'jat-repeater__head', 'aria-expanded': open === i, onClick: () => setOpen( open === i ? -1 : i ) },
					el( 'span', { className: 'jat-repeater__n' }, String( i + 1 ).padStart( 2, '0' ) ), el( 'span', { className: 'jat-repeater__t' }, titleOf( it, i ) )
				),
				open === i ? el( 'div', { className: 'jat-repeater__body' },
					keys.map( ( k ) => {
						const fs = Object.assign( { key: k, type: 'text', label: human( k ) }, spec.fields[ k ] );
						return el( 'div', { key: k, className: 'jat-field' }, el( Field, { spec: fs, value: it ? it[ k ] : '', onChange: ( v ) => set( i, k, v ) } ) );
					} ),
					el( 'div', { className: 'jat-repeater__tools' },
						el( C.Button, { size: 'small', variant: 'tertiary', disabled: i === 0, onClick: () => move( i, -1 ), label: __( 'Move up', 'jat-signal' ), icon: 'arrow-up-alt2' } ),
						el( C.Button, { size: 'small', variant: 'tertiary', disabled: i === items.length - 1, onClick: () => move( i, 1 ), label: __( 'Move down', 'jat-signal' ), icon: 'arrow-down-alt2' } ),
						el( C.Button, { size: 'small', variant: 'tertiary', isDestructive: true, onClick: () => {
							onChange( items.filter( ( _, j ) => j !== i ) );
							setOpen( -1 );
						} }, __( 'Remove', 'jat-signal' ) )
					)
				) : null
			) ),
			el( C.Button, { variant: 'secondary', size: 'compact', className: 'jat-repeater__add', onClick: () => {
				onChange( items.concat( [ blank() ] ) );
				setOpen( items.length );
			} }, __( 'Add item', 'jat-signal' ) )
		);
	}

	/* ---------- Tech stack: page meta, edited in place ---------- */
	const STACK_COLORS = [
		{ value: 'cyan', label: __( 'Cyan · CH1', 'jat-signal' ) },
		{ value: 'violet', label: __( 'Violet · CH2', 'jat-signal' ) },
		{ value: 'amber', label: __( 'Amber · CH3', 'jat-signal' ) },
		{ value: 'emerald', label: __( 'Green · CH4', 'jat-signal' ) },
		{ value: 'rose', label: __( 'Red', 'jat-signal' ) },
	];
	function TechStackPanel( { context } ) {
		const current = useSelect( ( s ) => {
			const ed = s( 'core/editor' );
			return ed ? { type: ed.getCurrentPostType(), id: ed.getCurrentPostId() } : {};
		}, [] );
		const postType = context.postType || current.type;
		const postId = context.postId || current.id;
		const [ meta, setMeta ] = useEntityProp( 'postType', postType || 'page', 'meta', postId );
		const [ open, setOpen ] = useState( -1 );
		if ( postType !== 'page' || ! meta ) {
			return el( C.Notice, { status: 'info', isDismissible: false }, __( 'The technologies are stored on each page. Open the page in the editor to change them.', 'jat-signal' ) );
		}
		let cats = parse( meta.tech_stack_data || '[]', [] );
		if ( ! Array.isArray( cats ) ) {
			cats = [];
		}
		const save = ( next ) => setMeta( Object.assign( {}, meta, { tech_stack_data: JSON.stringify( next ) } ) );
		const set = ( i, k, v ) => save( cats.map( ( c, j ) => ( j === i ? Object.assign( {}, c, { [ k ]: v } ) : c ) ) );
		return el( 'div', { className: 'jat-repeater' },
			el( 'p', { className: 'jat-repeater__label' }, __( 'Categories', 'jat-signal' ), el( 'span', null, ' · ' + cats.length ) ),
			cats.map( ( c, i ) => el( 'div', { key: i, className: 'jat-repeater__item' + ( open === i ? ' is-open' : '' ) },
				el( 'button', { type: 'button', className: 'jat-repeater__head', 'aria-expanded': open === i, onClick: () => setOpen( open === i ? -1 : i ) },
					el( 'span', { className: 'jat-repeater__n jat-dot jat-dot--' + ( c.color || 'violet' ) } ), el( 'span', { className: 'jat-repeater__t' }, c.name || __( 'Untitled', 'jat-signal' ) )
				),
				open === i ? el( 'div', { className: 'jat-repeater__body' },
					el( 'div', { className: 'jat-field' }, el( C.TextControl, { label: __( 'Name', 'jat-signal' ), value: c.name || '', onChange: ( v ) => set( i, 'name', v ), __next40pxDefaultSize: true, __nextHasNoMarginBottom: true } ) ),
					el( 'div', { className: 'jat-field' }, el( C.SelectControl, { label: __( 'Accent', 'jat-signal' ), value: c.color || 'violet', options: STACK_COLORS, onChange: ( v ) => set( i, 'color', v ), __next40pxDefaultSize: true, __nextHasNoMarginBottom: true } ) ),
					el( 'div', { className: 'jat-field' }, el( C.FormTokenField, { label: __( 'Technologies', 'jat-signal' ), value: Array.isArray( c.badges ) ? c.badges : [], onChange: ( v ) => set( i, 'badges', v ), __next40pxDefaultSize: true, __nextHasNoMarginBottom: true } ) ),
					el( 'div', { className: 'jat-repeater__tools' },
						el( C.Button, { size: 'small', variant: 'tertiary', disabled: i === 0, label: __( 'Move up', 'jat-signal' ), icon: 'arrow-up-alt2', onClick: () => {
							const n = cats.slice();
							[ n[ i - 1 ], n[ i ] ] = [ n[ i ], n[ i - 1 ] ];
							save( n );
							setOpen( i - 1 );
						} } ),
						el( C.Button, { size: 'small', variant: 'tertiary', disabled: i === cats.length - 1, label: __( 'Move down', 'jat-signal' ), icon: 'arrow-down-alt2', onClick: () => {
							const n = cats.slice();
							[ n[ i + 1 ], n[ i ] ] = [ n[ i ], n[ i + 1 ] ];
							save( n );
							setOpen( i + 1 );
						} } ),
						el( C.Button, { size: 'small', variant: 'tertiary', isDestructive: true, onClick: () => {
							save( cats.filter( ( _, j ) => j !== i ) );
							setOpen( -1 );
						} }, __( 'Remove', 'jat-signal' ) )
					)
				) : null
			) ),
			el( C.Button, { variant: 'secondary', size: 'compact', className: 'jat-repeater__add', onClick: () => {
				save( cats.concat( [ { name: __( 'New category', 'jat-signal' ), color: 'cyan', badges: [] } ] ) );
				setOpen( cats.length );
			} }, __( 'Add category', 'jat-signal' ) )
		);
	}

	/* ---------- edit component ---------- */
	function group( spec ) {
		if ( LEGACY.test( spec.key ) ) {
			return 'legacy';
		}
		if ( spec.type === 'repeater' || spec.type === 'posts' ) {
			return 'items';
		}
		if ( /Url$/.test( spec.key ) || /(button|link|cta|submit)Text$/i.test( spec.key ) ) {
			return 'links';
		}
		if ( [ 'toggle', 'number', 'select', 'term' ].includes( spec.type ) ) {
			return 'display';
		}
		return 'content';
	}
	const GROUPS = [
		[ 'content', __( 'Content', 'jat-signal' ), true ],
		[ 'items', __( 'Items', 'jat-signal' ), true ],
		[ 'links', __( 'Links and buttons', 'jat-signal' ), false ],
		[ 'display', __( 'Display', 'jat-signal' ), true ],
		[ 'legacy', __( 'Kept from Developer Portfolio', 'jat-signal' ), false ],
	];

	function makeEdit( name ) {
		return function Edit( props ) {
			const { attributes, setAttributes, context } = props;
			const type = getBlockType( name );
			const defs = ( type && type.attributes ) || {};
			const specs = Object.keys( defs ).filter( ( k ) => ! SKIP.includes( k ) ).map( ( k ) => specFor( name, k, defs[ k ] ) ).filter( ( s ) => s.type !== 'hidden' && ( ! s.showIf || s.showIf( attributes ) ) );
			const blockProps = useBlockProps( { className: 'jat-ssr' } );
			const postId = ( context && context.postId ) || undefined;

			const valueOf = ( s ) => {
				const v = attributes[ s.key ];
				if ( s.json ) {
					return parse( v == null ? defs[ s.key ].default : v, [] );
				}
				if ( s.type === 'posts' ) {
					return parse( v || '[]', [] );
				}
				return v;
			};
			const change = ( s ) => ( v ) => {
				const out = ( s.json || s.type === 'posts' ) ? JSON.stringify( v ) : v;
				setAttributes( { [ s.key ]: out } );
			};
			const render = ( s ) => {
				if ( s.type === 'media' || s.type === 'mediaId' ) {
					return el( 'div', { key: s.key, className: 'jat-field' }, el( MediaControl, { label: s.label, attributes, setAttributes, spec: s } ) );
				}
				return el( 'div', { key: s.key, className: 'jat-field' }, el( Field, { spec: s, value: valueOf( s ), onChange: change( s ) } ) );
			};

			const panels = GROUPS.map( ( [ g, title, initialOpen ] ) => {
				const list = specs.filter( ( s ) => group( s ) === g );
				if ( ! list.length ) {
					return null;
				}
				return el( C.PanelBody, { key: g, title, initialOpen },
					g === 'legacy' ? el( 'p', { className: 'jat-help' }, __( 'Saved with your content but not shown in this design.', 'jat-signal' ) ) : null,
					list.map( render )
				);
			} );
			if ( name === 'developer-portfolio/tech-stack' ) {
				panels.splice( 1, 0, el( C.PanelBody, { key: 'stack', title: __( 'Technologies on this page', 'jat-signal' ), initialOpen: true }, el( TechStackPanel, { context: context || {} } ) ) );
			}

			return el( Fragment, null,
				el( InspectorControls, null,
					NOTES[ name ] ? el( 'div', { className: 'jat-inspector-note' }, NOTES[ name ] ) : null,
					panels
				),
				el( 'div', blockProps,
					el( SSR, {
						block: name, attributes, urlQueryArgs: postId ? { post_id: postId } : undefined, skipBlockSupportAttributes: true,
						LoadingResponsePlaceholder: Placeholder, EmptyResponsePlaceholder: Empty,
					} )
				)
			);
		};
	}

	function Placeholder( { children } ) {
		return el( 'div', { className: 'jat-ssr__loading' }, children || el( C.Spinner ) );
	}
	function Empty() {
		return el( 'div', { className: 'jat-ssr__empty readout' }, __( 'Nothing to show here yet. Check the block settings.', 'jat-signal' ) );
	}

	/* Register every theme block that the server registered from block.json. */
	const names = ( window.JAT_EDITOR && window.JAT_EDITOR.blocks ) || [];
	names.forEach( ( name ) => {
		if ( ! getBlockType( name ) ) {
			registerBlockType( name, { edit: makeEdit( name ), save: () => null } );
		}
	} );
}( window.wp ) );
