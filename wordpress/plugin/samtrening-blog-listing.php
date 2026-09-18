<?php
/**
 * Plugin Name:       SAMtrening — lista wpisów na /blog/
 * Plugin URI:        https://github.com/sambor88-glitch/samtrening-website
 * Description:       Podmienia statyczne sekcje filtrów, wyróżnionego wpisu i siatki na /blog/ na wpisy pobierane z WordPressa. Nie modyfikuje żadnego pliku motywu — dezaktywacja wtyczki przywraca poprzedni wygląd strony.
 * Version:           1.0.0
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            SAMTRENING
 * License:           GPL-2.0-or-later
 * Text Domain:       samtrening-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SAMTRENING_BLOG_VERSION', '1.0.0' );
define( 'SAMTRENING_BLOG_MARKER', '<!-- samtrening-blog-listing -->' );

require_once __DIR__ . '/inc/sw-blog-listing.php';

/**
 * Wyrażenia na trzy sekcje z makiety. Klasa musi być pierwszym atrybutem
 * sekcji (tak jest w blog.html), dalsze atrybuty są dowolne.
 */
function samtrening_blog_patterns() {
	return array(
		'filters'  => '~<section\s+class="filters"[^>]*>.*?</section>~is',
		'featured' => '~<section\s+class="featured"[^>]*>.*?</section>~is',
		'posts'    => '~<section\s+class="posts-section"[^>]*>.*?</section>~is',
	);
}

/**
 * Render listy do stringa. Wołane PRZED ob_start() — wewnątrz callbacku
 * bufora nie wolno używać funkcji ob_*.
 */
function samtrening_blog_render() {
	ob_start();
	echo SAMTRENING_BLOG_MARKER;
	samtrening_blog_listing();

	return (string) ob_get_clean();
}

/**
 * Przechowuje wyrenderowaną listę między template_redirect i callbackiem bufora.
 */
function samtrening_blog_cache( $set = null ) {
	static $html = '';

	if ( null !== $set ) {
		$html = (string) $set;
	}

	return $html;
}

/**
 * Czy to strona listy wpisów (/blog/)? Nadpisywalne filtrem.
 */
function samtrening_blog_is_listing_page() {
	$is = false;

	if ( ! is_admin() && ! is_feed() && ! is_embed() && ! is_singular() && ! is_404() ) {
		$path = '';
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$path = untrailingslashit( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) );
		}
		$is = is_home() || is_page( 'blog' ) || '/blog' === $path;
	}

	return (bool) apply_filters( 'samtrening_blog_is_listing_page', $is );
}

/**
 * Włącza bufor na stronie listy wpisów.
 */
function samtrening_blog_maybe_swap() {
	if ( ! apply_filters( 'samtrening_blog_autoswap', true ) ) {
		return;
	}

	if ( wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	if ( ! samtrening_blog_is_listing_page() ) {
		return;
	}

	$listing = samtrening_blog_render();
	if ( '' === trim( $listing ) ) {
		return;
	}

	samtrening_blog_cache( $listing );
	ob_start( 'samtrening_blog_swap_sections' );
}
add_action( 'template_redirect', 'samtrening_blog_maybe_swap', 20 );

/**
 * Podmiana sekcji. Zasada: wszystko albo nic — przy jakimkolwiek problemie
 * zwracamy oryginalny HTML, więc strona nigdy nie wychodzi uszkodzona.
 */
function samtrening_blog_swap_sections( $html ) {
	if ( ! is_string( $html ) || '' === trim( $html ) ) {
		return $html;
	}

	// Szablon motywu już woła samtrening_blog_listing() — nie dublujemy.
	if ( false !== strpos( $html, SAMTRENING_BLOG_MARKER ) ) {
		return $html;
	}

	$listing = samtrening_blog_cache();
	if ( '' === trim( $listing ) ) {
		return $html;
	}

	try {
		$patterns = samtrening_blog_patterns();

		foreach ( $patterns as $key => $re ) {
			if ( ! preg_match( $re, $html, $m ) ) {
				samtrening_blog_note_miss( $key . ': nie znaleziono sekcji' );
				return $html;
			}
			// Zagnieżdżona <section> oznaczałaby, że dopasowanie ucięło się
			// w złym miejscu — wtedy nie ruszamy strony.
			if ( false !== stripos( substr( $m[0], 1 ), '<section' ) ) {
				samtrening_blog_note_miss( $key . ': zagnieżdżona sekcja' );
				return $html;
			}
		}

		$out = preg_replace( $patterns['featured'], '', $html, 1 );
		$out = preg_replace( $patterns['posts'], '', (string) $out, 1 );

		// preg_replace_callback, bo wstawiany HTML może zawierać \ albo $1.
		$out = preg_replace_callback(
			$patterns['filters'],
			static function () use ( $listing ) {
				return $listing;
			},
			(string) $out,
			1
		);

		if ( null === $out || '' === trim( (string) $out ) ) {
			return $html;
		}

		samtrening_blog_clear_miss();

		return (string) $out;
	} catch ( \Throwable $e ) {
		samtrening_blog_note_miss( 'wyjątek: ' . $e->getMessage() );

		return $html;
	}
}

/**
 * Zapisuje powód nieudanej podmiany, żeby pokazać go w kokpicie.
 */
function samtrening_blog_note_miss( $reason ) {
	set_transient( 'samtrening_blog_swap_miss', (string) $reason, DAY_IN_SECONDS );
}

function samtrening_blog_clear_miss() {
	if ( get_transient( 'samtrening_blog_swap_miss' ) ) {
		delete_transient( 'samtrening_blog_swap_miss' );
	}
}

/**
 * Komunikat w kokpicie, gdy podmiana się nie udała — bez tego wtyczka
 * milczałaby, a strona wyglądałaby jak przed instalacją.
 */
function samtrening_blog_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$reason = get_transient( 'samtrening_blog_swap_miss' );
	if ( ! $reason ) {
		return;
	}

	echo '<div class="notice notice-warning"><p><strong>SAMtrening — lista wpisów:</strong> nie udało się podmienić sekcji na /blog/ (' . esc_html( $reason ) . '). Strona została zostawiona bez zmian. Prawdopodobnie markup w szablonie różni się od makiety — wyślij plik <code>inc/content/content-blog.php</code> do poprawki.</p></div>';
}
add_action( 'admin_notices', 'samtrening_blog_admin_notice' );

/**
 * Sprzątanie po dezaktywacji.
 */
function samtrening_blog_deactivate() {
	delete_transient( 'samtrening_blog_swap_miss' );
}
register_deactivation_hook( __FILE__, 'samtrening_blog_deactivate' );
