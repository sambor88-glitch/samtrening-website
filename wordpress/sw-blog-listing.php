<?php
/* =====================================================================
 * SW-BLOG-LISTING — dynamiczna lista wpisów na /blog/
 * Motyw: samtrening-2026
 *
 * WDROŻENIE — jedna z dwóch opcji:
 *   A) wklej całą zawartość tego pliku na KOŃCU functions.php motywu, albo
 *   B) (zalecane) skopiuj plik do motywu jako inc/sw-blog-listing.php
 *      i dodaj w functions.php:
 *          require_once get_theme_file_path( 'inc/sw-blog-listing.php' );
 *      — łatwiej aktualizować i nie puchnie functions.php.
 *
 * Następnie w inc/content/content-blog.php zastąp sekcje .filters,
 * .featured i .posts-section (razem z #posts-empty) jedną linią:
 *     <?php samtrening_blog_listing(); ?>
 *
 * Klasy CSS i atrybuty data-* są te same co w makiecie (blog.html),
 * więc istniejący JS filtrów działa bez zmian.
 *
 * Pełna instrukcja i checklista: docs/wdrozenie-blog-listing.md
 * ===================================================================== */

if ( ! function_exists( 'samtrening_blog_listing' ) ) {

	/**
	 * Szacowany czas czytania (200 słów / min).
	 *
	 * Liczymy słowa przez \p{L}+, bo str_word_count() nie rozumie polskich
	 * znaków (ł, ą, ż) — rozbija wyrazy i zawyża wynik o ~25%.
	 */
	function sw_blog_read_time( $post_id ) {
		$text  = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
		$words = (int) preg_match_all( '/\p{L}+/u', $text );

		return max( 1, (int) ceil( $words / 200 ) );
	}

	/**
	 * Okładka wpisu.
	 *
	 * Brak miniatury → zdjęcie zastępcze z filtra 'sw_blog_fallback_image'.
	 * Gdy filtr zwróci '', renderujemy placeholder z makiety — lepszy niż
	 * zepsuty obrazek i 404 w logach.
	 */
	function sw_blog_image( $post_id, $size = 'medium_large', $placeholder = '[ Zdjęcie ]' ) {
		$style = 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover;';

		if ( has_post_thumbnail( $post_id ) ) {
			return get_the_post_thumbnail( $post_id, $size, array(
				'style'   => $style,
				'loading' => 'lazy',
				'alt'     => esc_attr( get_the_title( $post_id ) ),
			) );
		}

		$fallback = apply_filters(
			'sw_blog_fallback_image',
			home_url( '/wp-content/uploads/2026/05/studio-1200x800.jpg' ),
			$post_id
		);

		if ( $fallback ) {
			return '<img src="' . esc_url( $fallback ) . '" alt="" loading="lazy" style="' . esc_attr( $style ) . '">';
		}

		return '<span class="placeholder">' . esc_html( $placeholder ) . '</span>';
	}

	/**
	 * Pierwsza „prawdziwa” kategoria wpisu — pomijamy Bez kategorii.
	 */
	function sw_blog_cat( $post_id ) {
		$cats = get_the_category( $post_id );

		foreach ( $cats as $c ) {
			if ( 'bez-kategorii' !== $c->slug && 'uncategorized' !== $c->slug ) {
				return $c;
			}
		}

		return $cats ? $cats[0] : null;
	}

	function samtrening_blog_listing() {
		// Uwaga: sticky posts nie działają w zapytaniu pobocznym (WP_Query
		// przestawia je tylko w zapytaniu głównym), więc „Wyróżniony” to
		// zawsze najnowszy wpis — celowo nie podajemy ignore_sticky_posts.
		$q = new WP_Query( array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 60,
			'no_found_rows'  => true,
		) );

		if ( ! $q->have_posts() ) {
			// Zostawiamy id #posts-empty, #posts-grid i #reset-filters —
			// JS makiety woła getElementById( ... ).addEventListener,
			// bez nich cały skrypt bloga leci na TypeError.
			?>
  <section class="posts-section" aria-label="Lista wpisów">
    <div class="posts-grid" id="posts-grid"></div>
    <div class="posts-empty is-visible" id="posts-empty">
      <h3>Wkrótce <span class="accent">pierwsze wpisy.</span></h3>
      <p>Publikujemy nowe wpisy 2 razy w tygodniu — zajrzyj za parę dni.</p>
      <button class="btn btn--ghost" id="reset-filters" hidden>Zresetuj filtry <span class="arrow">→</span></button>
    </div>
  </section>
			<?php
			return;
		}

		// Pierwszy wpis idzie do sekcji „Wyróżnione”, reszta do siatki.
		// Liczniki budujemy TYLKO z wpisów w siatce — wyróżniony nie jest
		// filtrowany (JS łapie .post, nie .featured-post), więc licząc go
		// pill pokazywałby 3, a filtr odsłaniał 2 karty.
		$grid    = array_slice( $q->posts, 1 );
		$total   = count( $grid );
		$cats    = array();
		$authors = array();

		foreach ( $grid as $p ) {
			$c = sw_blog_cat( $p->ID );
			if ( $c ) {
				$cats[ $c->slug ] = array(
					'name'  => $c->name,
					'count' => ( $cats[ $c->slug ]['count'] ?? 0 ) + 1,
				);
			}
			$u = get_userdata( $p->post_author );
			if ( $u ) {
				$authors[ $u->user_nicename ] = $u->display_name;
			}
		}

		if ( $total > 0 ) :
			?>
  <section class="filters" aria-label="Filtry wpisów">
    <div class="filters__inner">
      <div class="filter-group">
        <div class="filter-group__label">Kategoria</div>
        <div class="filter-pills" role="tablist" aria-label="Filtruj po kategorii">
          <button class="pill is-active" data-filter-category="all" role="tab" aria-selected="true">
            Wszystkie <span class="count"><?php echo (int) $total; ?></span>
          </button>
          <?php foreach ( $cats as $slug => $c ) : ?>
          <button class="pill" data-filter-category="<?php echo esc_attr( $slug ); ?>" role="tab" aria-selected="false">
            <?php echo esc_html( $c['name'] ); ?> <span class="count"><?php echo (int) $c['count']; ?></span>
          </button>
          <?php endforeach; ?>
        </div>
      </div>
      <?php if ( count( $authors ) > 1 ) : ?>
      <div class="filter-group">
        <div class="filter-group__label">Autor</div>
        <div class="filter-pills" role="tablist" aria-label="Filtruj po autorze">
          <button class="pill is-active" data-filter-author="all" role="tab" aria-selected="true">Wszyscy</button>
          <?php foreach ( $authors as $slug => $name ) : ?>
          <button class="pill" data-filter-author="<?php echo esc_attr( $slug ); ?>" role="tab" aria-selected="false"><?php echo esc_html( $name ); ?></button>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </section>
			<?php
		endif;

		$first = true;
		while ( $q->have_posts() ) :
			$q->the_post();
			$id    = get_the_ID();
			$c     = sw_blog_cat( $id );
			$cslug = $c ? $c->slug : '';
			$cname = $c ? $c->name : '';
			$u     = get_userdata( get_the_author_meta( 'ID' ) );
			$aslug = $u ? $u->user_nicename : '';
			$aname = get_the_author();
			$ainit = mb_substr( $aname, 0, 1 );
			$mins  = sw_blog_read_time( $id );

			if ( $first ) :
				$first = false; ?>
  <section class="featured" aria-labelledby="featured-title">
    <div class="featured__inner">
      <div style="margin-bottom:48px;"><div class="eyebrow eyebrow--accent">Wyróżnione</div></div>
      <a href="<?php the_permalink(); ?>" class="featured-post" data-category="<?php echo esc_attr( $cslug ); ?>" data-author="<?php echo esc_attr( $aslug ); ?>">
        <div class="featured-post__image">
          <?php echo sw_blog_image( $id, 'large', '[ Zdjęcie wyróżnione wpisu blogowego ]' ); ?>
          <div class="featured-badge" style="z-index:2;">Najnowszy</div>
        </div>
        <div class="featured-post__body">
          <div class="featured-post__meta">
            <span class="tag"><?php echo esc_html( $cname ); ?></span>
            <span><?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?></span>
            <span class="dot">●</span>
            <span><?php echo (int) $mins; ?> min czytania</span>
          </div>
          <h2 id="featured-title"><?php the_title(); ?></h2>
          <p class="featured-post__excerpt"><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
          <div class="featured-post__footer">
            <div class="featured-post__author">
              <div class="featured-post__avatar"><?php echo esc_html( $ainit ); ?></div>
              <div><div class="featured-post__author-name"><?php echo esc_html( $aname ); ?></div></div>
            </div>
            <div class="featured-post__read">Czytaj cały wpis <span>→</span></div>
          </div>
        </div>
      </a>
    </div>
  </section>
  <section class="posts-section" aria-label="Lista wpisów">
    <div class="posts-grid" id="posts-grid">
			<?php else : ?>
      <a href="<?php the_permalink(); ?>" class="post" data-category="<?php echo esc_attr( $cslug ); ?>" data-author="<?php echo esc_attr( $aslug ); ?>">
        <div class="post__image"><?php echo sw_blog_image( $id ); ?></div>
        <div class="post__body">
          <div class="post__meta">
            <span class="post__tag"><?php echo esc_html( $cname ); ?></span>
            <span><?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?></span>
          </div>
          <h3><?php the_title(); ?></h3>
          <p class="post__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
          <div class="post__footer">
            <div class="post__author"><span class="avatar"><?php echo esc_html( $ainit ); ?></span> <?php echo esc_html( $aname ); ?> · <?php echo (int) $mins; ?> min</div>
            <span class="post__read">Czytaj →</span>
          </div>
        </div>
      </a>
			<?php endif;
		endwhile;
		wp_reset_postdata();
		?>
    </div>
    <div class="posts-empty" id="posts-empty">
      <h3>Brak wpisów <span class="accent">w tej kategorii.</span></h3>
      <p>Spróbuj innej kombinacji filtrów.</p>
      <button class="btn btn--ghost" id="reset-filters">Zresetuj filtry <span class="arrow">→</span></button>
    </div>
  </section>
		<?php
	}
}
/* === /SW-BLOG-LISTING === */
