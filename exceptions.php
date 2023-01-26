<?php

Class MyException extends Exception{};
Class HTTPexception extends Exception{};


function foo()
{
    try {
        throw new MyException('My error');
    } catch (MyException $exception) {
        echo "моя ошибка". PHP_EOL;
        echo $exception->getMessage();
        return false;
    }
    return true;
}

try {
    echo "start". PHP_EOL;
    var_dump(foo());
    echo "end". PHP_EOL;
} catch (HTTPexception $exception) {
    echo "HTTPexception ошибкa";
    $exception->getMessage();
} catch (Exception $exception) {
    echo "Все ошибки";
    $exception->getMessage();
}

