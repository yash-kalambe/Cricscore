import { __ } from '@wordpress/i18n';
import EditCustomContentType from './Edit';

export default {
	type: 'insert_custom_content_type',
	label: __( 'Insert/Update Custom Content Type Item', 'jet-engine' ),
	edit: EditCustomContentType,
	icon: <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
		<rect x="0" fill="none" width="20" height="20"/>
		<g>
			<path
				d="M17 7V4h-2V2h-3v1H3v15h11V9h1V7h2zm-1-2v1h-2v2h-1V6h-2V5h2V3h1v2h2z"/>
		</g>
	</svg>,
	docHref: 'https://crocoblock.com/knowledge-base/jetengine/how-to-insert-update-cct-via-form/',
	category: 'content',
	validators: [
		( { settings } ) => {
			return settings?.type ? false : { property: 'type' };
		},
	],
};