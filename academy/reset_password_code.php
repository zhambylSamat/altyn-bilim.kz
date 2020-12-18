<div class='modal fade' id='enter-sms-code-password-modal' tabindex='-1' role='dialog'>
    <div class='modal-dialog' role='document'>
        <div class='modal-content'>
            <div class='modal-header' style='background-color: #38B6FF;'>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">СМС код телефон номеріңе жіберілді. Сол кодты енгіз</h4>
            </div>
            <div class='modal-body'>
                <form class='form-horizontal'>
                    <input type="hidden" name="phone" value=''>
                    <div class='form-group'>
                        <label class='control-label col-md-4 col-sm-4 col-xs-12'>СМС код</label>
                        <div class='col-md-8 col-sm-8 col-xs-12'>
                            <!-- <input type="number" name="sms-code" max='9999' class='form-control' min='0000' required placeholder="КОД"> -->
                            <table class='sms-code-digis-table'>
                                <tr>
                                    <td>
                                        <input id="digit-1" class="sms-code-digit" autocomplete="off" data-id='1' type="text" name='code[1]' maxlength="1" size="1">
                                    </td>
                                    <td>
                                        <input id="digit-2" class="sms-code-digit" autocomplete="off" data-id='2' type="text" name='code[2]' maxlength="1" size="1">
                                    </td>
                                    <td>
                                        <input id="digit-3" class="sms-code-digit" autocomplete="off" data-id='3' type="text" name='code[3]' maxlength="1" size="1">
                                    </td>
                                    <td>
                                        <input id="digit-4" class="sms-code-digit" autocomplete="off" data-id='4' type="text" name='code[4]' maxlength="1" size="1">
                                    </td>
                                </tr>
                            </table>
                            <p id='sms-code-wrong-message' style='color: red;'></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
