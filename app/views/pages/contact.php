<?php ob_start(); ?>

<div class="container mt-4 mb-4">
    <h1 class="page-title">Kontakti</h1>

    <div class="grid grid-2 mt-3" style="gap: 2rem;">
        <div class="card">
            <div class="card-body">
                <h2>Sazinies ar mums</h2>

                <?php if (Session::has('errors')): ?>
                    <div class="alert alert-danger mt-3">
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            <?php foreach (Session::get('errors') as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (Session::has('success')): ?>
                    <div class="alert alert-success mt-3">
                        <?= e(Session::get('success')) ?>
                    </div>
                <?php endif; ?>

                <form action="/contact/submit" method="POST" class="mt-3">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="name"><?= lang('common.name') ?> *</label>
                        <input type="text" name="name" id="name" class="form-control"
                               value="<?= e(Session::getOldInput('name')) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><?= lang('common.email') ?> *</label>
                        <input type="email" name="email" id="email" class="form-control"
                               value="<?= e(Session::getOldInput('email')) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="subject">Tēma *</label>
                        <input type="text" name="subject" id="subject" class="form-control"
                               value="<?= e(Session::getOldInput('subject')) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Ziņojums *</label>
                        <textarea name="message" id="message" class="form-control" rows="6" required><?= e(Session::getOldInput('message')) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Nosūtīt ziņojumu</button>
                </form>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-body">
                    <h3>Kontaktinformācija</h3>

                    <div style="margin-top: 1.5rem;">
                        <p style="margin-bottom: 1rem;">
                            <strong>📧 E-pasts:</strong><br>
                            <a href="mailto:info@celteka.lv">info@celteka.lv</a>
                        </p>

                        <p style="margin-bottom: 1rem;">
                            <strong>📞 Tālrunis:</strong><br>
                            +371 20 000 000
                        </p>

                        <p style="margin-bottom: 1rem;">
                            <strong>🏢 Adrese:</strong><br>
                            Brīvības iela 1<br>
                            Rīga, LV-1010<br>
                            Latvija
                        </p>

                        <p style="margin-bottom: 0;">
                            <strong>🕐 Darba laiks:</strong><br>
                            Pirmdiena - Piektdiena: 9:00 - 17:00<br>
                            Sestdiena - Svētdiena: Slēgts
                        </p>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h3>Biežāk uzdotie jautājumi</h3>

                    <div style="margin-top: 1.5rem;">
                        <p><strong>Kā es varu pievienot savus produktus?</strong></p>
                        <p style="margin-bottom: 1.5rem;">Reģistrējieties kā pārdevējs un pieejiet savai kontrollpultij, kur varēsiet pievienot un pārvaldīt savus produktus.</p>

                        <p><strong>Kā darbojas piegāde?</strong></p>
                        <p style="margin-bottom: 1.5rem;">Katrs pārdevējs var piedāvāt dažādas piegādes iespējas - paņemšanu uz vietas, personīgo piegādi vai izmantot centralizēto piegādes sistēmu.</p>

                        <p><strong>Vai platformas izmantošana ir maksas?</strong></p>
                        <p style="margin-bottom: 0;">Reģistrācija platformā ir bezmaksas. Komisijas maksu par veiksmīgiem darījumiem skatiet mūsu <a href="/pricing">cenrādī</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Kontakti - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
