<style>
.rate-container {
    padding-bottom: 50px;
}

table {
    background-color: #810000 !important;
    border-radius: 15px !important;
    padding-bottom: 50px !important;
}

table,
td,
th {
    border: none !important;
    padding: 20px 50px;
}

td {
    font-size: 20px;
    background-color: #1B1717 !important;
}

th {
    color: white !important;
    background-color: rgba(0, 0, 0, 0.0) !important;
    font-size: 20px;
}

tr:not(:last-child) {
    border-spacing: 5em;
}

td:not(.right) {
    padding-right: 20px !important;
    border-radius: 20px 0px 0px 20px !important;
}

td.right {
    width: 20%;
    padding-right: 20px !important;
    border-radius: 0px 20px 20px 0px !important;
}

.name-ac,
.input-ac {
    background-color: white !important;
    border-radius: 20px !important;
    padding: 20px;
    width: 100%;
}

.ui.input input {
    text-align: right !important;
}

.ui.green.button {
    font-size: 20px;
}
</style>

<div class="rate-container mx-5">

    <div class="div">

        <?php if($_SESSION['user']->role != 'admin'){ ?>

        <select class="ui dropdown type_id">
            <option value="ALL" <?php if($type_id == "ALL") {echo "selected";}; ?>>ALL</option>
            <?php foreach ($activity_types as $activity_type){ ?>
            <?php if($activity_type->type_name != 'Admin'){ ?>
            <option value="<?php echo $activity_type->_id ?>"
                <?php if($type_id == $activity_type->type_name) {echo "selected";}; ?>>
                <?php echo $activity_type->type_name ?>
            </option>
            <?php } ?>
            <?php } ?>
        </select>

        <?php }else{ ?>

        <select class="ui dropdown type_id">
            <option value="62342aa328e2c98b0115edd0" selected>Admin</option>
        </select>

        <?php } ?>

        <select class="ui dropdown day">
            <?php for($i = 7; $i <= 14; $i++){ ?>
            <?php if($i < 10){ ?>
            <option value="2022-04-0<?php echo $i ?>"
                <?php if(isset($day) && $day == "2022-04-0".$i) {echo "selected";}; ?>>
                วันที่ <?php echo $i ?> เมษายน 2565
            </option>
            <?php }else{ ?>
            <option value="2022-04-<?php echo $i ?>"
                <?php if(isset($day) && $day == "2022-04-".$i) {echo "selected";}; ?>>
                วันที่ <?php echo $i ?> เมษายน 2565
            </option>
            <?php } ?>

            <?php } ?>
        </select>

        <button class="ui green button float-end" onclick="save_assess()"> บันทึก </button>
    </div>

    <?php $count = 0 ?>

    <div class="rate-form mt-5">
        <table class="ui celled table">
            <thead>
                <tr>
                    <th class="center aligned">ชื่อ Activities</th>
                    <th class="center aligned">คะแนน ($SE)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity) {?>
                <tr id="ac_<?php echo $activity->_id ?>">
                    <td class="center aligned">
                        <div class="name-ac">
                            <?php echo $activity->ac_name ?>
                            <input type="text" hidden="true" name="ac_id" id="ac_id"
                                value="<?php echo $activity->_id ?>">
                            <input type="text" hidden="true" name="type" id="type"
                                value="<?php echo $activity->type ?>">
                        </div>
                    </td>
                    <td class="right aligned">
                        <div class="input-ac">
                            <div class="ui transparent input" style="width: 100%">
                                <input type="number" placeholder="/<?php echo $activity->max_score ?>"
                                    value="<?php if(isset($activity->score)) echo $activity->score ?>"
                                    class="score_<?php echo $count ?>" id="score" name="score" min="0"
                                    max="<?php echo $activity->max_score ?>"
                                    onkeyup="check_number(<?php echo $count ?>,<?php echo $activity->max_score ?>)">
                            </div>
                        </div>
                    </td>
                </tr>
                <?php $count++; } ?>
            </tbody>
        </table>
        <?php if($count == 0){ ?>
        <div class="ui message text-center">
            <div class="header">
                ไม่มีกิจกรรมในระบบ
            </div>
            <p></p>
        </div>
        <style>
        table {
            display: none;
        }
        </style>
        <?php } ?>
    </div>

</div>

<script>
$(document).ready(function() {
    get_score();
    console.log('eieiei');
});

function get_score() {
    var user_id = '<?php echo $_SESSION['user']->_id; ?>';
    var date = '<?php echo $day; ?>';

    for (var i = 0; i < $('tbody tr').length; i++) {
        var ac_id = $('input[name=ac_id]:eq(' + i + ')').val();

        $.ajax({
            url: '<?php echo base_url() . 'index.php/C_Assess/get_score' ?>',
            method: 'POST',
            dataType: 'JSON',
            async: false,
            data: {
                user_id: user_id,
                ac_id: ac_id,
                date: date
            },
            success: function(data) {
                var placeholder = $('input[name=score]:eq(' + i + ')').attr('placeholder');
                if (data.score == 0)
                    $('input[name=score]:eq(' + i + ')').attr('placeholder', '0' + placeholder);
                else
                    $('input[name=score]:eq(' + i + ')').attr('placeholder', data.score + placeholder);
            }
        });
    }
}

$('.type_id, .day').on('change', function() {
    var type_id = $('.type_id').val();
    var day = $('.day').val();
    window.location.href = '<?php echo base_url(); ?>' + 'index.php/C_Assess/show_assess/' + type_id + '/' +
        day;
});

function check_number(index, max_score) {
    if ($('.score_' + index).val() < 0)
        $('.score_' + index).val(0);
    else if ($('.score_' + index).val() > max_score)
        $('.score_' + index).val(max_score);
}

function save_assess() {

    var team = <?php echo $_SESSION['user']->team; ?>;
    var user_id = '<?php echo $_SESSION['user']->_id; ?>';
    var date = '<?php echo $day; ?>';

    for (var i = 0; i < $('tbody tr').length; i++) {
        var ac_id = $('input[name=ac_id]:eq(' + i + ')').val();
        var score = $('input[name=score]:eq(' + i + ')').val();
        var type = $('input[name=type]:eq(' + i + ')').val();

        $.ajax({
            url: '<?php echo base_url() . 'index.php/C_Assess/insert_assess' ?>',
            method: 'POST',
            dataType: 'JSON',
            async: true,
            data: {
                team: team,
                date: date,
                ac_id: ac_id,
                user_id: user_id,
                type: type,
                score: score
            },
            success: function(data) {
                console.log(data);
            }
        });
    }

    swal({
        title: "บันทึกสำเร็จ",
        text: "",
        icon: "success",
        button: "OK",
    });

}
</script>