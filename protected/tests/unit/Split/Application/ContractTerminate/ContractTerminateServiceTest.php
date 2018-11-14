<?php
/**
 * User: liyu
 * Date: 2018/6/15
 * Time: 11:37
 * Desc: ContractTerminateServiceTest.php
 */

use ddd\Split\Application\ContractTerminateService;
use ddd\Split\Dto\AttachmentDTO;
use ddd\Split\Dto\ContractTerminate\ContractTerminateDTO;
use PHPUnit\Framework\TestCase;
use ddd\domain\entity\Attachment;

class ContractTerminateServiceTest extends TestCase
{

    use \ddd\Split\Domain\Model\Contract\ContractTerminateRepository;

    public static $entity;
    public $contract;
    public static $contractId;
    public $service;
    public static $contractTerminateId;

    public function setUp() {
        self::$contractId = 1091;
        $this->contract = \ddd\Split\Repository\Contract\ContractRepository::repository()->findByPk(self::$contractId);
        $this->service = new ContractTerminateService();
    }

    public function testSave() {
        if (!self::$entity) {
            $dto = new ContractTerminateDTO();
            $dto->contract_id = self::$contractId;
            $dto->reason = 'test add save terminate';

            $file1 = new AttachmentDTO();
            $file1->name = '测试文件1';
            $file1->file_url = 'static/tmp/test_file_1.pdf';

            $file2 = new AttachmentDTO();
            $file2->name = '测试文件2';
            $file2->file_url = 'static/tmp/test_file_2.pdf';

            $files = [$file1, $file2];
            $dto->files = $files;
            try {
                $res = $this->service->save($dto, $this->contract);
                $this->assertTrue($res);
                self::$entity = $this->getContractTerminateRepository()->findByContractId(self::$contractId);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        return self::$entity;

    }

    /**
     * @depends testSave
     */
    public function testIsCanSubmit($entity) {
        $res = $this->service->isCanSubmit($entity);
        $this->assertTrue($res);
    }

    /**
     * @depends testSave
     */
    public function testIsCanEdit($entity) {
        $res = $this->service->isCanEdit($entity);
        $this->assertTrue($res);
    }


    /**
     * @depends testSave
     */
    public function testSubmit($entity) {
        $persistent = true;
        $res = $this->service->submit($entity, $persistent);
        $this->assertTrue($res);
        return $entity;
    }

    /**
     * @depends testSave
     */
    public function testGetContractTerminate($entity) {
        $res = $this->service->getContractTerminate($entity->contract_id);
        $this->assertNotEmpty($res);
    }


    /**
     * @depends testSubmit
     */
    public function testCheckPass($entity) {
        $persistent = true;
        $res = $this->service->checkPass($entity, $persistent);
        $this->assertTrue($res);
    }

    /**
     * @depends testSubmit
     */
    public function testCheckBack($entity) {
        $persistent = true;
        $res = $this->service->checkBack($entity, $persistent);
        $this->assertTrue($res);
    }


    /**
     * @afterClass
     */
    public static function tearDownSomeOtherSharedFixtures() {
        \ContractTerminate::model()->deleteByPk(self::$entity->getId());
    }
}
