<?php ob_start(); ?>

<div class="container mt-4 mb-4">
    <h1 class="page-title">Par mums</h1>

    <div class="card mt-3">
        <div class="card-body">
            <h2>Vietējais Tirgus - platforma vietējiem ražotājiem</h2>

            <p style="margin-top: 1rem; line-height: 1.6;">
                <strong>Vietējais Tirgus</strong> ir moderna un ērta tiešsaistes platforma, kas savieno vietējos ražotājus, amatniekus un pakalpojumu sniedzējus ar pircējiem visā Latvijā.
            </p>

            <h3 class="mt-4">Mūsu misija</h3>
            <p style="line-height: 1.6;">
                Mūsu mērķis ir veicināt vietējās ekonomikas attīstību, piedāvājot vienkāršu un efektīvu veidu, kā vietējie ražotāji var sasniegt savus klientus un pārdot savus produktus tiešsaistē.
            </p>

            <h3 class="mt-4">Ko mēs piedāvājam?</h3>
            <div class="grid grid-2 mt-3">
                <div class="card">
                    <div class="card-body">
                        <h4>🌾 Ražotājiem</h4>
                        <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                            <li>Vienkārša produktu pievienošana un pārvaldība</li>
                            <li>Pasūtījumu uzraudzība</li>
                            <li>Tiešsaistes maksājumi</li>
                            <li>Atsauksmju sistēma</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h4>🛒 Pircējiem</h4>
                        <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                            <li>Plašs vietējo produktu klāsts</li>
                            <li>Droša iepirkšanās</li>
                            <li>Elastīgas piegādes iespējas</li>
                            <li>Iespēja atbalstīt vietējos uzņēmējus</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h3 class="mt-4">Mūsu vērtības</h3>
            <div class="mt-3">
                <p style="line-height: 1.6;"><strong>🤝 Uzticība</strong> - Mēs būvējam uzticības pilnas attiecības starp pircējiem un pārdevējiem</p>
                <p style="line-height: 1.6;"><strong>🌱 Ilgtspējība</strong> - Atbalstām vietējo ražošanu un ilgtspējīgu patēriņu</p>
                <p style="line-height: 1.6;"><strong>💡 Inovācija</strong> - Pastāvīgi uzlabojam platformu, lai tā būtuērtāka lietotājiem</p>
                <p style="line-height: 1.6;"><strong>🎯 Kvalitāte</strong> - Veicināmam augstas kvalitātes produktu un pakalpojumu piedāvājumu</p>
            </div>

            <h3 class="mt-4">Kontaktinformācija</h3>
            <p style="line-height: 1.6;">
                Ja jums ir jautājumi vai ierosinājumi, lūdzu, sazinieties ar mums, izmantojot <a href="/contact">kontaktu formu</a>.
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Par mums - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
