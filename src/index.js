(function(wp) {
  const { hooks, blocks, editor, data } = wp;
  const { createHigherOrderComponent } = wp.compose;
  const { InspectorControls, BlockEdit } = editor;
  const { Fragment } = wp.element;
  const { select } = data;

  const withMobileAttributes = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
      const { attributes, setAttributes, isSelected } = props;
      const { mobileAttributes = {} } = attributes;

      const isMobilePreview = select('core/edit-post').__experimentalGetPreviewDeviceType() || select('core/edit-site').__experimentalGetPreviewDeviceType();

      const newSetAttributes = (newAttributes) => {
        console.log(newAttributes);
        if (isMobilePreview) {
          setAttributes({ mobileAttributes: { ...mobileAttributes, ...newAttributes } });
        } else {
          setAttributes(newAttributes);
        }
      };

      return (
          <BlockEdit {...props}  setAttributes={newSetAttributes} />
      );
    };
  }, 'withMobileAttributes');

  wp.hooks.addFilter('editor.BlockEdit', 'rba/with-mobile-attributes', withMobileAttributes);

})(wp);
