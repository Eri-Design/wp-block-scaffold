/**
 * WordPress dependencies
 */
import { registerBlockType, createBlock } from "@wordpress/blocks";
import { InnerBlocks, useBlockProps } from "@wordpress/block-editor";
import { useDispatch, useSelect } from "@wordpress/data";
import { Button } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

/**
 * Internal dependencies
 */
import metadata from "./block.json";
import "./style.scss";

registerBlockType(metadata, {
  edit: Edit,
  save: Save,
});

function Edit(props) {}

function Save() {}
