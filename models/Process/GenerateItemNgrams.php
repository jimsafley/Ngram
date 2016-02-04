<?php
class Process_GenerateItemNgrams extends Omeka_Job_Process_AbstractProcess
{
    public function run($args)
    {
        $db = get_db();
        $corpus = $db->getTable('NgramCorpus')->find($args['corpus_id']);
        $textElementId = get_option('ngram_text_element_id');
        $n = $args['n'];

        $selectItemSql = sprintf('
        SELECT 1
        FROM %s ing
        JOIN %s ng
        ON ing.ngram_id = ng.id
        WHERE ing.item_id = ? 
        AND ng.n = %s
        GROUP BY ing.item_id',
        $db->NgramItemNgram,
        $db->NgramNgram,
        $db->quote($n, Zend_Db::INT_TYPE));

        $selectTextSql = sprintf('
        SELECT et.text
        FROM %s i
        JOIN %s et
        ON i.id = et.record_id
        WHERE et.record_id = ?
        AND et.element_id = %s',
        $db->Item,
        $db->ElementText,
        $db->quote($textElementId, Zend_Db::INT_TYPE));

        $ngramsSql = sprintf('
        INSERT INTO %s (ngram, n) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)',
        $db->NgramNgram);

        $itemNgramsSql = sprintf('
        INSERT INTO %s (ngram_id, item_id) VALUES (?, ?)',
        $db->NgramItemNgram);

        $db->beginTransaction();
        try {
            // Iterate corpus items.
            foreach ($corpus->ItemsCorpus as $itemId => $sequenceMember) {

                // Do not re-generate item ngrams for the current n.
                $stmt = $db->query($selectItemSql, $itemId);
                if ($stmt->fetchColumn(0)) {
                    continue;
                }

                // Get the item text.
                $stmt = $db->query($selectTextSql, $itemId);
                $text = new Ngram_Text($stmt->fetchColumn(0));

                // Iterate item ngrams.
                foreach ($text->getNgrams($n) as $ngram) {
                    $db->query($ngramsSql, array($ngram, $n));
                    $db->query($itemNgramsSql, array($db->lastInsertId(), $itemId));
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
