<?php ob_start(); ?>

<section class="container mt-4 mb-4 text-center">
    <div style="font-size: 6rem; margin-bottom: 1rem;">🔍</div>
    <h1>404 - Lapa nav atrasta</h1>
    <p class="mt-2">Atvainojiet, bet meklētā lapa neeksistē.</p>
    <div class="mt-3">
        <a href="/" class="btn btn-primary">Atgriezties uz sākumlapu</a>
    </div>
</section>

<?php
$content = ob_get_clean();
$title = '404 - Lapa nav atrasta';
require __DIR__ . '/../layout.php';
?>
