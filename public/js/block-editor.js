/**
 * Gutenberg Block Editor
 *
 * @package Smart_FAQ_Manager
 */

(function(wp) {
    'use strict';
    
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var SelectControl = wp.components.SelectControl;
    var ToggleControl = wp.components.ToggleControl;
    var RangeControl = wp.components.RangeControl;
    var ServerSideRender = wp.serverSideRender;
    var createElement = wp.element.createElement;
    
    registerBlockType('smart-faq/faq-widget', {
        title: 'Smart FAQ',
        description: 'Display contextually relevant FAQs',
        icon: 'editor-help',
        category: 'widgets',
        keywords: ['faq', 'question', 'answer'],
        
        attributes: {
            limit: {
                type: 'number',
                default: 0
            },
            category: {
                type: 'string',
                default: ''
            },
            style: {
                type: 'string',
                default: 'accordion'
            },
            showNumbers: {
                type: 'boolean',
                default: true
            }
        },
        
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            
            // Build category options
            var categoryOptions = [{ label: 'All Categories', value: '' }];
            if (smartFaqBlock && smartFaqBlock.categories) {
                smartFaqBlock.categories.forEach(function(cat) {
                    categoryOptions.push({ label: cat, value: cat });
                });
            }
            
            return createElement('div', { className: props.className },
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: 'FAQ Settings', initialOpen: true },
                        createElement(RangeControl, {
                            label: 'Number of FAQs (0 = use global setting)',
                            value: attributes.limit,
                            onChange: function(value) { setAttributes({ limit: value }); },
                            min: 0,
                            max: 20,
                            help: 'Set to 0 to use the global "Maximum FAQs" setting from Settings page'
                        }),
                        createElement(SelectControl, {
                            label: 'Display Style',
                            value: attributes.style,
                            options: [
                                { label: 'Accordion', value: 'accordion' },
                                { label: 'List', value: 'list' },
                                { label: 'Grid', value: 'grid' }
                            ],
                            onChange: function(value) { setAttributes({ style: value }); }
                        }),
                        createElement(SelectControl, {
                            label: 'Category Filter',
                            value: attributes.category,
                            options: categoryOptions,
                            onChange: function(value) { setAttributes({ category: value }); }
                        }),
                        createElement(ToggleControl, {
                            label: 'Show Question Numbers',
                            checked: (typeof attributes.showNumbers !== 'undefined') ? attributes.showNumbers : (smartFaqBlock && smartFaqBlock.defaults ? smartFaqBlock.defaults.showNumbers : true),
                            onChange: function(value) { setAttributes({ showNumbers: value }); }
                        })
                    )
                ),
                createElement(ServerSideRender, {
                    block: 'smart-faq/faq-widget',
                    attributes: attributes
                })
            );
        },
        
        save: function() {
            return null; // Server-side rendering
        }
    });
    
})(window.wp);

