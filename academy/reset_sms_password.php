<div class='modal fade' id='enter-phone-for-reset-password' tabindex='-1' role='dialog'>
    <div class='modal-dialog' role='document'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Құпия сөзді ауыстыру</h4>
            </div>
            <div class='modal-body'>
                <form id='send-sms-code-form' class='form-horizontal' style='padding: 0 25px;'>
                    <center>
                        <p>Енгізетін телефон номеріңе 4 сандық код СМС түрінде барады барады</p>
                        <div class='form-group'>
                            <div class='input-group'>
                                <div class='input-group-addon'>+7</div>
                                <input type='number' max='7999999999' min='7000000000' step='1' autocomplete='off' name='phone-sms' class='form-control' id='phone' placeholder='Телефон нөмірін енгізіңіз' value='7*********' required>
                            </div>
                            <p id='student-does-not-exists-message' style='color: red;'></p>
                        </div>
                        <br>
                        <button class='btn btn-sm btn-success' type='submit' id='send-sms-code'>СМС жіберу</button>
                    </center>
                </form>
            </div>
        </div>
    </div>
</div>