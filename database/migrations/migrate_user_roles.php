<?php
/**
 * Migrācijas skripts: Lietotāju lomu pārnese uz user_role_assignments tabulu
 *
 * Šis skripts migrē esošo lietotāju lomas no role_id kolonnas
 * uz jauno user_role_assignments junction tabulu, kas atbalsta vairākas lomas.
 *
 * Izpildīšana: php database/migrations/migrate_user_roles.php
 */

// Ielādēt aplikācijas konfigurāciju
require_once __DIR__ . '/../../public/index.php';

echo "=== Lietotāju lomu migrācija ===\n\n";

try {
    $db = db();

    // Pārbaudīt, vai migrācija jau ir izpildīta
    $existingRoles = $db->fetchColumn("SELECT COUNT(*) FROM user_role_assignments");

    if ($existingRoles > 0) {
        echo "⚠️  BRĪDINĀJUMS: user_role_assignments tabula jau satur ierakstus ($existingRoles).\n";
        echo "Vai vēlaties turpināt un pievienot trūkstošās lomas? (y/n): ";
        $input = trim(fgets(STDIN));
        if (strtolower($input) !== 'y') {
            echo "Migrācija atcelta.\n";
            exit(0);
        }
    }

    // Iegūt visus lietotājus ar viņu role_id
    $users = $db->fetchAll("
        SELECT id, full_name, email, role_id
        FROM users
        WHERE role_id IS NOT NULL
        ORDER BY id
    ");

    if (empty($users)) {
        echo "✓ Nav lietotāju ar role_id. Migrācija nav nepieciešama.\n";
        exit(0);
    }

    echo "Atrasti " . count($users) . " lietotāji ar role_id.\n\n";

    // Lomu kartējums (role_id -> role nosaukums)
    $roleMapping = [
        1 => 'buyer',
        2 => 'seller',
        3 => 'admin'
    ];

    $migratedCount = 0;
    $skippedCount = 0;

    foreach ($users as $user) {
        $userId = $user['id'];
        $roleId = $user['role_id'];
        $role = $roleMapping[$roleId] ?? null;

        if (!$role) {
            echo "⚠️  Izlaists: Lietotājs ID {$userId} ({$user['email']}) - nezināms role_id: {$roleId}\n";
            $skippedCount++;
            continue;
        }

        // Pārbaudīt, vai loma jau eksistē
        $exists = $db->fetchColumn(
            "SELECT COUNT(*) FROM user_role_assignments WHERE user_id = ? AND role = ?",
            [$userId, $role]
        );

        if ($exists > 0) {
            echo "⊙ Izlaists: Lietotājs ID {$userId} ({$user['email']}) - loma '{$role}' jau eksistē\n";
            $skippedCount++;
            continue;
        }

        // Pievienot lomu
        try {
            $db->insert('user_role_assignments', [
                'user_id' => $userId,
                'role' => $role,
                'assigned_at' => date('Y-m-d H:i:s')
            ]);

            echo "✓ Migrēts: Lietotājs ID {$userId} ({$user['email']}) → loma '{$role}'\n";
            $migratedCount++;
        } catch (Exception $e) {
            echo "✗ Kļūda: Lietotājs ID {$userId} - " . $e->getMessage() . "\n";
            $skippedCount++;
        }
    }

    echo "\n=== Migrācija pabeigta ===\n";
    echo "Veiksmīgi migrēti: {$migratedCount}\n";
    echo "Izlaisti: {$skippedCount}\n";

    // Pēdējā pārbaude: vai pirmajam lietotājam ir admin loma
    $firstUser = $db->fetchOne("SELECT id, full_name, email FROM users ORDER BY id LIMIT 1");
    if ($firstUser) {
        $hasAdmin = $db->fetchColumn(
            "SELECT COUNT(*) FROM user_role_assignments WHERE user_id = ? AND role = 'admin'",
            [$firstUser['id']]
        );

        if ($hasAdmin == 0) {
            echo "\n⚠️  BRĪDINĀJUMS: Pirmajam lietotājam (ID {$firstUser['id']}) nav admin lomas!\n";
            echo "Vai vēlaties piešķirt admin lomu lietotājam {$firstUser['email']}? (y/n): ";
            $input = trim(fgets(STDIN));
            if (strtolower($input) === 'y') {
                $db->insert('user_role_assignments', [
                    'user_id' => $firstUser['id'],
                    'role' => 'admin',
                    'assigned_at' => date('Y-m-d H:i:s')
                ]);
                echo "✓ Admin loma piešķirta lietotājam {$firstUser['email']}\n";
            }
        } else {
            echo "\n✓ Pirmajam lietotājam (ID {$firstUser['id']}) ir admin loma.\n";
        }
    }

} catch (Exception $e) {
    echo "\n✗ KRITISKA KĻŪDA: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== Gatavs! ===\n";
