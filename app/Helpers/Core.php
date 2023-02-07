<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Hash;

class Core
{

    public static function getmail($data)
    {

        $ldap_server = "ldap://12.7.2.19"; //server ldap pkt
        $ldap_conn = ldap_connect($ldap_server);

        $emails = array();
        $no = 0;
        foreach ($data as $key => $value) {
            $result = ldap_search($ldap_conn, 'o=pkt', 'uid=' . $value->npk);
            $info = ldap_get_entries($ldap_conn, $result);
            //dd($info[0]['mail'][0]);die();
            //dd($info);die();
            if (!empty($value->email)) {
                $emails[$no] = $value->email;
            } else if ($info['count'] != 0) {
                $emails[$no] = $info[0]['mail'][0];
            }
            $no++;
        }

        return $emails;
    }

    public static function encodex($string, $key = "", $url_safe = TRUE)
    {
        // you may change these values to your own
        $secret_key = 'my_simple_secret_key';
        $secret_iv = 'my_simple_secret_iv';

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));

        if ($url_safe) {
            $output = strtr(
                $output,
                array(
                    '+' => '.',
                    '=' => '-',
                    '/' => '~'
                )
            );
        }

        return $output;
    }

    public static function decodex($string, $key = "", $url_safe = TRUE)
    {
        // you may change these values to your own
        $secret_key = 'my_simple_secret_key';
        $secret_iv = 'my_simple_secret_iv';

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $string = strtr(
            $string,
            array(
                '.' => '+',
                '-' => '=',
                '~' => '/'
            )
        );
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);

        return $output;
    }

    public function map_filter($filter)
    {
        $where = $this->parse($filter);
        return ($where == "" || $where == "()") ? "" : $where;
    }

    function parse($arr)
    {
        $ans = '';
        for ($i = 0; $i < count((array)$arr); $i++) {
            if (is_array($arr[$i])) {
                if ($i > 0) $ans .= ' ';
                $ans .= $this->parse($arr[$i]);
            } else {
                if ($i == 0) $ans .= $arr[$i];
                else if ($i % 2 == 1) {
                    if (is_array($arr[$i + 1]))
                        $ans .= ' ' . $arr[$i];
                    else {
                        $ans .= $this->convert_param($arr[$i], $arr[++$i]);
                    }
                }
            }
        }
        $ans .= '';
        return $ans;
    }

    public function convert_param($param, $filter_text)
    {
        switch ($param) {
            case "contains":
                return "^^LIKE^^%{$filter_text}%";
                break;
            case "notcontains":
                return "^^NOT LIKE^^%{$filter_text}%";
                break;
            case "startswith":
                return "^^LIKE^^{$filter_text}%";
                break;
            case "endswith":
                return "^^LIKE^^%{$filter_text}";
                break;
            case "=":
                if (strtolower($filter_text) === 'null') :
                    return "IS NULL";
                elseif (strtolower($filter_text) === 'not null') :
                    return "IS NOT NULL";
                else :
                    return "^^=^^{$filter_text}";
                endif;
                break;
            case "<>":
                return "^^<>^^{$filter_text}";
                break;
            case ">=":
                return "^^>=^^{$filter_text}";
                break;
            case "<=":
                return "^^<=^^{$filter_text}";
                break;
        }

        return false;
    }

    /////////////////////devextreme server side mssql

    public function filter($filters, $db)
    {
        if (in_array('and', $filters)) {

            $filters = ($filters);

            if (!empty($filters)) {
                foreach ($filters as $filter) {
                    if (is_array($filter)) {
                        if (is_array($filter[0])) {
                            foreach ($filter as $f_) {
                                if (is_array($f_)) {
                                    $this->doFilter($f_, $db);
                                }
                            }
                        } else {
                            $this->doFilter($filter, $db);
                        }
                    }
                }
            }
        } else {
            $filter = ($filters);
            if (is_array($filter)) {
                $this->doFilter($filter, $db);
            }
        }
    }

    private static function doFilter($filter, $db)
    {

        if ($filter[1] == 'contains') {
            $db->where($filter[0], 'LIKE', '%' . $filter[2] . '%');
        } elseif ($filter[1] == 'notcontains') {
            $db->where($filter[0], 'NOT LIKE', '%' . $filter[2] . '%');
        } elseif ($filter[1] == 'startswith') {
            $db->where($filter[0], 'LIKE', $filter[2] . '%');
        } elseif ($filter[1] == 'endswith') {
            $db->where($filter[0], 'LIKE', '%' . $filter[2]);
        } elseif ($filter[1] == '=') {
            $db->where($filter[0], $filter[2]);
        } elseif ($filter[1] == '<>') {
            $db->where($filter[0], '!=', $filter[2]);
        } elseif ($filter[1] == '>') {
            $db->where($filter[0], '>', $filter[2]);
        } elseif ($filter[1] == '<') {
            $db->where($filter[0], '<', $filter[2]);
        } elseif ($filter[1] == '>=') {
            $db->where($filter[0], '>=', $filter[2]);
        } elseif ($filter[1] == '<=') {
            $db->where($filter[0], '<=', $filter[2]);
        } elseif ($filter[1] == 'or') {
            $db->where($filter[0], 'LIKE', '%' . $filter[2] . '%');
        } else {
            $db->where($filter[0], 'LIKE', '%' . $filter[2] . '%');
        }
    }

    public function data($data, $request = null, $order = null, $take = null)
    {
        DB::enableQueryLog();
        $search = $request->filter ? $request->filter : '';
        //$search = !empty($search) ? json_decode($search) : '';

        if (!empty($search)) {
            $this->filter($search, $data);
        }
        //dd($search, $data);die(); 

        if ($take != null) {
            $count = $data->get()->take($take)->count();
        } else {
            $count = $data->get()->count();
        }

        $limit = $request->take ? $request->take : $count;
        $offset = $request->skip ? $request->skip : 0;

        $items = $data->skip($offset)->take($limit);


        if ($request->sort) {
            if ($request->sort == 'desc') {
                $items = $items->orderBy($request->orderby, 'desc');
            } else {
                $items = $items->orderBy($request->orderby, 'asc');
            }
        } 
        else{
            $items = $items->orderBy($order, 'desc');
            //$items = $items->orderBy('id', 'desc');
        }

        if ($take != null) {
            $items = $items->get()->take($take);
        } else {
            $items = $items->get();
        }

        //dd(DB::getQueryLog());die();

        return array(
            'offset' => $offset,
            'count' => $count,
            'recordsTotal' => intval($count),
            'recordsFiltered' => intval($count),
            'datax' => $items,
        );
    }

    function activity_type_desc($status_id, $action)
    {
        if ($status_id == 1 && $action == 'save') {
            return "Save Draft";
        } else if ($status_id == 1 && $action == 'Update') {
            return "Update Draft";
        } else if ($status_id == 2 && $action == 'Update') {
            return "Updated By Reviewer 1 (avp/vp)";
        } else if ($status_id == 2) {
            return "Submitted";
        } else if ($status_id == 3 && $action == 'Update') {
            return "Updated By Reviewer 2 (staf sekper)";
        } else if ($status_id == 3) {
            return "Reviewed By AVP / VP";
        } else if ($status_id == 4 && $action == 'Update') {
            return "Updated By SVP Sekper";
        } else if ($status_id == 4) {
            return "Reviewed By Staf Sekper";
        } else if ($status_id == 5) {
            return "Final Report";
        } else if ($status_id == 99) {
            return "Rejected";
        } else if ($action == 'reject') {
            return "Rejected";
        } else if ($action == 'delete') {
            return "Deleted";
        }
    }

    public static function tgl_indo($tanggal)
    {
        $bulan = array(
            1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
        $pecahkan = explode('-', $tanggal);

        // variabel pecahkan 0 = tanggal
        // variabel pecahkan 1 = bulan
        // variabel pecahkan 2 = tahun

        return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
    }

}
