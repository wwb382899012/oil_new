<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/10 15:27
 * Describe：
 */

namespace ddd\domain\iRepository\contract;


use ddd\domain\entity\contract\Contract;
use ddd\Common\Domain\IRepository;

interface IContractRepository  extends IRepository
{
    function submit(Contract $contract);

    function back(Contract $contract);

    function setSettledBack(Contract $contract);

    function setOnSettling(Contract $contract);

    function setSettled(Contract $contract);

    function setDone(Contract $contract);

    function setFileUploaded(Contract $contract);
    function setSignedFileUploaded(Contract $contract);
    function setPaperUploaded(Contract $contract);
    function setSplit(Contract $contract);
    function setTerminating(Contract $contract);
    function setTerminateBack(Contract $contract);
    function setTerminated(Contract $contract);
}

