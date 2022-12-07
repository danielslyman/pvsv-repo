( function( blocks, element, data, editor, i18n, components ) {

    // Required components from WordPress
    var registerBlockType = blocks.registerBlockType; // registerBlockType function that creates a block
    var el = element.createElement;


    var InspectorControls = editor.InspectorControls,
        SelectControl = components.SelectControl,
        ToggleControl = components.ToggleControl,
        ServerSideRender = components.ServerSideRender;

    // Other components
    var __ = i18n.__; // Internationalisation

    registerBlockType( 'erecht24/erecht24', {
        title: __('eRecht24', 'erecht24'),
        icon: 'universal-access-alt',
        category: 'common',
        attributes: {
            lang: {
                type: 'string',
                default: 'de',
            },
            type: {
                type: 'string',
                default: 'imprint',
            },
            strip_title: {
                type: 'boolean'
            },
        },

        edit: function( props ) {
            var attributes = props.attributes,
                className = props.className,
                setAttributes = props.setAttributes;

            return [
                el( ServerSideRender, {
                    block: 'erecht24/erecht24',
                    attributes: attributes
                } ),
                el(InspectorControls, { key: "inspector"},
                    el('h2', {}, __('Settings', 'erecht24')),

                    el( SelectControl,
                        {
                            label: __( 'Type', 'erecht24' ),
                            options : [
                                { label: __( 'Imprint', 'erecht24' ), value: 'imprint' },
                                { label: __( 'Privacy Policy', 'erecht24' ), value: 'privacy_policy' },
                                { label: __( 'Privacy Policy for social media', 'erecht24' ), value: 'privacy_policy_social_media' },
                            ],
                            onChange: ( value ) => {
                                setAttributes( { type: value } );
                            },
                            value: attributes.type
                        }
                    ),

                    // Select dropdown field
                    el( SelectControl,
                        {
                            label: __( 'Language', 'erecht24' ),
                            options : [
                                { label: __( 'German', 'erecht24' ), value: 'de' },
                                { label: __( 'English', 'erecht24' ), value: 'en' },
                            ],
                            onChange: ( value ) => {
                                setAttributes( { lang: value } );
                            },
                            value: attributes.lang
                        }
                    ),

                    el( ToggleControl,
                        {
                            label: __( 'Remove H1', 'erecht24' ),
                            onChange: ( value ) => {
                                setAttributes( { strip_title: value } );
                            },
                            checked: attributes.strip_title,
                        }
                    )
                ),
            ];
        },

        save: function(props) {
            return null;
        },
    } );
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
    window.wp.editor,
    window.wp.i18n,
    window.wp.components
) );
