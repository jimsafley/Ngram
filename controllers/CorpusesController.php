<?php
class Ngram_CorpusesController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('NgramCorpus');
    }

    public function addAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->getRequest()->setPost('text_element_id', get_option('ngram_text_element_id'));
        }
        parent::addAction();

        $table = $this->_helper->db;
        $this->view->elementOptions = $table->getTable('NgramCorpus')->getElementsForSelect();
        $this->view->corpus = $this->view->ngram_corpu; // correct poor inflection
    }

    /**
     * Set items in corpus.
     *
     * @param Omeka_Record_AbstractRecord $corpus
     */
    protected function _redirectAfterAdd($corpus)
    {
        parse_str($corpus->query, $query);
        // Items must be described by the corpus sequence element.
        $query['advanced'][] = array(
            'element_id' => $corpus->sequence_element_id,
            'type' => 'is not empty',
        );
        // Items must be described by the corpus text element.
        $query['advanced'][] = array(
            'element_id' => $corpus->text_element_id,
            'type' => 'is not empty',
        );

        $table = $this->_helper->db;
        $items = $table->getTable('Item')->findBy($query);
        $itemIds = array();
        foreach ($items as $item) {
            $itemIds[] = $item->id;
        }

        $corpus->items = json_encode($itemIds);
        $corpus->save(false);

        parent::_redirectAfterAdd($corpus);
    }

    public function validateItemsAction()
    {
        $table = $this->_helper->db;
        $db = $table->getDb();
        $corpus = $table->find($this->getParam('id'));

        // Query the database directly to get sequence element text. This
        // reduces the overhead that would otherwise be required to cache all
        // element texts.

        $sql = sprintf(
        'SELECT i.id, et.text
        FROM %s i JOIN %s et
        ON i.id = et.record_id
        WHERE i.id IN (%s)
        AND et.element_id = %s
        GROUP BY i.id',
        $db->Item,
        $db->ElementText,
        $db->quote(json_decode($corpus->items, true)),
        $db->quote($corpus->sequence_element_id));
        $items = $db->fetchAll($sql);

        // Validate the sequence text.
        $validItems = array();
        $invalidItems = array();
        foreach ($items as $item) {
            $text = trim($item['text']);
            // Validate "Date by year"
            if ('year' === $corpus->sequence_type) {
                if (preg_match('/^\d{4}$/', $text)) {
                    $validItems[] = array(
                        'id' => $item['id'],
                        'sequence_text' => $text,
                        'sequence_member' => $text,
                    );
                } else {
                    $timestamp = strtotime($text);
                    if ($timestamp) {
                        $validItems[] = array(
                            'id' => $item['id'],
                            'sequence_text' => $text,
                            'sequence_member' => date('Y', $timestamp),
                        );
                    } else {
                        $invalidItems[] = array(
                            'id' => $item['id'],
                            'sequence_text' => $text,
                        );
                    }
                }
            // Validate "Date by month"
            } elseif ('month' === $corpus->sequence_type) {
                if (preg_match('/^\d{4}$/', $text)) {
                    $invalidItems[] = array(
                        'id' => $item['id'],
                        'sequence_text' => $text,
                    );
                } else {
                    $timestamp = strtotime($text);
                    if ($timestamp) {
                        $validItems[] = array(
                            'id' => $item['id'],
                            'sequence_text' => $text,
                            'sequence_member' => date('Ym', $timestamp),
                        );
                    } else {
                        $invalidItems[] = array(
                            'id' => $item['id'],
                            'sequence_text' => $text,
                        );
                    }
                }
            // Validate "Date by day"
            } elseif ('day' === $corpus->sequence_type) {
                if (preg_match('/^\d{4}$/', $text)) {
                    $invalidItems[] = array(
                        'id' => $item['id'],
                        'sequence_text' => $text,
                    );
                } elseif (preg_match('/^[a-z]+ \d+$/i', $text)) {
                    $invalidItems[] = array(
                        'id' => $item['id'],
                        'sequence_text' => $text,
                    );
                } elseif (preg_match('/^\d+-\d+$/i', $text)) {
                    $invalidItems[] = array(
                        'id' => $item['id'],
                        'sequence_text' => $text,
                    );
                } else {
                    $timestamp = strtotime($text);
                    if ($timestamp) {
                        $validItems[] = array(
                            'id' => $item['id'],
                            'sequence_text' => $text,
                            'sequence_member' => date('Ymd', $timestamp),
                        );
                    } else {
                        $invalidItems[] = array(
                            'id' => $item['id'],
                            'sequence_text' => $text,
                        );
                    }
                }
            }
        }

        // Sort the valid and invalid item arrays.
        usort($validItems, function($a, $b) {
            if ($a['sequence_member'] === $b['sequence_member']) {
                return 0;
            }
            return $a['sequence_member'] < $b['sequence_member'] ? -1 : 1;
        });
        usort($invalidItems, function($a, $b) {
            return strcmp($a['sequence_text'], $b['sequence_text']);
        });

        $this->view->corpus = $corpus;
        $this->view->validItems = $validItems;
        $this->view->invalidItems = $invalidItems;

        //~ var_dump(memory_get_peak_usage());exit;
    }

    protected function _getAddSuccessMessage($record)
    {
        return sprintf('The "%s" corpus was sucessfully added.', $record->name);
    }
}
