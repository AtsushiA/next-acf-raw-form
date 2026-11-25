import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test.describe( 'NExT ACF Raw Form Output Block', () => {
	test( 'プラグインが有効化されている', async ( { admin, page } ) => {
		await admin.visitAdminPage( 'plugins.php' );
		// data-slug は WordPress がプラグイン名を sanitize_title() した値
		const pluginRow = page.locator( 'tr[data-slug="next-acf-raw-form-output-block"]' );
		await expect( pluginRow ).toHaveClass( /active/ );
	} );
} );
