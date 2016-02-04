<?php
class Process_GenerateItemNgrams extends Omeka_Job_Process_AbstractProcess
{
    protected $_corpusNgrams = array();

    public function run($args)
    {
        $db = get_db();
        $corpus = $db->getTable('NgramCorpus')->find($args['corpus_id']);
        $textElementId = get_option('ngram_text_element_id');
        $n = $args['n'];

        $selectItemSql = sprintf('
        SELECT ng.id
        FROM %s ing
        JOIN %s ng
        ON ing.ngram_id = ng.id
        WHERE ing.item_id = ? 
        AND ng.n = %s',
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
                $ngramIds = $db->query($selectItemSql, $itemId)
                    ->fetchAll(Zend_Db::FETCH_COLUMN, 0);
                if ($ngramIds) {
                    foreach ($ngramIds as $ngramId) {
                        $this->_addCorpusNgram($itemId, $ngramId, $sequenceMember);
                    }
                    continue;
                }

                // Get the item text.
                $stmt = $db->query($selectTextSql, $itemId);
                $text = new Ngram_Text($stmt->fetchColumn(0));

                // Iterate item ngrams.
                foreach ($text->getNgrams($n) as $ngram) {
                    $db->query($ngramsSql, array($ngram, $n));
                    $ngramId = $db->lastInsertId();
                    $db->query($itemNgramsSql, array($ngramId, $itemId));
                    $this->_addCorpusNgram($itemId, $ngramId, $sequenceMember);
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $corpusNgramsSql = sprintf('
        INSERT IGNORE INTO %s (
            corpus_id, ngram_id, sequence_member, match_count, item_count, relative_frequency
        ) VALUES (
            %s, ?, ?, ?, ?, ?
        )',
        $db->NgramCorpusNgram,
        $corpus->id);

        $db->beginTransaction();
        try {
            $corpusSequenceMembers = [];
            foreach ($this->_corpusNgrams as $ngramId => $sequenceMembers) {
                foreach ($sequenceMembers as $sequenceMember => $itemIds) {
                    if (!isset($corpusSequenceMembers[$sequenceMember])) {
                        $corpusSequenceMembers[$sequenceMember] = [];
                    }
                    // This is the match count, i.e. the number of instances of the
                    // ngram for the specified sequence member.
                    $corpusSequenceMembers[$sequenceMember][$ngramId] = count($itemIds);
                }
            }

            foreach ($corpusSequenceMembers as $sequenceMember => $ngramIds) {
                // This is the total number of ngrams for the sequence member.
                $totalCount = array_sum($ngramIds);
                foreach ($ngramIds as $ngramId => $matchCount) {
                    $items = $this->_corpusNgrams[$ngramId][$sequenceMember];
                    $db->query($corpusNgramsSql, array(
                        $ngramId,
                        $sequenceMember,
                        $matchCount,
                        count(array_unique($items)),
                        $matchCount / $totalCount,
                    ));
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        var_dump(memory_get_peak_usage());
    }

    protected function _addCorpusNgram($itemId, $ngramId, $sequenceMember)
    {
        if (!isset($this->_corpusNgrams[$ngramId])) {
            $this->_corpusNgrams[$ngramId] = [];
        }
        if (!isset($this->_corpusNgrams[$ngramId][$sequenceMember])) {
            $this->_corpusNgrams[$ngramId][$sequenceMember] = [];
        }
        $this->_corpusNgrams[$ngramId][$sequenceMember][] = $itemId;
    }
}
