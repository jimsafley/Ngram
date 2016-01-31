<?php
echo head(array('title' => 'Browse Corpora', 'bodyclass' => 'browse'));
echo flash();
?>

<?php if ($total_results): ?>

<div class="table-actions">
    <a href="<?php echo html_escape(url('ngram/corpora/add')); ?>" class="small green button">Add a Corpus</a>
</div>

<table>
<thead>
    <tr>
        <th>Name</th>
        <th>Sequence Element</th>
        <th>Sequence Type</th>
        <th>Sequence Range</th>
        <th>Items</th>
        <th></th>
    </tr>
</thead>
<tbody>
<?php foreach (loop('ngram_corpus') as $corpus): ?>
<?php
$sequenceElement = $corpus->SequenceElement;
$sequenceElementName = $sequenceElement->name;
$sequenceElementSetName = $sequenceElement->getElementSet()->name;
?>
    <tr>
        <td><?php echo $corpus->name;?></td>
        <td><?php echo sprintf('%s<br>(%s)', $sequenceElementName, $sequenceElementSetName); ?></td>
        <td><?php echo $corpus->SequenceType->getLabel(); ?></td>
        <td><?php echo $corpus->sequence_range; ?></td>
        <td><?php echo count($corpus->Items); ?></td>
        <td>
            <?php if ($corpus->canValidateItems()): ?>
            <a href="<?php echo url('ngram/corpora/validate/' . $corpus->id); ?>" class="small green button">Validate Items</a>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>

<h2>You have no corpora.</h2>
<p>Get started by adding your first corpus.</p>
<a href="<?php echo html_escape(url('ngram/corpora/add')); ?>" class="add big green button">Add a Corpus</a>
<?php endif; ?>

<?php echo foot(); ?>
