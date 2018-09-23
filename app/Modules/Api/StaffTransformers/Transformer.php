<?php

namespace App\Modules\Api\StaffTransformers;


use Carbon\Carbon;

abstract class Transformer
{
    public static $lang;

    public function transformCollection(array $items, $systemLang=['en'], $method = 'transform')
    {
        if (is_array($systemLang))
            self::$lang = ((end($systemLang) == 'ar') ? 'ar' : 'en');
        else
            self::$lang = $systemLang;
        if (isset($items['current_page'])) {
            return $this->transformPaginate($items, $systemLang, $method);
        }

        return array_map([$this, $method], $items, $systemLang);
    }


    public function transformPaginate($item, $opt, $method)
    {
        return array_merge([
            'current_page' => $item['current_page'],
            'from' => ((isset($item['from'])) ? $item['from'] : ""),
            'last_page' => $item['last_page'],
            'next_page_url' => ((!$item['next_page_url']) ? '' : self::NextPageUrl($item['next_page_url'])),
            'path' => self::NextPageUrl($item['path']),
            'per_page' => $item['per_page'],
            'prev_page_url' => (isset($item['prev_page_url']) ? self::NextPageUrl($item['prev_page_url']) : ""),
            'to' => ((isset($item['to'])) ? $item['to'] : ""),
            'total' => $item['total'],
        ], ['items' => $this->transformCollection($item['data'], $opt, $method)]);
    }

    public abstract function transform($item, $opt);

    private static function NextPageUrl($url)
    {
       return trim(substr($url, strpos($url, '/', 45)), '/');
    }


    public static function trans($item, $column, $lang)
    {
        if (!isset(self::$lang)) {
            if (isset($lang))
                self::$lang = ((is_array($lang)) ? end($lang) : $lang);
        }

        if (isset($item[$column]))
            return $item[$column];
        else if (isset($item[$column . '_' . self::$lang])) {
            return $item[$column . '_' . self::$lang];
        } else
            return null;
    }
// http://192.168.1.7:8080/seattle/public/api/staff/
    public static function isPaid($item)
    {
        return (($item['is_paid'] == 'yes') ? true : false);
    }

    public static function status($item)
    {
        return (($item['status'] == 'active') ? true : false);
    }

    public static function payVia($item)
    {
        return ((isset($item['type'])) ? (($item['type'] == 'wallet') ? 'Wallet' : 'Cash') : null);
    }

    public static function Link($item, $column)
    {
        if (isset($item[$column])) {
            return $item[$column];
            //return getenv('APP_URL') . '/' . $item[$column];
        } else
            return "";
    }

    public static function fullName($item)
    {
        if (isset($item)) {
            $fullname = '';
            if (isset($item['firstname']))
                $fullname = $item['firstname'];
            if (isset($item['middlename']) && strlen($item['middlename']))
                $fullname .= ' ' . $item['middlename'];
            if (isset($item['lastname']))
                $fullname .= ' ' . $item['lastname'];
            return $fullname;
        } else
            return null;
    }

    public static function walletOwnerType($item)
    {
        if (isset($item['merchant_category_id']))
            return 'Merchant';
        elseif (isset($item['unique_name']))
            return __('System Wallet');
        else
            return 'User';
    }

    public static function Round($number)
    {
        return number_format($number, 2);
    }


    public static function TransformerDate($datetime)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $datetime);
    }
}