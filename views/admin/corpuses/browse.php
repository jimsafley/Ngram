<?php
$pageTitle = __('Browse Corpora');
echo head(array('title' => $pageTitle, 'bodyclass' => 'browse'));
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
        <th>Valid Items</th>
        <th>Invalid Items</th>
        <th></th>
    </tr>
</thead>
<tbody>
<?php foreach (loop('ngram_corpus') as $corpus): ?>
    <tr>
        <td><?php echo link_to($corpus, 'show', $corpus->name);?></td>
        <td><?php echo $corpus->getTextElement()->name; ?></td>
        <td><?php echo $corpus->getSequenceMemberElementId()->name; ?></td>
        <td><?php echo count($corpus->getValidItems()); ?></td>
        <td><?php echo count($corpus->getInvalidItems()); ?></td>
        <td>
            <button>Validate Items</button>
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
