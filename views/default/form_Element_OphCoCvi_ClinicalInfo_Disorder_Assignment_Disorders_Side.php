<?php
    foreach ($disorder_section->disorders as $disorder) {
        $field_base_name = CHtml::modelName($element) . "[{$side}_disorders][{$disorder->id}]";
    ?>
    <fieldset class="row field-row">
        <div class="large-6 column">
            <label><?php echo $disorder->name; ?></label>
        </div>
        <div class="large-6 column">
            <label class="inline highlight">
                <?php
                echo CHtml::radioButton($field_base_name . "[affected]", $element->hasCviDisorderForSide($disorder, $side), array('id' => $field_base_name . '_affected_1', 'value' => 1))?>
                Yes
            </label>
            <label class="inline highlight">
                <?php echo CHtml::radioButton($field_base_name . "[affected]", !$element->hasCviDisorderForSide($disorder, $side), array('id' => $field_base_name . '_affected_0', 'value' => 0))?>
                No
            </label>
            <label class="inline"><?php echo CHtml::checkBox($field_base_name . "[main_cause]", $element->isCviDisorderMainCauseForSide($disorder, $side));?>
                Main cause
            </label>

        </div>
    </fieldset>
<?php } ?>