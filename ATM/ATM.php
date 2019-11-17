<?php

require('./User.php');

class ATM {
    protected static $password_number_count = 4;                
    public $playUser;                                    

    public function  __construct() {
        $this->selectUser();
    }

    public function selectUser() {
        echo 'IDを入力してください。' . PHP_EOL;
        $user_id = $this->input('id');
        if (!User::checkUserList($user_id)) {
            echo 'IDが見つかりません。' . PHP_EOL;
            return $this->selectUser();
        }
        if ($this->playUser['id'] === $user_id) {
            echo 'あなたのIDです。' . PHP_EOL;
            return $this->selectUser();
        }
        $this->playUser = User::getUserById($user_id);
    }

    public function play() {
        echo '1:残高照会, 2:入金, 3:引き出し, 4:ユーザーを切り替え, 5:終了' . PHP_EOL;
        echo 'ご利用のメニュー番号を入力してください。' . PHP_EOL;
        $menu = $this->input('menu_info');
        switch ($menu) {
            case 1:
                $this->balanceInquiry();
                return $this->play();
                break;
            case 2:
                $this->deposit();
                return $this->play();
                break;
            case 3:
                $this->withdrawal();
                return $this->play();
                break;
            case 4:
                $this->selectUser();
                return $this->play();
                break;
            case 5:
                echo 'ご利用ありがとうございました。' . PHP_EOL;
                break;
        }
    }

    public function checkPassword() {
        echo '暗証番号を入力してください' . PHP_EOL;
        $password = $this->input('password_number');
        if (!($this->playUser["password"] === $password)) {
            $this->$password_number_count --;
            $this->forcedTermination();
            return false;
        }
        self::$password_number_count = 4;
        return true;
    }

    public function deposit() {                                
        $balance = $this->playUser['balance'];                
        $deposit_amount;                                        
        echo '入金額を入力してください。' . PHP_EOL;
        $deposit_amount = $this->input('money');
        if ($deposit_amount > 500000) {
            echo '入金額が多すぎます' . PHP_EOL;
            return;
        }
        $balance += $deposit_amount;
        $deposit_amount = number_format($deposit_amount);

        echo '￥' . $deposit_amount . '入金しました。' . PHP_EOL;

        $this->playUser['balance'] = $balance;
        }

    public function balanceInquiry() {                                
        if (!$this->checkPassword()) {                                
            echo '暗証番号が違います。' . PHP_EOL;
            return $this->balanceInquiry();
        }
        $balance = $this->playUser['balance'];                        
        $balance = number_format($balance);
        echo '￥' . $balance . PHP_EOL;
    }

    public function withdrawal() {                                     
        $balance = $this->playUser['balance'];                        
        $withdrawal_amount;                                         
        if (!$this->checkPassword()) {                               
            echo '暗証番号が違います。' . PHP_EOL;
            return $this->withdrawal();
        }
        echo '出金額を入力してください。' . PHP_EOL;
        $withdrawal_amount = $this->input('money');
        if ($balance  < $withdrawal_amount) {
            echo '引き出し金額が残高を超えています。' . PHP_EOL;
            return;
        }
        $balance -= $withdrawal_amount;
        $withdrawal_amount = number_format($withdrawal_amount);
        echo '￥' . $withdrawal_amount . '引きだしました。' . PHP_EOL;
        $this->playUser['balance'] = $balance;
    }

    public function input($type) {                                     
        $input = trim(fgets(STDIN));
        if ($type === 'menu_info') {                           
            $check = $this->checkMenuNumber($input);
        }

        if ($type === 'password_number') {                     
            $check = $this->checkNumber($input);
        }

        if ($type === 'money') {                                
            $check = $this->checkMoney($input);
        }

        if ($type === 'id') {                               
            $check = $this->checkId($input);
        }
        if ($type === 'name') {                                
            $check = $this->checkName($input);
        }

        if (!$check) {                                          
            return $this->input($type);
        }
        return $input;
    }

    public function forcedTermination() {                              
        if ($this->$password_number_count === 0) {
            exit('強制終了します。');
        }
    }

    public function checkMenuNumber($input) {                             
        $errors = array();
        if ($input === '') {
            $errors[] = '未入力です';
        }

        if (!ctype_digit(strval($input))) {
            $errors[] = '整数を入力してください';
        }

        if (!($input >= 1 && $input <= 5)) {
            $errors[] = '[1, 2, 3, 4, 5]を入力してください。' . PHP_EOL;
        }

        if ($errors) {
            foreach ($errors as $error) {
                echo $error . PHP_EOL;
            }
            return false;
        }
        return $input;
    }

    public function checkNumber($input) {                             
        $errors = array();
        if ($input === '') {
            $errors[] = '未入力です';
        }

        if (ctype_digit(strval($input)) === false) {
            $errors[] = '整数を入力してください';
        }

        if (!preg_match('/^([0-9]{4})$/', $input)) {
            $errors[] = '4桁の数字を入力してください。';
        }

        if ($errors) {
            foreach ($errors as $error) {
                echo $error . PHP_EOL;
            }
            return false;
        }
        return $input;
    }

    public function checkMoney($input) {                              
        $errors = array();
        if ($input === '') {
            $errors[] = '未入力です';
        }

        if (ctype_digit(strval($input)) === false) {
            $errors[] = '整数を入力してください';
        }

        if ($errors) {
            foreach ($errors as $error) {
                echo $error . PHP_EOL;
            }
            return false;
        }
        return $input;
    }

    public function checkId($input) {                              
        $errors = array();
        if ($input === '') {
            $errors[] = '未入力です';
        }

        if (ctype_digit(strval($input)) === false) {
            $errors[] = '整数を入力してください';
        }

        if ($errors) {
            foreach ($errors as $error) {
                echo $error . PHP_EOL;
            }
            return false;
        }
        return $input;
    }

    public function checkName($input) {                               
        $errors = array();
        if ($input === '') {
            $errors[] = '未入力です';
        }

        if (!preg_match("/^[a-zA-Z]+$/", $input)) {
            $errors[] = '文字を入力してください';
        }

        if ($errors) {
            foreach ($errors as $error) {
                echo $error . PHP_EOL;
            }
            return false;
        }
        return $input;
    }

}

$ATM = new ATM();
$ATM -> play();

 ?>
