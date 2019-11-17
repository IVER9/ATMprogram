<?php

require('./User.php');

class ATM {
    protected static $password_number_count = 4;                //暗証番号入力可能回数
    public $playUser;                                    //ATMを使用しているユーザ-

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

    public function deposit() {                                //入金
        $balance = $this->playUser['balance'];                //使用しているユーザーの残高を取得
        $deposit_amount;                                        //入金額
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

    public function balanceInquiry() {                                 //残高照会
        if (!$this->checkPassword()) {                                 //暗証番号照合
            echo '暗証番号が違います。' . PHP_EOL;
            return $this->balanceInquiry();
        }
        $balance = $this->playUser['balance'];                         //使用しているユーザーの残高を取得
        $balance = number_format($balance);
        echo '￥' . $balance . PHP_EOL;
    }

    public function withdrawal() {                                     //引き出し
        $balance = $this->playUser['balance'];                        //使用しているユーザーの残高を取得
        $withdrawal_amount;                                           //引き出し金額
        if (!$this->checkPassword()) {                                //暗証番号照合
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

    public function input($type) {                                     //入力関数
        $input = trim(fgets(STDIN));
        if ($type === 'menu_info') {                            //メニュー入力の時
            $check = $this->checkMenuNumber($input);
        }

        if ($type === 'password_number') {                      //暗証番号を入力の時
            $check = $this->checkNumber($input);
        }

        if ($type === 'money') {                                //入金額、出金額の時
            $check = $this->checkMoney($input);
        }

        if ($type === 'id') {                                //入金額、出金額の時
            $check = $this->checkId($input);
        }
        if ($type === 'name') {                                //入金額、出金額の時
            $check = $this->checkName($input);
        }

        if (!$check) {                                          // エラーがある場合は、やり直し
            return $this->input($type);
        }
        return $input;
    }

    public function forcedTermination() {                              //強制終了
        if ($this->$password_number_count === 0) {
            exit('強制終了します。');
        }
    }

    public function checkMenuNumber($input) {                              //メニュー番号のバリデーションチェック
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

    public function checkNumber($input) {                              //番号のバリデーションチェック
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

    public function checkMoney($input) {                               //金額のバリデーションチェック
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

    public function checkId($input) {                               //IDのバリデーションチェック
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

    public function checkName($input) {                               //名前のバリデーションチェック
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
