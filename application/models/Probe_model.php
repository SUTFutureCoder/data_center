<?php
/**
 * Created by PhpStorm.
 * User: linxingchen_iwm
 * Date: 2017/4/27
 * Time: 13:30
 */
class Probe_model extends CI_Model{
    public function __construct() {
        parent::__construct();
    }

    public function setProbe($arrProbeList){
        $this->load->database();

        //开始整理数据
        $arrInsert = [
            'host_id'       => $arrProbeList['BasicInfo']['host_info']['hostid'],
            'test_int'      => $arrProbeList['IntTest'],
            'test_float'    => $arrProbeList['FloatTest'],
            'test_io'       => $arrProbeList['IOTest'],
            'test_sum'      => (doubleval($arrProbeList['IntTest']) + doubleval($arrProbeList['FloatTest']) + doubleval($arrProbeList['IOTest'])),
            'os'            => $arrProbeList['BasicInfo']['host_info']['os'],
            'platform'      => $arrProbeList['BasicInfo']['host_info']['platform'],
            'platform_version'      => $arrProbeList['BasicInfo']['host_info']['platformVersion'],
            'kernel_version'        => $arrProbeList['BasicInfo']['host_info']['kernelVersion'],
            'mem_percent'       => $arrProbeList['BasicInfo']['mem_info']['usedPercent'],
            'mem_total'         => $arrProbeList['BasicInfo']['mem_info']['total'],
            'mem_swap_percent'      => $arrProbeList['HardwareInfo']['mem_swap_info']['usedPercent'],
            'mem_swap_total'        => $arrProbeList['HardwareInfo']['mem_swap_info']['total'],
            'cpu_percent'       => $arrProbeList['BasicInfo']['cpu_percent'][0],
            'cpu_model_name'        => $arrProbeList['HardwareInfo']['cpu_info'][0]['modelName'],
            'cpu_vendor'        => $arrProbeList['HardwareInfo']['cpu_info'][0]['vendorId'],
            'cpu_mhz'           => $arrProbeList['HardwareInfo']['cpu_info'][0]['mhz'],
            'cpu_cache_size'        => $arrProbeList['HardwareInfo']['cpu_info'][0]['cacheSize'],
            'cpu_family'        => $arrProbeList['HardwareInfo']['cpu_info'][0]['family'],
            'disk_percent'      => $arrProbeList['BasicInfo']['disk_info']['usedPercent'],
            'disk_total'        => $arrProbeList['BasicInfo']['disk_info']['total'],
            'disk_fstype'       => $arrProbeList['BasicInfo']['disk_info']['fstype'],

            'create_time'   => time(),
            'origin_json'   => json_encode($arrProbeList),

        ];
        $this->db->insert("probe", $arrInsert);

        //获取聚合后数据并返回
        $arrSelect = [
            'host_id',
            'test_int',
            'test_float',
            'test_io',
            'os',
            'platform',
            'platform_version',
            'kernel_version',
            'mem_percent',
            'mem_total',
            'mem_swap_percent',
            'mem_swap_total',
            'cpu_percent',
            'cpu_model_name',
            'cpu_vendor',
            'cpu_mhz',
            'cpu_cache_size',
            'cpu_family',
            'disk_percent',
            'disk_total',
            'disk_fstype',
            'create_time',
        ];
        $arrRet = [];

        //获取排名
        $this->db->where('test_sum <', (doubleval($arrProbeList['IntTest']) + doubleval($arrProbeList['FloatTest']) + doubleval($arrProbeList['IOTest'])));
        $this->db->group_by('host_id');
        $this->db->from('probe');
        $arrRet['rank'] = $this->db->count_all_results() + 1;

        //获取第一
        $this->db->select($arrSelect);
        $this->db->order_by('test_sum', 'ESC');
        $this->db->limit(1);
        $aceRet = $this->db->from('probe')->get()->result_array();
        $arrRet['ace']  = $aceRet;

        //获取前面的2个
        $this->db->select($arrSelect);
        $this->db->where('test_sum <', (doubleval($arrProbeList['IntTest']) + doubleval($arrProbeList['FloatTest']) + doubleval($arrProbeList['IOTest'])));
        $this->db->order_by('test_sum', 'DESC');    //注意离我越近越大
        $this->db->limit(2);
        $aceRet = $this->db->from('probe')->get()->result_array();
        $arrRet['up_two']  = $aceRet;

        //获取后面的2个
        $this->db->select($arrSelect);
        $this->db->where('test_sum >', (doubleval($arrProbeList['IntTest']) + doubleval($arrProbeList['FloatTest']) + doubleval($arrProbeList['IOTest'])));
        $this->db->order_by('test_sum', 'ESC');    //注意离我越近越大
        $this->db->limit(2);
        $aceRet = $this->db->from('probe')->get()->result_array();
        $arrRet['down_two']  = $aceRet;
        echo json_encode($arrRet);
        exit;
    }

}