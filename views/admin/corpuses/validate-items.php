<?php echo head(array('title' => 'Validate Corpus Items')); ?>

<h2><?php echo $corpus->name; ?></h2>

<h3>Invalid Items</h3>

<table>
<thead>
    <tr>
        <th>Item</th>
        <th>Sequence Text</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($invalidItems as $item): ?>
    <tr>
        <td><a href="<?php echo url(array('controller' => 'items', 'action' => 'edit', 'id' => $item['id']), 'id'); ?>"><?php echo $item['id']; ?></a></td>
        <td><?php echo $item['sequence_text']; ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>

<h3>Valid Items</h3>

<table>
<thead>
    <tr>
        <th>Item</th>
        <th>Sequence Text</th>
        <th>Sequence Member</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($validItems as $item): ?>
    <tr>
        <td><a href="<?php echo url(array('controller' => 'items', 'action' => 'edit', 'id' => $item['id']), 'id'); ?>"><?php echo $item['id']; ?></a></td>
        <td><?php echo $item['sequence_text']; ?></td>
        <td><kbd><?php echo $item['sequence_member']; ?></kbd></td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>

<form method="post" action="<?php echo html_escape(url('ngram/corpuses/validate-items')); ?>">
    <?php echo $this->formHidden('id', $corpus->id); ?>
    <?php echo $this->formSubmit('accept_items', 'Accept Valid Items'); ?>
</form>

<?php echo foot(); ?>
