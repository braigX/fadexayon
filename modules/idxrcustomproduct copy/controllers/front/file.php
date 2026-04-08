<?php
/**
 * modules/idxrcustomproduct/controllers/front/file.php
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class idxrcustomproductFileModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        // Lecture du seuil soumis (GET/POST), défaut 2926
        $minId = (int)Tools::getValue('min_id', 2926);

        // Optionnel : limite de lignes pour éviter des pages trop lourdes
        $limit = (int)Tools::getValue('limit', 200);
        if ($limit <= 0 || $limit > 1000) {
            $limit = 200;
        }

        // Construction de la requête avec le préfixe dynamique
        $sql = '
            SELECT *
            FROM `'._DB_PREFIX_.'idxrcustomproduct_notes`
            WHERE `id_order` > '.(int)$minId.'
              AND `private_note` LIKE "%Rectangle%"
              AND `private_note` NOT LIKE "%Arrondi%"
            ORDER BY `id_order` DESC
            LIMIT '.(int)$limit;

        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        // Petit helper pour échapper l’affichage HTML
        $e = function ($v) {
            return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
        };

        // On fabrique la page HTML directement (pour aller vite)
        // Pour un rendu plus "Presta", vous pouvez basculer sur un .tpl Smarty.
        header('Content-Type: text/html; charset=utf-8');

        echo '<!DOCTYPE html><html lang="fr"><head>';
        echo '<meta charset="utf-8">';
        echo '<meta name="robots" content="noindex,nofollow">';
        echo '<title>Liste des notes filtrées</title>';
        // Un peu de style minimal
        echo '<style>
                body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:20px;}
                h1{margin-top:0}
                form{margin-bottom:16px;display:flex;gap:8px;align-items:center;flex-wrap:wrap}
                input[type=number]{padding:6px 10px;border:1px solid #ccc;border-radius:6px;width:140px}
                input[type=submit],button{padding:8px 12px;border:1px solid #0a7;border-radius:6px;background:#0a7;color:#fff;cursor:pointer}
                input[type=submit]:hover{background:#086}
                table{border-collapse:collapse;width:100%;margin-top:10px}
                th,td{border:1px solid #e5e5e5;padding:8px;vertical-align:top;font-size:14px}
                th{background:#f7f7f7}
                .meta{color:#666;font-size:12px;margin-bottom:8px}
              </style>';
        echo '</head><body>';

        echo '<h1>Notes « Rectangle » (hors « Arrondi »)</h1>';
        echo '<div class="meta">Seuil courant : <strong>'.$e($minId).'</strong> — Limite : '.$e($limit).' lignes</div>';

        // Formulaire GET
        $self = $e($this->context->link->getModuleLink('idxrcustomproduct', 'file'));
        echo '<form method="get" action="'.$self.'">
                <label for="min_id">ID commande minimal &gt;</label>
                <input type="number" id="min_id" name="min_id" value="'.$e($minId).'" min="0" step="1">
                <label for="limit">Limite</label>
                <input type="number" id="limit" name="limit" value="'.$e($limit).'" min="1" max="1000" step="1">
                <input type="submit" value="Lister">
                <a href="'.$self.'"><button type="button" title="Réinitialiser">Réinitialiser</button></a>
              </form>';

        // Rendu du tableau
        if (!$rows) {
            echo '<p>Aucun enregistrement trouvé pour ce filtre.</p>';
        } else {
            // Entêtes dynamiques depuis les clés du premier résultat
            $headers = array_keys($rows[0]);
            echo '<table><thead><tr>';
            foreach ($headers as $h) {
                echo '<th>'.$e($h).'</th>';
            }
            echo '</tr></thead><tbody>';

            foreach ($rows as $r) {
                echo '<tr>';
                foreach ($headers as $h) {
                    $val = isset($r[$h]) ? $r[$h] : '';
                    // Affichage compact : tronquer les très longs textes
                    $text = (string)$val;
                    if (mb_strlen($text, 'UTF-8') > 400) {
                        $text = mb_substr($text, 0, 400, 'UTF-8').'…';
                    }
                    echo '<td>'.$e($text).'</td>';
                }
                echo '</tr>';
            }
            echo '</tbody></table>';
        }

        echo '</body></html>';
        exit; // on coupe le cycle rendu Presta, on a tout renvoyé
    }
}
