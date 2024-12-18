<?php

namespace App\Tests\Service;

use App\Service\ValueWithUnitHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ValueWithUnitHelperTest extends KernelTestCase
{
    private ValueWithUnitHelper $helper;

    protected function setUp(): void
    {
        // https://symfony.com/doc/current/testing.html#retrieving-services-in-the-test
        self::bootKernel();
        $container = static::getContainer();

        $this->helper = $container->get(ValueWithUnitHelper::class);
    }

    public function testGetScale(): void
    {
        $helper = $this->helper->withOptions([
            ValueWithUnitHelper::OPTION_UNITS => ['test' => [ValueWithUnitHelper::OPTION_UNIT_LABEL => 'test']],
            ValueWithUnitHelper::OPTION_SCALE => 2,
        ]);
        $expected = 2;
        $actual = $helper->getScale();

        $this->assertEquals($expected, $actual);
    }

    public function testGetUnits(): void
    {
        $helper = $this->helper->withOptions([
            ValueWithUnitHelper::OPTION_UNITS => ['test' => [ValueWithUnitHelper::OPTION_UNIT_LABEL => 'test']],
        ]);
        $expected = [
            'test' => [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'test',
                ValueWithUnitHelper::OPTION_UNIT_SCALE => 1,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
            ],
        ];
        $actual = $helper->getUnits();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getMatchingUnitProvider
     */
    public function testGetMatchingUnit(array $options, int $value, array $expected): void
    {
        $helper = $this->helper->withOptions($options);
        $actual = $helper->getMatchingUnit($value);

        $this->assertEquals($expected, $actual);
    }

    public static function getMatchingUnitProvider(): iterable
    {
        $options = [
            ValueWithUnitHelper::OPTION_UNITS => ['test' => [ValueWithUnitHelper::OPTION_UNIT_LABEL => 'test']],
        ];

        yield [
            $options,
            87,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'test',
                ValueWithUnitHelper::OPTION_UNIT_SCALE => 1,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 87,
                ValueWithUnitHelper::FIELD_UNIT => 'test',
            ],
        ];

        $options = [
            ValueWithUnitHelper::OPTION_UNITS => [
                'm' => [
                    ValueWithUnitHelper::OPTION_UNIT_LABEL => 'meter',
                    ValueWithUnitHelper::OPTION_SCALE => 1,
                ],
                'km' => [
                    ValueWithUnitHelper::OPTION_UNIT_LABEL => 'kilometer',
                    ValueWithUnitHelper::OPTION_SCALE => 1000,
                ],
            ],
        ];

        yield [
            $options,
            87,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'meter',
                ValueWithUnitHelper::OPTION_UNIT_SCALE => 1,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 87,
                ValueWithUnitHelper::FIELD_UNIT => 'm',
            ],
        ];

        yield [
            $options,
            1234,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'kilometer',
                ValueWithUnitHelper::OPTION_UNIT_SCALE => 1000,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 1,
                ValueWithUnitHelper::FIELD_UNIT => 'km',
            ],
        ];

        $options[ValueWithUnitHelper::OPTION_SCALE] = 1;
        yield [
            $options,
            87,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'meter',
                ValueWithUnitHelper::OPTION_UNIT_SCALE => 1,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 87,
                ValueWithUnitHelper::FIELD_UNIT => 'm',
            ],
        ];

        yield [
            $options,
            1234,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'kilometer',
                ValueWithUnitHelper::OPTION_UNIT_SCALE => 1000,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 1.2,
                ValueWithUnitHelper::FIELD_UNIT => 'km',
            ],
        ];
    }
}
