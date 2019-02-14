<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Aliyun\Core\Regions\EndpointProvider;

class AliyunIotServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //genernate aliyun config file
        $this->genernateConfig();
        $this->initEndpoint();

    }

    /**
     * initialize the configuration of  aliyun core
     */
    public function initEndpoint(){

        //config http proxy
        define('ENABLE_HTTP_PROXY', false);
        define('HTTP_PROXY_IP', '127.0.0.1');
        define('HTTP_PROXY_PORT', '8888');

        //get endpoint configuration
        $endpoint_config = config('endpoint');
        if(!is_array($endpoint_config)){
            throw new \Exception('阿里云配置文件不存在,请刷新自动生成或使用命令行生成！');
        }

        $endpoints = array();
        foreach ($endpoint_config as $endpoint_item) {
            # pre-process RegionId & Product
            $region_ids = $endpoint_item['RegionIds'];
            $products = $endpoint_item['Products'];

            $product_domains = array();
            foreach ($products as $product) {
                $product_domain = new \Aliyun\Core\Regions\ProductDomain($product['ProductName'], $product['DomainName']);
                array_push($product_domains, $product_domain);
            }

            $endpoint = new \Aliyun\Core\Regions\Endpoint($region_ids[0], $region_ids, $product_domains);
            array_push($endpoints, $endpoint);
        }

        EndpointProvider::setEndpoints($endpoints);
    }

    /**
     * genernate the configuration file of laravel by aliyun xml
     */
    public function genernateConfig(){

        //The path of configuration file is /path/to/config/endpoint.php
        $config_file = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "config". DIRECTORY_SEPARATOR ."endpoint.php";
        //如果存在配置文件，则退出生成
        if(is_file($config_file)) return false;

        //The path of aliyun endpoint file is /path/to/third/Aliyun/Core/Regions/endpoints.xml
        $endpoint_filename = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "third/Aliyun/Core/Regions/" . "endpoints.xml";
        $xml = simplexml_load_string(file_get_contents($endpoint_filename));
        $json = json_encode($xml);
        $json_array = json_decode($json, true);

        // 将xml转换成数组文本
        $str = "<?php \n return [\n";
        foreach ($json_array['Endpoint'] as $json_endpoint){
            $str .= "   [\n";
            if (!array_key_exists("RegionId", $json_endpoint["RegionIds"])) {
                $str .= "       \"RegionIds\" => [],\n";
            } else {
                $json_region_ids = $json_endpoint['RegionIds']['RegionId'];
                if (!is_array($json_region_ids)) {
                    $str .= "       \"RegionIds\" => [\"{$json_region_ids}\"],\n";
                } else {
                    foreach($json_region_ids as $a){
                        $str .= "       \"RegionIds\" => [ \n";
                        $str .= "           \"{$a}\",\n";
                        $str .= "       ],\n";
                    }
                }

                if (!array_key_exists("Product", $json_endpoint["Products"])) {
                    $str .= "       \"Products\" => []\n";
                } else {
                    $json_products = $json_endpoint["Products"]["Product"];

                    if (array() === $json_products or !is_array($json_products)) {
                        $str .= "       \"Products\" => []\n";
                    } elseif (array_keys($json_products) !== range(0, count($json_products) - 1)) {
                        # array is not sequential
                        $str .= "       \"Products\" => [\n";
                        $str .= "           [\n";
                        $str .= "               \"ProductName\"=>\"".$json_products['ProductName']."\",\n";
                        $str .= "               \"DomainName\"=>\"".$json_products['DomainName']."\"\n";
                        $str .= "           ]\n";
                        $str .= "       ]\n";
                    } else {
                        $str .= "       \"Products\" => [\n";
                        foreach($json_products as $b){
                            $str .= "           [\n";
                            $str .= "               \"ProductName\"=>\"".$b['ProductName']."\",\n";
                            $str .= "               \"DomainName\"=>\"".$b['DomainName']."\"\n";
                            $str .= "           ],\n";
                        }
                        $str .= "       ]\n";
                    }
                }
            }

            $str .= "   ],\n";
        }
        $str .= "];";

        file_put_contents($config_file,$str);

        return true;

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
