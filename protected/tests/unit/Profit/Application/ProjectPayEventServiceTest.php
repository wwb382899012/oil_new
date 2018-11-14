<?php
/**
 * User: liyu
 * Date: 2018/8/23
 * Time: 19:16
 * Desc: ProjectPayEventServiceTest.php
 */

use ddd\Profit\Application\ProjectPayEventService;
use PHPUnit\Framework\TestCase;

class ProjectPayEventServiceTest extends TestCase
{

    public $service;
    public $project_id;
    public $payment_id;

    public function setUp() {
        $this->service = new ProjectPayEventService();
        $this->project_id = 20180716003;
        $this->payment_id = 2018081700001;
    }

    public function testOnPayConfirm() {
        try {
            $res = $this->service->onPayConfirm($this->project_id, $this->payment_id);
            $this->assertTrue($res);
        } catch (Exception $e) {
            echo $e;
        }
    }
}
