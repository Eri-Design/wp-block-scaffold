/**
 * WordPress dependencies
 */
import { select } from '@wordpress/data';

const getInnerBlockCount = ( props ) => {
	const { clientId } = props;

	if ( ! clientId ) {
		return 0;
	}

	const blockEditorStore = 'core/block-editor';
	const { getBlockCount } = select( blockEditorStore );

	return getBlockCount( clientId );
};

const hasInnerBlock = ( props, blockName ) => {
	let innerBlocks = [];
	const { clientId } = props;

	if ( clientId ) {
		const blockEditorStore = 'core/block-editor';
		const { getBlock } = select( blockEditorStore );
		const block = getBlock( clientId );

		if ( ! block || ! block.innerBlocks ) {
			return false;
		}

		innerBlocks = block.innerBlocks;
	}

	let hasBlockName = false;

	if ( innerBlocks.length && innerBlocks.length > 0 ) {
		hasBlockName = innerBlocks.some( ( innerBlock ) => {
			return innerBlock.name === blockName;
		} );
	}

	return hasBlockName;
};

export { getInnerBlockCount, hasInnerBlock };
