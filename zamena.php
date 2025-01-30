<?php
// Načtení základní konfigurace a inicializace systému
include(dirname(__FILE__).'/config/config.inc.php');
include(dirname(__FILE__).'/init.php');

// Definice proměnných
$id_lang = 1; // Jazyk, ve kterém se provádí změny
$search_string = 'všeobecně'; // Hledaný řetězec bez rozdílu velké/malé písmeno
$replace_string = 'General'; // Nový řetězec, kterým bude nahrazen
$table = _DB_PREFIX_ . 'product_lang'; // Tabulka obsahující měněný název
$column = 'name'; // Sloupec obsahující názvy

try {
    $db = Db::getInstance();

    // Výběr všech záznamů obsahujících hledaný řetězec
    $sql_select = "SELECT `$column` FROM `$table` WHERE LOWER(`$column`) LIKE LOWER('%" . pSQL($search_string) . "%') AND `id_lang` = " . (int)$id_lang;
    $results = $db->executeS($sql_select);

    // Počet výskytů hledaného řetězce v názvech
    $total_occurrences = array_sum(array_map(fn($row) => substr_count(strtolower($row[$column]), strtolower($search_string)), $results));

    // Aktualizace názvů nahrazením hledaného řetězce
    $sql_update = "UPDATE `$table` SET `$column` = REPLACE(LOWER(`$column`), LOWER('" . pSQL($search_string) . "'), '" . pSQL($replace_string) . "') WHERE `id_lang` = " . (int)$id_lang;
    $db->execute($sql_update);

    // Výstup počtu upravených výskytů
    echo "Počet upravených slov: " . $total_occurrences . "\n";

} catch (Exception $e) {
    // Zpracování chyby a výstup chybové zprávy
    echo "Chyba: " . $e->getMessage();
}
?>
