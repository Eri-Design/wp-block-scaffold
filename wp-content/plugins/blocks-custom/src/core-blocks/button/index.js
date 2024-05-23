/**
 * WordPress dependencies
 */
import { registerBlockStyle, unregisterBlockStyle } from "@wordpress/blocks";
import { createHigherOrderComponent } from "@wordpress/compose";
import { select } from "@wordpress/data";
import { addFilter } from "@wordpress/hooks";

/**
 * Internal dependencies
 */
import "./style.scss";

const allBlockStyles = [];

const registerBlockStyles = (blockStyles = null) => {
  if (!blockStyles) {
    allBlockStyles.forEach((blockStyle, index) => {
      if (
        "undefined" === blockStyle.availableForAll ||
        false !== blockStyle.availableForAll
      ) {
        registerBlockStyle("core/button", {
          name: blockStyle.name,
          label: blockStyle.label,
          isDefault: 0 === index ? true : false,
        });
      }
    });
  } else {
    blockStyles.forEach((blockStyle, index) => {
      const blockStyleAttrs = allBlockStyles.find(
        (style) => style.name === blockStyle
      );

      if (blockStyleAttrs && blockStyleAttrs.label) {
        registerBlockStyle("core/button", {
          name: blockStyle,
          label: blockStyleAttrs.label,
          isDefault: 0 === index ? true : false,
        });
      }
    });

    allBlockStyles.forEach((blockStyle) => {
      if (!blockStyles.includes(blockStyle.name)) {
        unregisterBlockStyle("core/button", blockStyle.name);
      }
    });
  }
};

const getDefaultStyle = (blockStyles = null) => {
  let defaultStyle = "";

  if (!blockStyles) {
    allBlockStyles.forEach((blockStyle, index) => {
      if (0 === index) {
        defaultStyle = blockStyle.name;
      }
    });
  } else {
    blockStyles.forEach((blockStyle, index) => {
      const blockStyleAttrs = allBlockStyles.find(
        (style) => style.name === blockStyle
      );

      if (blockStyleAttrs && blockStyleAttrs.label) {
        if (0 === index || !defaultStyle) {
          defaultStyle = blockStyle;
        }
      }
    });
  }

  return defaultStyle;
};

const modifyButtonInspector = createHigherOrderComponent((BlockEdit) => {
  return (props) => {
    if ("core/button" !== props.name) {
      return <BlockEdit {...props} />;
    }

    const blockEditorStore = "core/block-editor";
    const { getBlockParents, getBlock } = select(blockEditorStore);
    let defaultStyle = "";
    let topLevelBlock;

    if (props.clientId) {
      const parentBlocks = getBlockParents(props.clientId);

      if (parentBlocks) {
        for (let i = parentBlocks.length - 1; i >= 0; i--) {
          topLevelBlock = getBlock(parentBlocks[i]);

          if (
            topLevelBlock &&
            topLevelBlock.attributes &&
            topLevelBlock.attributes.buttonStyles
          ) {
            defaultStyle = getDefaultStyle(
              topLevelBlock.attributes.buttonStyles
            );
          }
        }
      }
    }

    if (defaultStyle && props.attributes && !props.attributes.className) {
      props.attributes.className = "is-style-" + defaultStyle;
    }

    if (props.isSelected) {
      let blockStylesRegistered = false;

      if (
        topLevelBlock &&
        topLevelBlock.attributes &&
        topLevelBlock.attributes.buttonStyles
      ) {
        registerBlockStyles(topLevelBlock.attributes.buttonStyles);

        blockStylesRegistered = true;
      }

      if (false === blockStylesRegistered) {
        registerBlockStyles();
      }
    }

    return <BlockEdit {...props} />;
  };
}, "modifyButtonInspector");

addFilter(
  "editor.BlockEdit",
  "custom-blocks/modify-button-inspector",
  modifyButtonInspector
);
