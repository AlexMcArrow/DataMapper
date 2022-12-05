<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AlexMcArrow\DataMapper\DataMapper;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertArrayNotHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertIsArray;

final class DataMapperTest extends TestCase
{
    public function testDataMapperInitialization(): void
    {
        self::assertInstanceOf(DataMapper::class, new DataMapper(), 'Not DataMapper class');
    }

    public function _data(): array
    {
        return [
            [
                'key' => 'a1',
                'name' => 'Alex',
                'email' => 'alex@domain.tld'
            ],
            [
                'key' => 'b2',
                'name' => 'Axel'
            ]
        ];
    }

    public function testDataMapperSetFiltersAllSoftKeyValue(): void
    {
        new DataMapper();
        DataMapper::setFieldsFilter([
            'key' => 'KeyID',
            'name' => 'User Name'
        ]);
        $outdata = DataMapper::parseMapData($this->_data(), DataMapper::$FILTER_PASS_ALL, DataMapper::$FILTER_TYPE_SOFT, DataMapper::$MAP_KEY, DataMapper::$MAP_VALUE);
        assertIsArray($outdata, 'DataMapper return NOT array');
        assertArrayHasKey('keyid', $outdata[0], 'keyid - not exist in outdata');
        assertArrayHasKey('user name', $outdata[0], 'user name -  not exist in outdata');
        assertArrayNotHasKey('key', $outdata[0], 'key - exist in outdata');
        assertArrayNotHasKey('name', $outdata[0], 'name - exist in outdata');
        assertCount(3, $outdata[0], 'wrong count of outdata for ALL');
    }

    public function testDataMapperSetFiltersCutSoftKeyValue(): void
    {
        new DataMapper();
        DataMapper::setFieldsFilter([
            'key' => 'KeyID'
        ]);
        $outdata = DataMapper::parseMapData($this->_data(), DataMapper::$FILTER_PASS_CUT, DataMapper::$FILTER_TYPE_SOFT, DataMapper::$MAP_KEY, DataMapper::$MAP_VALUE);
        assertIsArray($outdata, 'DataMapper return NOT array');
        assertArrayHasKey('keyid', $outdata[0], 'keyid - not exist in outdata');
        assertArrayNotHasKey('name', $outdata[0], 'name - exist in outdata');
        assertArrayNotHasKey('user name', $outdata[0], 'user name - exist in outdata');
        assertCount(2, $outdata, 'wrong count of outdata');
        assertCount(1, $outdata[0], 'wrong count of outdata for CUT');
    }

    public function testDataMapperSetFiltersCutHardKeyValue(): void
    {
        new DataMapper();
        DataMapper::setFieldsFilter([
            'key' => 'KeyID'
        ]);
        $outdata = DataMapper::parseMapData($this->_data(), DataMapper::$FILTER_PASS_CUT, DataMapper::$FILTER_TYPE_HARD, DataMapper::$MAP_KEY, DataMapper::$MAP_VALUE);
        assertIsArray($outdata, 'DataMapper return NOT array');
        assertArrayHasKey('keyid', $outdata[0], 'keyid - not exist in outdata');
        assertArrayNotHasKey('name', $outdata[0], 'name - exist in outdata');
        assertArrayNotHasKey('user name', $outdata[0], 'user name - exist in outdata');
        assertCount(2, $outdata, 'wrong count of outdata');
        assertCount(1, $outdata[0], 'wrong count of outdata for CUT');
    }

    public function testDataMapperSetFiltersCutHardKeyKey(): void
    {
        new DataMapper();
        DataMapper::setFieldsFilter([
            'key' => 'KeyID'
        ]);
        $outdata = DataMapper::parseMapData($this->_data(), DataMapper::$FILTER_PASS_CUT, DataMapper::$FILTER_TYPE_HARD, DataMapper::$MAP_KEY, DataMapper::$MAP_KEY);
        assertIsArray($outdata, 'DataMapper return NOT array');
        assertArrayHasKey('key', $outdata[0], 'key - not exist in outdata');
        assertArrayNotHasKey('keyid', $outdata[0], 'keyid - exist in outdata');
        assertArrayNotHasKey('name', $outdata[0], 'name - exist in outdata');
        assertCount(2, $outdata, 'wrong count of outdata');
        assertCount(1, $outdata[0], 'wrong count of outdata for CUT');
    }

    public function testDataMapperSetFiltersCutHardValueKey(): void
    {
        new DataMapper();
        DataMapper::setFieldsFilter([
            'key' => 'KeyID'
        ]);
        $outdata = DataMapper::parseMapData($this->_data(), DataMapper::$FILTER_PASS_CUT, DataMapper::$FILTER_TYPE_HARD, DataMapper::$MAP_VALUE, DataMapper::$MAP_KEY);
        assertIsArray($outdata, 'DataMapper return NOT array');
        assertCount(0, $outdata, 'wrong count of outdata');
    }
}
