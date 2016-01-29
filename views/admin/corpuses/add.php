<?php
echo head(array('title' => 'Add Corpus', 'bodyclass' => 'add'));
echo flash();
?>
<form method="post">
<section class="seven columns alpha">
    <div class="field">
        <div class="two columns alpha">
            <label for="name" class="required">Name</label>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formText('name', $corpus->name); ?>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="query">Query</label>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formText('query', $corpus->query); ?>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="sequence_element_id" class="required">Sequence Element</label>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formSelect(
                'sequence_element_id',
                $corpus->sequence_element_id,
                array('id' => 'sequence_element_id'),
                $elementOptions
            ); ?>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="sequence_type" class="required">Sequence Type</label>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formSelect(
                'sequence_type',
                $corpus->sequence_type,
                array('id' => 'sequence_type'),
                array(
                    '' => 'Select Below',
                    'year' => 'Date by year',
                    'month' => 'Date by month',
                    'day' => 'Date by day',
                )
            ); ?>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="sequence_range">Sequence Range</label>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formText('sequence_range', $corpus->sequence_range); ?>
        </div>
    </div>
</section>
<section class="three columns omega">
    <div id="save" class="panel">
        <input type="submit" name="submit" id="submit" value="Save Corpus" class="submit big green button">
    </div>
</section>
</form>

<?php echo foot(); ?>
