<?php
class Ngram_CorpusesController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('NgramCorpus');
    }

    public function addAction()
    {
        $db = $this->_helper->db;
        if ($this->getRequest()->isPost()) {
            $this->getRequest()->setPost('text_element_id', get_option('ngram_text_element_id'));
        }
        $this->view->elementOptions = $db->getTable('NgramCorpus')->getElementsForSelect();
        parent::addAction();
        $this->view->corpus = $this->view->ngram_corpu; // correct poor inflection
    }

    public function validateItemsAction()
    {
        $table = $this->_helper->db;

        $corpus = $table->findById();
        $sequenceElement = $corpus->getSequenceElement();
        $textElement = $corpus->getTextElement();

        // Find items by corpus query, if any.
        $query = $corpus->query ? parse_str($corpus->query) : array();
        // Items must be described by the corpus sequence element.
        $query['advanced'][] = array(
            'element_id' => $sequenceElement->id,
            'type' => 'is not empty',
        );
        // Items must be described by the corpus text element.
        $query['advanced'][] = array(
            'element_id' => $textElement->id,
            'type' => 'is not empty',
        );
        $items = $table->getTable('Item')->findBy($query);

        $corpusItemIds = array();
        foreach ($items as $item) {
            $corpusItemIds[] = $item->id;
        }

        $db = $table->getDb();
        $sequenceElementId = $db->quote($sequenceElement->id);
        $corpusItemIds = $db->quote($corpusItemIds);
        $sql = <<<SQL
SELECT i.id, et.text
FROM {$db->Item} i
JOIN {$db->ElementText} et
ON i.id = et.record_id
WHERE i.id IN ($corpusItemIds)
AND et.element_id = $sequenceElementId
GROUP BY i.id
SQL;

        $items = $db->fetchAll($sql);
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
