import iconsList from '../functions/iconsList';

export default function Icons( { icon } ) {
	const il = iconsList();

	for ( const [ key, value ] of Object.entries( il ) ) {
		if ( icon === key ) {
			return value;
		}
	}
}
