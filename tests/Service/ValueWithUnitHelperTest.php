<?php

namespace App\Tests\Service;

use App\Service\ValueWithUnitHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\LocaleSwitcher;

final class ValueWithUnitHelperTest extends KernelTestCase
{
    private ValueWithUnitHelper $helper;

    protected function setUp(): void
    {
        // https://symfony.com/doc/current/testing.html#retrieving-services-in-the-test
        self::bootKernel();
        $container = static::getContainer();

        $this->helper = $container->get(ValueWithUnitHelper::class);
        $container->get(LocaleSwitcher::class)->setLocale('en');
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
                ValueWithUnitHelper::OPTION_UNIT_FACTOR => 1,
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
            ValueWithUnitHelper::OPTION_UNITS => [
                'test' => [
                    ValueWithUnitHelper::OPTION_UNIT_LABEL => 'test',
                ],
            ],
        ];

        yield [
            $options,
            87,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'test',
                ValueWithUnitHelper::OPTION_UNIT_FACTOR => 1,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 87,
                ValueWithUnitHelper::FIELD_UNIT => 'test',
            ],
        ];

        $options = [
            ValueWithUnitHelper::OPTION_UNITS => [
                'm' => [
                    ValueWithUnitHelper::OPTION_UNIT_LABEL => 'meter',
                    ValueWithUnitHelper::OPTION_UNIT_FACTOR => 1,
                ],
                'km' => [
                    ValueWithUnitHelper::OPTION_UNIT_LABEL => 'kilometer',
                    ValueWithUnitHelper::OPTION_UNIT_FACTOR => 1000,
                ],
            ],
        ];

        yield [
            $options,
            87,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'meter',
                ValueWithUnitHelper::OPTION_UNIT_FACTOR => 1,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 87,
                ValueWithUnitHelper::FIELD_UNIT => 'm',
            ],
        ];

        yield [
            $options,
            1234,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'meter',
                ValueWithUnitHelper::OPTION_UNIT_FACTOR => 1,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 1234,
                ValueWithUnitHelper::FIELD_UNIT => 'm',
            ],
        ];

        $options[ValueWithUnitHelper::OPTION_SCALE] = 1;
        yield [
            $options,
            87,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'meter',
                ValueWithUnitHelper::OPTION_UNIT_FACTOR => 1,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 87,
                ValueWithUnitHelper::FIELD_UNIT => 'm',
            ],
        ];

        yield [
            $options,
            1234,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'meter',
                ValueWithUnitHelper::OPTION_UNIT_FACTOR => 1,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 1234,
                ValueWithUnitHelper::FIELD_UNIT => 'm',
            ],
        ];

        yield [
            $options,
            2000,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'kilometer',
                ValueWithUnitHelper::OPTION_UNIT_FACTOR => 1000,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 2.0,
                ValueWithUnitHelper::FIELD_UNIT => 'km',
            ],
        ];

        yield [
            $options,
            2100,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'kilometer',
                ValueWithUnitHelper::OPTION_UNIT_FACTOR => 1000,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 2.1,
                ValueWithUnitHelper::FIELD_UNIT => 'km',
            ],
        ];

        $options = [
            ValueWithUnitHelper::OPTION_UNITS => [
                'hour' => [
                    ValueWithUnitHelper::OPTION_UNIT_LABEL => 'hours',
                    ValueWithUnitHelper::OPTION_UNIT_FACTOR => 60 * 60,
                ],
                'minute' => [
                    ValueWithUnitHelper::OPTION_UNIT_LABEL => 'minutes',
                    ValueWithUnitHelper::OPTION_UNIT_FACTOR => 60,
                ],
            ],
        ];

        yield [
            $options,
            9000,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'minutes',
                ValueWithUnitHelper::OPTION_UNIT_FACTOR => 60,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 150,
                ValueWithUnitHelper::FIELD_UNIT => 'minute',
            ],
        ];

        yield [
            $options,
            10800,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'hours',
                ValueWithUnitHelper::OPTION_UNIT_FACTOR => 60 * 60,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 3,
                ValueWithUnitHelper::FIELD_UNIT => 'hour',
            ],
        ];

        $options[ValueWithUnitHelper::OPTION_SCALE] = 1;

        yield [
            $options,
            9000,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'hours',
                ValueWithUnitHelper::OPTION_UNIT_FACTOR => 60 * 60,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 2.5,
                ValueWithUnitHelper::FIELD_UNIT => 'hour',
            ],
        ];

        yield [
            $options,
            10800,
            [
                ValueWithUnitHelper::OPTION_UNIT_LABEL => 'hours',
                ValueWithUnitHelper::OPTION_UNIT_FACTOR => 60 * 60,
                ValueWithUnitHelper::OPTION_UNIT_LOCALIZED_UNIT => null,
                ValueWithUnitHelper::FIELD_VALUE => 3,
                ValueWithUnitHelper::FIELD_UNIT => 'hour',
            ],
        ];
    }

    /**
     * @dataProvider getFormattedValueProvider
     */
    public function testGetFormattedValue(array $options, int $value, string $expected): void
    {
        $helper = $this->helper->withOptions($options);
        $actual = $helper->getFormattedValue($value);

        $this->assertEquals($expected, $actual);
    }

    public static function getFormattedValueProvider(): iterable
    {
        $options = [
            ValueWithUnitHelper::OPTION_UNITS => [
                'test' => [
                    ValueWithUnitHelper::OPTION_UNIT_LABEL => 'test',
                ],
            ],
        ];

        yield [
            $options,
            87,
            '87 test',
        ];

        $options = [
            ValueWithUnitHelper::OPTION_UNITS => [
                'm' => [
                    ValueWithUnitHelper::OPTION_UNIT_LABEL => 'meter',
                    ValueWithUnitHelper::OPTION_UNIT_FACTOR => 1,
                ],
                'km' => [
                    ValueWithUnitHelper::OPTION_UNIT_LABEL => 'kilometer',
                    ValueWithUnitHelper::OPTION_UNIT_FACTOR => 1000,
                ],
            ],
        ];

        yield [
            $options,
            87,
            '87 m',
        ];

        yield [
            $options,
            1234,
            '1234 m',
        ];

        $options[ValueWithUnitHelper::OPTION_SCALE] = 1;
        yield [
            $options,
            87,
            '87.0 m',
        ];

        yield [
            $options,
            1234,
            '1234.0 m',
        ];

        yield [
            $options,
            2000,
            '2.0 km',
        ];

        yield [
            $options,
            2100,
            '2.1 km',
        ];

        $options = [
            ValueWithUnitHelper::OPTION_UNITS => [
                'hour' => [
                    ValueWithUnitHelper::OPTION_UNIT_LABEL => 'hours',
                    ValueWithUnitHelper::OPTION_UNIT_FACTOR => 60 * 60,
                ],
                'minute' => [
                    ValueWithUnitHelper::OPTION_UNIT_LABEL => 'minutes',
                    ValueWithUnitHelper::OPTION_UNIT_FACTOR => 60,
                ],
            ],
        ];

        yield [
            $options,
            9000,
            '150 minute',
        ];

        yield [
            $options,
            10800,
            '3 hour',
        ];

        $options[ValueWithUnitHelper::OPTION_SCALE] = 1;
        yield [
            $options,
            9000,
            '2.5 hour',
        ];

        yield [
            $options,
            10800,
            '3.0 hour',
        ];
    }
}
