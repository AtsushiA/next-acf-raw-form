
/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

/**
 * WordPress dependencies
 */
import { PanelBody, TextControl, Notice } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object}   props               Properties passed to the function.
 * @param {Object}   props.attributes    Available block attributes.
 * @param {Function} props.setAttributes Function that updates individual attributes.
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { fieldName } = attributes;
	const [ fieldValue, setFieldValue ] = useState( '' );
	const [ isLoading, setIsLoading ] = useState( false );
	const [ error, setError ] = useState( null );

	const postId = useSelect( ( select ) => {
		return select( 'core/editor' ).getCurrentPostId();
	}, [] );

	useEffect( () => {
		if ( ! fieldName || ! postId ) {
			setFieldValue( '' );
			return;
		}

		setIsLoading( true );
		setError( null );

		apiFetch( {
			path: `/wp/v2/posts/${ postId }`,
		} )
			.then( ( post ) => {
				if ( post.acf && post.acf[ fieldName ] ) {
					setFieldValue( post.acf[ fieldName ] );
				} else {
					setFieldValue( '' );
					setError(
						__(
							'ACF field not found or empty',
							'next-acf-raw-form'
						)
					);
				}
				setIsLoading( false );
			} )
			.catch( () => {
				setFieldValue( '' );
				setError(
					__(
						'Unable to fetch ACF field value',
						'next-acf-raw-form'
					)
				);
				setIsLoading( false );
			} );
	}, [ fieldName, postId ] );

	const blockProps = useBlockProps( {
		className: 'acf-raw-form-output-block',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'ACF Field Settings', 'next-acf-raw-form' ) }
				>
					<TextControl
						label={ __( 'ACF Field Name', 'next-acf-raw-form' ) }
						value={ fieldName }
						onChange={ ( value ) =>
							setAttributes( { fieldName: value } )
						}
						help={ __(
							'Enter the name of the ACF field to display (e.g., application_form)',
							'next-acf-raw-form'
						) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ ! fieldName && (
					<Notice status="warning" isDismissible={ false }>
						{ __(
							'Please enter an ACF field name in the block settings',
							'next-acf-raw-form'
						) }
					</Notice>
				) }

				{ fieldName && isLoading && (
					<div className="acf-raw-form-loading">
						<p>
							{ __(
								'Loading ACF field content...',
								'next-acf-raw-form'
							) }
						</p>
					</div>
				) }

				{ fieldName && ! isLoading && error && (
					<Notice status="info" isDismissible={ false }>
						{ error }
					</Notice>
				) }

				{ fieldName && ! isLoading && ! error && fieldValue && (
					<div className="acf-raw-form-preview">
						<Notice status="info" isDismissible={ false }>
							{ __(
								'Preview: Raw HTML from ACF field will be rendered here',
								'next-acf-raw-form'
							) }
						</Notice>
						<div className="acf-raw-form-content">
							<pre>{ fieldValue.substring( 0, 200 ) }...</pre>
						</div>
					</div>
				) }

				{ fieldName &&
					! isLoading &&
					! error &&
					! fieldValue && (
						<Notice status="info" isDismissible={ false }>
							{ __(
								'ACF field is empty',
								'next-acf-raw-form'
							) }
						</Notice>
					) }
			</div>
		</>
	);
}
