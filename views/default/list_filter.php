<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<div class="search-filters">
    <?php $this->beginWidget('CActiveForm', array(
        'id' => 'cvi-filter',
        'htmlOptions' => array(
            'class' => 'row',
        ),
        'enableAjaxValidation' => false,
    ))?>

    <div class="large-12 column">
        <div class="panel box js-toggle-container">
            <h3>Filter CVIs</h3>
            <a href="#" class="toggle-trigger toggle-hide js-toggle">
                    <span class="icon-showhide">
                        Show/hide this section
                    </span>
            </a>
            <div class=" js-toggle-body">
                <div class="row">

                    <div class="column large-1 text-right"><label for="date_from">From:</label></div>
                    <div class="column large-2"><input type="text" id="date_from" name="date_from" class="datepicker filter-field" value="<?=$this->request->getPost('date_from', '')?>" /></div>
                    <div class="column large-1 text-right"><label for="date_to">To:</label></div>
                    <div class="column large-2"><input type="text" id="date_to" name="date_to" class="datepicker filter-field" value="<?=$this->request->getPost('date_to', '')?>" /></div>
                    <div class="column large-1 text-right"><label for="consultants">Consultant(s):</label></div>
                    <div class="column large-2"><?php
                        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                            'id' => 'consultant_auto_complete',
                            'name' => 'consultant_auto_complete',
                            'value' => '',
                            'source' => "js:function(request, response) {
                                    var existing = [];
                                    $('#consultant_list').children('li').map(function() {
                                        existing.push(String($(this).data('id')));
                                    });

                                    $.ajax({
                                        'url': '".Yii::app()->createUrl('user/autocomplete')."',
                                        'type':'GET',
                                        'data':{'term': request.term},
                                        'success':function(data) {
                                            data = $.parseJSON(data);

                                            var result = [];

                                            for (var i = 0; i < data.length; i++) {
                                                var index = $.inArray(data[i].id, existing);
                                                if (index == -1) {
                                                    result.push(data[i]);
                                                }
                                            }

                                            response(result);
                                        }
                                    });
                                    }",
                            'options' => array(
                                'minLength' => '3',
                                'select' => "js:function(event, ui) {
                                    addConsultantToList(ui.item);
                                    $('#consultant_auto_complete').val('');
                                    return false;
                                }",
                            ),
                            'htmlOptions' => array(
                                'placeholder' => 'type to search for users',
                            ),
                        ));
                        ?>
                        <div><ul id="consultant_list" class="multi-select-search scroll">
                                <?php $consultant_ids = $this->request->getPost('consultant_ids', '');
                                if ($consultant_ids) {
                                    foreach(explode(',', $consultant_ids) as $id) {
                                        if ($user = User::model()->findByPk($id)) { ?>
                                            <li data-id="<?=$id?>"><?= $user->getReversedFullname() ?><a href="#" class="remove">X</a></li>
                                        <?php }
                                    }
                                }?>
                            </ul></div>
                        <?= CHtml::hiddenField('consultant_ids', $this->request->getPost('consultant_ids', ''), array('class' => 'filter-field')); ?>
                    </div>
                    <div class="column large-2 text-right"><label for="show-issued">Show Issued:</label></div>
                    <div class="column large-1 end"><?php echo CHtml::checkBox('show_issued', ($this->request->getPost('show_issued', '') == 1), array('class' => 'filter-field'))?></div>
                </div>
                <div class="row">
                    <div class="column large-12 text-right end">
                        <?php if ($this->isListFiltered()) { ?>
                            <button id="reset_button" class="warning small" type="submit">Reset</button>
                        <?php } ?>
                        <button id="search_button" class="secondary small" type="submit" disabled="true">Apply</button></div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->endWidget()?>
</div>

<script type="text/javascript">
    function addConsultantToList(consultant)
    {
        var currentIds = $('#consultant_ids').val() ? $('#consultant_ids').val().split(',') : [];
        currentIds.push(consultant.id);
        $('#consultant_ids').val(currentIds.join());

        $('#consultant_list').append('<li data-id="'+ consultant.id +'">' + consultant.value +'<a href="#" class="remove">X</a></li>');
        $('#consultant_list').scrollTop($('#consultant_list')[0].scrollHeight);
    }

    $('#consultant_list').on('click', '.remove', function(e) {
        var li = $(e.target).parents('li');
        var consultantId = li.data('id');
        var ids = $('#consultant_ids').val() ? $('#consultant_ids').val().split(',') : [];
        var newIds = [];
        for (var i in ids) {
            if (String(ids[i]) != consultantId) {
                newIds.push(ids[i]);
            }
        }
        $('#consultant_ids').val(newIds.join());
        $(li).remove();
    });

    $('#reset_button').on('click', function(e) {
        e.preventDefault();
        window.location.href = document.URL;
    });

    $('#cvi-filter').on('change', '.filter-field', function() {
        $('#search_button').removeAttr('disabled');
    });

    $(document).ready(function() {
        $('.datepicker').datepicker({'showAnim':'fold','dateFormat':'d M yy'});
    });
</script>