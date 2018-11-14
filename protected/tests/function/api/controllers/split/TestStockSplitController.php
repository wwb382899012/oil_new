<?php

use PHPUnit\Framework\TestCase;


class TestStockSplitController extends TestCase{

    /**
     *  设置基境(fixture)
     */
    protected function setUp(){

    }

    public function curl($url,$data=[],$json = true){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            if($json && is_array($data)){
                $data = \CJSON::encode($data);
            }
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            if($json){ //发送JSON数据
                curl_setopt($curl, CURLOPT_HEADER, 0);
                curl_setopt($curl, CURLOPT_HTTPHEADER,array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length:' . strlen($data)
                ));
            }
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        curl_close($curl);

        return \CJSON::decode($res);
    }

    public function testSave(){
        $url = 'http://oil2.jtjr.com/api/split/StockSplit/submit';
        $data = [
            'apply_id' => 1,
            'contract_id'=> 895,
            'bill_id' => 201803070006,
            'type' => 0,
            'remark'=>'989898989',
            'files'=> [
                [
                    'id'=>1,
                    'name'=>'ddddd',
                    'status'=>1,
                    'file_url'=> "/xxx/xx/test.pdf"
                ],
                [
                    'id'=>2,
                    'name'=>'22222',
                    'status'=>1,
                    'file_url'=> "/xxx/xx/test.pdf"
                ]
            ],
            'split_items'=>[
                [
                    'contract_id'=> 10000,
                    'goods_items'=>[
                        [
                            'goods_id' => '13',
                            'quantity' => '50.0000',
                            'unit' => '1',
                        ]
                    ]
                ],
                [
                    'contract_id'=> 10001,
                    'goods_items'=>[
                        [
                            'goods_id' => '13',
                            'quantity' => '50.0000',
                            'unit' => '1',
                        ]
                    ]
                ]
            ],
        ];

        $result = $this->curl($url,$data,true);

        $this->assertArrayHasKey('state',$result);

        $this->assertEquals(0,$result['state']);
    }

}