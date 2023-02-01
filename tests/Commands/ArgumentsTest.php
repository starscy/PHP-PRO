<?php

namespace Starscy\Project\UnitTests\Commands;

use Starscy\Project\models\Commands\Arguments;
use Starscy\Project\models\Exceptions\ArgumentException;
use PHPUnit\Framework\TestCase;

/**
    * @covers ArgumentsTest
 */

class ArgumentsTest extends TestCase
{

    public function testItReturnsArgumentsValueByName(): void
    {
            // Подготовка
        $arguments = new Arguments(['some_key' => 23]);

            // Действие
        $value = $arguments->get('some_key');
            
            // Проверка
        $this->assertSame("23", $value);
    }


    public function testItReturnsValuesAsStrings(): void
    {
        // Создаём объект с числом в качестве значения аргумента
        $arguments = new Arguments(['some_key' => 123]);
        $value = $arguments->get('some_key');
        // Проверяем, что число стало строкой
        $this->assertEquals('123', $value);
    }

    public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
    {
            // Подготавливаем объект с пустым набором данных
        $arguments = new Arguments([]);

            // Описываем тип ожидаемого исключения
        $this->expectException(ArgumentException::class);

            // и его сообщение
        $this->expectExceptionMessage("No such argument: some_key");

            // Выполняем действие, приводящее к выбрасыванию исключения
        $arguments->get('some_key');
    }

    public function argumentsProvider(): iterable
    {
        return [
            ['some_string', 'some_string'], // Тестовый набор
            [' some_string', 'some_string'], // Тестовый набор No2
            [' some_string ', 'some_string'],
            [123, '123'],
            [12.3, '12.3'],
            ];
    }
        // Связываем тест с провайдером данных с помощью аннотации @dataProvider
        // У теста два агрумента
        // В одном тестовом наборе из провайдера данных два значения
        /**
        * @dataProvider argumentsProvider
        */

    public function testItConvertsArgumentsToStrings(
        $inputValue,
        $expectedValue
        ): void 
    {
            // Подставляем первое значение из тестового набора
        $arguments = new Arguments(['some_key' => $inputValue]);
        $value = $arguments->get('some_key');
        
            // Сверяем со вторым значением из тестового набора
        $this->assertEquals($expectedValue, $value);
    }
}