<?php

namespace AlexMcArrow\DataMapper;

class DataMapper
{
    /**
     * Use SOFT-algoritm for mapping
     * @var int
     */
    public static int $FILTER_TYPE_SOFT = 10;
    /**
     * Use HARD-algoritm for mapping
     * @var int
     */
    public static int $FILTER_TYPE_HARD = 11;

    /**
     * Pass all columns
     * @var int
     */
    public static int $FILTER_PASS_ALL = 20;
    /**
     * Cut not exists in filter columns
     * @var int
     */
    public static int $FILTER_PASS_CUT = 21;

    /**
     * Use KEY (from filter-array) for mapping
     * @var int
     */
    public static int $MAP_KEY = 30;
    /**
     * Use VALUE (from filter-array) for mapping
     * @var int
     */
    public static int $MAP_VALUE = 31;

    private static array $filter = [];

    /**
     * Mapping has error
     * @var bool
     */
    public static bool $has_error;
    /**
     * Mapping error-array-stack
     * @var array
     */
    public static array $error = [];

    public function __construct()
    {
        self::$filter = [];
        self::$error = [];
    }

    /**
     * Field normalize
     * @param (int|string) $field
     */
    public static function fieldnormalize(mixed $field): string
    {
        return trim(mb_strtolower((string)$field));
    }

    /**
     * Set Fields filter
     * @param array $fields
     */
    public static function setFieldsFilter(array $fields): void
    {
        self::$filter = [];
        foreach ($fields as $key => $value) {
            self::$filter[self::fieldnormalize($key)] = self::fieldnormalize($value);
        }
    }


    /**
     * Parse and map data by exist filter
     * @param array $data Filtered data
     * @param int $filterpass ALL - keep columns, CUT - remove columns
     * @param int $filtertype SOFT - , HARD -
     * @param int $mapin [KEY|VALUE] - key or value used as key for INPUT
     * @param int $mapout [KEY|VALUE] - key or value used as key for OUTPUT
     * @return (array|string)[]
     */
    public static function parseMapData(array $data, int $filterpass, int $filtertype, int $mapin, int $mapout)
    {
        self::$has_error = false;
        self::$error = [];
        $foundFields = [];
        $out = [];
        foreach ($data[0] as $key => $value) {
            switch ($mapin) {
                case self::$MAP_KEY:
                    if (array_key_exists(self::fieldnormalize($key), self::$filter) !== false) {
                        switch ($mapout) {
                            case self::$MAP_KEY:
                                $foundFields[self::fieldnormalize($key)] = self::fieldnormalize($key);
                                break;
                            case self::$MAP_VALUE:
                            default:
                                $foundFields[self::fieldnormalize($key)] = self::$filter[self::fieldnormalize($key)];
                                break;
                        }
                    }
                    break;
                case self::$MAP_VALUE:
                default:
                    if (array_search(self::fieldnormalize($key), self::$filter) !== false) {
                        switch ($mapout) {
                            case self::$MAP_KEY:
                                $foundFields[self::$filter[array_search(self::fieldnormalize($key), self::$filter)]] = array_search(self::fieldnormalize($key), self::$filter);
                                break;
                            case self::$MAP_VALUE:
                            default:
                                $foundFields[self::$filter[array_search(self::fieldnormalize($key), self::$filter)]] = self::$filter[array_search(self::fieldnormalize($key), self::$filter)];
                                break;
                        }
                    }
                    break;
            }
        }

        if ($filtertype == self::$FILTER_TYPE_HARD) {
            switch ($mapin) {
                case self::$MAP_KEY:
                    $infilter = array_keys(self::$filter);
                    switch ($mapout) {
                        case self::$MAP_KEY:
                            $infound = array_values($foundFields);
                        case self::$MAP_VALUE:
                        default:
                            $infound = array_keys($foundFields);
                            break;
                    }
                    break;
                case self::$MAP_VALUE:
                default:
                    $infilter = array_values(self::$filter);
                    switch ($mapout) {
                        case self::$MAP_KEY:
                            $infound = array_values($foundFields);
                        case self::$MAP_VALUE:
                        default:
                            $infound = array_keys($foundFields);
                            break;
                    }
                    break;
            }
            $arrdiff = array_diff($infilter, $infound);
            if (count($arrdiff) > 0) {
                self::$has_error = true;
                self::$error[] = 'Data don`t have required fields: ' . implode(' ', $arrdiff) . ';';
                return [];
            }
        }

        foreach ($data as $rowdata) {
            $row = [];
            foreach ($rowdata as $rowdataID => $rowdataValue) {
                if ($filterpass == self::$FILTER_PASS_CUT && array_key_exists(self::fieldnormalize($rowdataID), $foundFields) !== false) {
                    $row[$foundFields[self::fieldnormalize($rowdataID)]] = $rowdataValue;
                } elseif ($filterpass == self::$FILTER_PASS_ALL) {
                    if (array_key_exists(self::fieldnormalize($rowdataID), $foundFields) !== false) {
                        $row[$foundFields[self::fieldnormalize($rowdataID)]] = $rowdataValue;
                    } else {
                        $row[$rowdataID] = $rowdataValue;
                    }
                }
            }
            $out[] = $row;
        }

        return $out;
    }
}
