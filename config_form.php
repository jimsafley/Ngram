<section class="seven columns alpha">
    <div class="field">
        <div class="two column alpha">
            <label for="text_element_id"><?php echo __('Text Element'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo $view->formSelect(
                'text_element_id',
                get_option('ngram_text_element_id'),
                array('id' => 'text_element_id'),
                $elementOptions
            ) ?>
            <p class="explanation"><?php echo __('Select an element that contains the text from which to derive ngrams.'); ?></p>
        </div>
    </div>
</section>
