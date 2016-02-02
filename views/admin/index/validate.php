<?php
echo head(array('title' => 'Validate Corpus Items'));
$validCount = count($validItems);
$invalidCount = count($invalidItems);
?>

<h2><?php echo $corpus->name; ?> (<?php echo $validCount + $invalidCount ?> total items)</h2>

<h3>Sequence Type: <?php echo $corpus->getSequenceTypeLabel(); ?></h4>

<h4>Invalid Items (<?php echo $invalidCount; ?>)</h4>

<?php if ($invalidCount): ?>
<table>
<thead>
    <tr>
        <th>Item</th>
        <th>Sequence Text</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($invalidItems as $id => $sequenceText): ?>
    <tr>
        <td><a href="<?php echo url(array('controller' => 'items', 'action' => 'edit', 'id' => $id), 'id'); ?>"><?php echo $id; ?></a></td>
        <td><?php echo $sequenceText; ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<p>There are no invalid items.</p>
<?php endif; ?>

<h4>Valid Items (<?php echo $validCount; ?>)</h4>

<?php if ($validCount): ?>
<table>
<thead>
    <tr>
        <th>Item</th>
        <th>Sequence Text</th>
        <th>Sequence Member</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($validItems as $id => $item): ?>
    <tr>
        <td><a href="<?php echo url(array('controller' => 'items', 'action' => 'edit', 'id' => $id), 'id'); ?>"><?php echo $id; ?></a></td>
        <td><?php echo $item['text']; ?></td>
        <td><kbd><?php echo $item['member']; ?></kbd></td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>
<form method="post" action="<?php echo html_escape(url('ngram/corpora/validate/' . $corpus->id)); ?>">
    <?php echo $this->formSubmit('accept_items', 'Accept Valid Items'); ?>
</form>
<?php else: ?>
<p>There are no valid items.</p>
<?php endif; ?>

<?php echo foot(); ?>
