<?= render('page-hero', ['page' => $page, 'lead' => $page['description']]) ?>
<section class="section">
    <div class="container with-sidebar">
        <article class="prose">
            <?php
            $body = ROOT . '/templates/content/' . $page['slug'] . '.php';
            if (is_file($body)) {
                include $body;
            } else { ?>
                <p class="notice">This page is ready for its content. Run the importer (<code>npm run crawl</code>) to pull the text from the live site, or add it in <code>templates/content/<?= e($page['slug']) ?>.php</code>.</p>
            <?php } ?>
        </article>
        <aside class="sidebar">
            <?php partial('contact-form', ['page' => $page]) ?>
        </aside>
    </div>
</section>
