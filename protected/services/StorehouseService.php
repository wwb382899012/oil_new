<?php 
class StorehouseService
{
    public static function editable($storehouse, $user) {
    	if($storehouse['create_user_id'] != $user['user_id']) {
    		return false;
    	}
    	if(!in_array($storehouse['status'], array(Storehouse::STATUS_NEW, Storehouse::STATUS_BACK))) {
    		return false;
    	}
    	return true;
	}

	/**
	 * @desc 根据仓库id获取仓库名
	 * @param string $store_ids
	 * @return array
	 */
    public static function getStoreNameByIds($store_ids) {
        $sql="select store_id,name from t_storehouse where store_id in (" . $store_ids .")";
        $data=Utility::query($sql);

        return $data;
    }

    /**
     * @desc 根据仓库id获取仓库名
     * @param int $store_id
     * @return array
     */
    public static function getStoreName($store_id) {
        $data = StorehouseService::getStoreNameByIds($store_id);
        if(Utility::isNotEmpty($data)){
            return $data[0]['name'];
        }
        return '';
    }
}
?>