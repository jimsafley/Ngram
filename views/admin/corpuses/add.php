<?php
$pageTitle = __('Add Corpus');
echo head(array('title' => $pageTitle, 'bodyclass' => 'add'));
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
            <label for="sequence_member_element_id" class="required">Sequence Element</label>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formSelect(
                'sequence_member_element_id',
                $corpus->sequence_member_element_id,
                array('id' => 'sequence_member_element_id'),
                $elementOptions
            ); ?>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="sequence_member_pattern" class="required">Sequence Pattern</label>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formSelect(
                'sequence_member_pattern',
                $corpus->sequence_member_pattern,
                array('id' => 'sequence_member_pattern'),
                array(
                    '' => 'Select Below',
                    '\d+' => '1 – n',
                    '\d{4}' => 'YYYY',
                    '\d{4}[1-]' => 'YYYYMM',
                    '\d{8}' => 'YYYYMMDD',
                )
            ); ?>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="sequence_member_range">Sequence Range</label>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formText('sequence_member_range', $corpus->sequence_member_range); ?>
        </div>
    </div>
</section>
<section class="three columns omega">
    <div id="save" class="panel">
        <input type="submit" name="submit" id="submit" value="Save Corpus" class="submit big green button">
    </div>
</section>
</form>
