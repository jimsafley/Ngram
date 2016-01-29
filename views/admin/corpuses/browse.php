<?php
echo head(array('title' => 'Browse Corpora', 'bodyclass' => 'browse'));
echo flash();
?>

<?php if ($total_results): ?>

<div class="table-actions">
    <a href="<?php echo html_escape(url('ngram/corpuses/add')); ?>" class="small green button">Add a Corpus</a>
</div>

<table>
<thead>
    <tr>
        <th>Name</th>
        <th>Text Element</th>
        <th>Sequence Element</th>
        <th>Sequence Type</th>
        <th>Sequence Range</th>
        <th></th>
    </tr>
</thead>
<tbody>
<?php foreach (loop('ngram_corpus') as $corpus): ?>
    <tr>
        <td><?php echo link_to($corpus, 'show', $corpus->name);?></td>
        <td><?php echo $corpus->getTextElement()->name; ?></td>
        <td><?php echo $corpus->getSequenceElement()->name; ?></td>
        <td><?php echo $corpus->sequence_type; ?></td>
        <td><?php echo $corpus->sequence_range; ?></td>
        <td>
            <form method="post" action="<?php echo html_escape(url('ngram/corpuses/validate-items')); ?>">
                <?php echo $this->formHidden('id', $corpus->id); ?>
                <?php echo $this->formSubmit('validate_items', 'Validate Items'); ?>
            </form>
            <?php if ($corpus->canGenerateCorpusNgrams()): ?>
            <button>Generate Corpus Ngrams</button>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>

<h2>You have no corpora.</h2>
<p>Get started by adding your first corpus.</p>
<a href="<?php echo html_escape(url('ngram/corpuses/add')); ?>" class="add big green button">Add a Corpus</a>
<?php endif; ?>

<?php echo foot(); ?>
