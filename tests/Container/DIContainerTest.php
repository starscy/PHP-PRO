<?php

namespace Starscy\Project\UnitTests\Container;

use Starscy\Project\models\Container\DIContainer;
use Starscy\Project\models\Exceptions\NotFoundException;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\Repositories\User\UserRepository;
use PHPUnit\Framework\TestCase;

class DIContainerTest extends TestCase
{
    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {
        // Создаём объект контейнера

        $container = new DIContainer();
        
        // Описываем ожидаемое исключение

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot resolve type: Starscy\Project\UnitTests\Container\SomeClass'
        );

        // Пытаемся получить объект несуществующего класса

        $container->get(SomeClass::class);
    }

    public function testItResolvesClassWithoutDependencies(): void
    {
        // Создаём объект контейнера

        $container = new DIContainer();

        // Пытаемся получить объект класса без зависимостей

        $object = $container->get(SomeClassWithoutDependencies::class);

        // Проверяем, что объект, который вернул контейнер,
        // имеет желаемый тип

        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object
        );
    }

    public function testItResolvesClassByContract(): void
    {
        // Создаём объект контейнера

        $container = new DIContainer();
        
        // Устанавливаем правило, по которому
        // всякий раз, когда контейнеру нужно
        // создать объект, реализующий контракт
        // UserRepositoryInterface, он возвращал бы
        // объект класса UserRepository

        $container->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        // Пытаемся получить объект класса,
        // реализующего контракт UserRepositoryInterface

        $object = $container->get(UserRepositoryInterface::class);

        // Проверяем, что контейнер вернул
        // объект класса UserRepository

        $this->assertInstanceOf(
        UserRepository::class,
        $object
        );
    }

    public function testItReturnsPredefinedObject(): void
    {
        // Создаём объект контейнера

        $container = new DIContainer();

        // Устанавливаем правило, по которому
        // всякий раз, когда контейнеру нужно
        // вернуть объект типа SomeClassWithParameter,
        // он возвращал бы предопределённый объект

        $container->bind(
        SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        // Пытаемся получить объект типа SomeClassWithParameter

        $object = $container->get(SomeClassWithParameter::class);

        // Проверяем, что контейнер вернул
        // объект того же типа

        $this->assertInstanceOf(
            SomeClassWithParameter::class,
        $object
        );

        // Проверяем, что контейнер вернул
        // тот же самый объект

        $this->assertSame(42, $object->value());
    }

    public function testItResolvesClassWithDependencies(): void
    {
        // Создаём объект контейнера

        $container = new DIContainer();

        // Устанавливаем правило получения
        // объекта типа SomeClassWithParameter

        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        // Пытаемся получить объект типа ClassDependingOnAnother

        $object = $container->get(ClassDependingOnAnother::class);

        // Проверяем, что контейнер вернул
        // объект нужного нам типа

        $this->assertInstanceOf(
            ClassDependingOnAnother::class,
            $object
        );
    }
}