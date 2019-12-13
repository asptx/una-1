<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Credits Credits
 * @ingroup     UnaModules
 *
 * @{
 */

/*
 * Module database queries
 */
class BxCreditsDb extends BxBaseModGeneralDb
{
    function __construct(&$oConfig)
    {
        parent::__construct($oConfig);
    }

    /**
     * Bundle related methods.
     */
    public function getBundle($aParams = array())
    {
    	$CNF = &$this->_oConfig->CNF;
    	$aMethod = array('name' => 'getAll', 'params' => array(0 => 'query'));

    	$sSelectClause = "`tb`.*";
    	$sJoinClause = $sWhereClause = $sLimitClause = "";
        $sOrderClause = "`tb`.`order` ASC";
        switch($aParams['type']) {
            case 'id':
            	$aMethod['name'] = 'getRow';
            	$aMethod['params'][1] = array(
                    'id' => $aParams['id']
                );

                $sWhereClause = " AND `tb`.`id`=:id";
                break;

            case 'order_max':
            	$aMethod['name'] = 'getOne';

                $sSelectClause = "IFNULL(MAX(`tb`.`order`), 0)";
                break;

            case 'all':
                break;
        }

        if(isset($aParams['active']))
            $sWhereClause .= $this->prepareAsString(" AND `tb`.`active`=?", (bool)$aParams['active'] ? 1 : 0);

        $sOrderClause = !empty($sOrderClause) ? "ORDER BY " . $sOrderClause : "";
        $sLimitClause = !empty($sLimitClause) ? "LIMIT " . $sLimitClause : "";

        $aMethod['params'][0] = "SELECT
                " . $sSelectClause . "
            FROM `" . $CNF['TABLE_BUNDLES'] . "` AS `tb`" . $sJoinClause . "
            WHERE 1" . $sWhereClause . " " . $sOrderClause . " " . $sLimitClause;

        return call_user_func_array(array($this, $aMethod['name']), $aMethod['params']);
    }

    public function isBundle($iId)
    {
        $aSport = $this->getBundle(array('type' => 'id', 'id' => $iId));
        return !empty($aSport) && is_array($aSport);
    }

    public function insertBundle($aSet)
    {
        $sQuery = "INSERT INTO `" . $this->_oConfig->CNF['TABLE_BUNDLES'] . "` SET " . $this->arrayToSQL($aSet);
        return (int)$this->query($sQuery) > 0;
    }

    public function updateBundle($aSet, $aWhere)
    {
        $sQuery = "UPDATE `" . $this->_oConfig->CNF['TABLE_BUNDLES'] . "` SET " . $this->arrayToSQL($aSet) . " WHERE " . (!empty($aWhere) ? $this->arrayToSQL($aWhere, ' AND ') : "1");
        return (int)$this->query($sQuery) > 0;
    }

    public function deleteBundle($aWhere)
    {
    	if(empty($aWhere))
            return false;

        $sQuery = "DELETE FROM `" . $this->_oConfig->CNF['TABLE_BUNDLES'] . "` WHERE " . $this->arrayToSQL($aWhere, ' AND ');
        return (int)$this->query($sQuery) > 0;
    }

    /**
     * Order related methods.
     */
    public function getOrder($aParams = array())
    {
    	$CNF = &$this->_oConfig->CNF;
    	$aMethod = array('name' => 'getAll', 'params' => array(0 => 'query'));

    	$sSelectClause = "`to`.*";
    	$sJoinClause = $sWhereClause = $sOrderClause = $sLimitClause = "";
        switch($aParams['type']) {
            case 'id':
            	$aMethod['name'] = 'getRow';
            	$aMethod['params'][1] = array(
                    'id' => $aParams['id']
                );

                $sWhereClause = " AND `to`.`id`=:id";
                break;

            case 'row_by':
                $aMethod['name'] = 'getRow';
                $sWhereClause = " AND " . $this->arrayToSQL($aParams['by'], ' AND ');
                break;

            case 'all':
                break;
        }

        $sOrderClause = !empty($sOrderClause) ? "ORDER BY " . $sOrderClause : "";
        $sLimitClause = !empty($sLimitClause) ? "LIMIT " . $sLimitClause : "";

        $aMethod['params'][0] = "SELECT
                " . $sSelectClause . "
            FROM `" . $CNF['TABLE_ORDERS'] . "` AS `tb`" . $sJoinClause . "
            WHERE 1" . $sWhereClause . " " . $sOrderClause . " " . $sLimitClause;

        return call_user_func_array(array($this, $aMethod['name']), $aMethod['params']);
    }

    public function isOrder($iId)
    {
        $aOrder = $this->getOrder(array('type' => 'id', 'id' => $iId));
        return !empty($aOrder) && is_array($aOrder);
    }

    public function isOrderByPbo($iProfileId, $iBundleId, $sOrder)
    {
        $aOrder = $this->getOrder(array('type' => 'row_by', 'by' => array('profile_id' => $iProfileId, 'bundle_id' => $iBundleId, 'order' => $sOrder)));
        return !empty($aOrder) && is_array($aOrder);
    }

    public function insertOrder($aSet, $sSetAddon = '')
    {
        $sQuery = "INSERT INTO `" . $this->_oConfig->CNF['TABLE_ORDERS'] . "` SET " . $this->arrayToSQL($aSet) . $sSetAddon;
        return (int)$this->query($sQuery) > 0;
    }

    public function insertOrderDeleted($aSet, $sSetAddon = '')
    {
        $sQuery = "INSERT INTO `" . $this->_oConfig->CNF['TABLE_ORDERS_DELETED'] . "` SET " . $this->arrayToSQL($aSet) . $sSetAddon;
        return (int)$this->query($sQuery) > 0;
    }

    public function registerOrder($iProfileId, $iBundleId, $iCount, $sOrder, $sLicense, $sType, $sDuration = '', $iTrial = 0)
    {
        $CNF = &$this->_oConfig->CNF;

        $sSetAddon = ', `added`=UNIX_TIMESTAMP()';

        return $this->insertOrder(array(
            'profile_id' => $iProfileId,
            'bundle_id' => $iBundleId,
            'count' => $iCount,
            'order' => $sOrder,
            'license' => $sLicense,
            'type' => $sType
        ), $sSetAddon);
    }

    public function updateOrder($aSet, $aWhere)
    {
        $sQuery = "UPDATE `" . $this->_oConfig->CNF['TABLE_ORDERS'] . "` SET " . $this->arrayToSQL($aSet) . " WHERE " . (!empty($aWhere) ? $this->arrayToSQL($aWhere, ' AND ') : "1");
        return (int)$this->query($sQuery) > 0;
    }

    /**
     * Note. Prolong isn't allowed for now.
     */
    public function prolongOrder($iProfileId, $iItemId, $iItemCount, $sOrder, $sLicense, $sType, $sDuration, $iTrial)
    {
        return false;
    }

    public function deleteOrder($aWhere)
    {
    	if(empty($aWhere))
            return false;

        $sQuery = "DELETE FROM `" . $this->_oConfig->CNF['TABLE_ORDERS'] . "` WHERE " . $this->arrayToSQL($aWhere, ' AND ');
        return (int)$this->query($sQuery) > 0;
    }

    public function unregisterOrder($iProfileId, $iItemId, $sOrder, $sLicense, $sType)
    {
        $CNF = &$this->_oConfig->CNF;

        $aOrder = $this->getOrder(array('type' => 'row_by', 'by' => array(
            'profile_id' => $iProfileId,
            'bundle_id' => $iItemId,
            'order' => $sOrder,
            'license' => $sLicense
    	)));

        if(empty($aOrder) || !is_array($aOrder))
            return true;

        //--- Move to deleted orders table with 'refund' as reason.
        $this->insertOrderDeleted($aOrder, ", `reason`='refund', `deleted`=UNIX_TIMESTAMP()");

        return $this->deleteOrder(array('id' => $aOrder[$CNF['FIELD_ID']]));
    }

    /**
     * Profile related methods.
     */
    public function getProfile($aParams = array())
    {
    	$CNF = &$this->_oConfig->CNF;
    	$aMethod = array('name' => 'getAll', 'params' => array(0 => 'query'));

    	$sSelectClause = "`tp`.*";
    	$sJoinClause = $sWhereClause = $sLimitClause = "";
        switch($aParams['type']) {
            case 'id':
            	$aMethod['name'] = 'getRow';
            	$aMethod['params'][1] = array(
                    'id' => $aParams['id']
                );

                $sWhereClause = " AND `tp`.`id`=:id";
                break;
            
            case 'balance':
            	$aMethod['name'] = 'getOne';
            	$aMethod['params'][1] = array(
                    'id' => $aParams['id']
                );

                $sSelectClause = "`tp`.`balance`";
                $sWhereClause = " AND `tp`.`id`=:id";
                break;
        }

        $sLimitClause = !empty($sLimitClause) ? "LIMIT " . $sLimitClause : $sLimitClause;

        $aMethod['params'][0] = "SELECT
                " . $sSelectClause . "
            FROM `" . $CNF['TABLE_PROFILES'] . "` AS `tp`" . $sJoinClause . "
            WHERE 1" . $sWhereClause . " " . $sLimitClause;

        return call_user_func_array(array($this, $aMethod['name']), $aMethod['params']);
    }

    public function isProfile($iId)
    {
        $aProfile = $this->getProfile(array('type' => 'id', 'id' => $iId));
        return !empty($aProfile) && is_array($aProfile);
    }

    public function insertProfile($aSet)
    {
        $sQuery = "INSERT INTO `" . $this->_oConfig->CNF['TABLE_PROFILES'] . "` SET " . $this->arrayToSQL($aSet);
        return (int)$this->query($sQuery) > 0;
    }

    public function updateProfile($aSet, $aWhere)
    {
        $sQuery = "UPDATE `" . $this->_oConfig->CNF['TABLE_PROFILES'] . "` SET " . $this->arrayToSQL($aSet) . " WHERE " . (!empty($aWhere) ? $this->arrayToSQL($aWhere, ' AND ') : "1");
        return (int)$this->query($sQuery) > 0;
    }

    public function updateProfileBalance($iId, $fBalance)
    {
        if(!$this->isProfile($iId))
            return $this->insertProfile(array('id' => $iId, 'balance' => $fBalance));

        $sQuery = "UPDATE `" . $this->_oConfig->CNF['TABLE_PROFILES'] . "` SET `balance`=`balance`+:balance WHERE `id`=:id";
        return (int)$this->query($sQuery, array(
            'id' => $iId,
            'balance' => $fBalance
        )) > 0;
    }

    public function deleteProfile($aWhere)
    {
    	if(empty($aWhere))
            return false;

        $sQuery = "DELETE FROM `" . $this->_oConfig->CNF['TABLE_PROFILES'] . "` WHERE " . $this->arrayToSQL($aWhere, ' AND ');
        return (int)$this->query($sQuery) > 0;
    }

    /**
     * History related methods.
     */
    public function getHistory($aParams = array())
    {
    	$CNF = &$this->_oConfig->CNF;
    	$aMethod = array('name' => 'getAll', 'params' => array(0 => 'query'));

    	$sSelectClause = "`th`.*";
    	$sJoinClause = $sWhereClause = $sLimitClause = "";
        switch($aParams['type']) {
            case 'id':
            	$aMethod['name'] = 'getRow';
            	$aMethod['params'][1] = array(
                    'id' => $aParams['id']
                );

                $sWhereClause = " AND `th`.`id`=:id";
                break;

            case 'row_by':
                $aMethod['name'] = 'getRow';
                $sWhereClause = " AND " . $this->arrayToSQL($aParams['by'], ' AND ');
                break;
        }

        $sLimitClause = !empty($sLimitClause) ? "LIMIT " . $sLimitClause : $sLimitClause;

        $aMethod['params'][0] = "SELECT
                " . $sSelectClause . "
            FROM `" . $CNF['TABLE_HISTORY'] . "` AS `th`" . $sJoinClause . "
            WHERE 1" . $sWhereClause . " " . $sLimitClause;

        return call_user_func_array(array($this, $aMethod['name']), $aMethod['params']);
    }

    public function isHistory($iId)
    {
        $aHistory = $this->getHistory(array('type' => 'id', 'id' => $iId));
        return !empty($aHistory) && is_array($aHistory);
    }

    public function insertHistory($aSet)
    {
        $sQuery = "INSERT INTO `" . $this->_oConfig->CNF['TABLE_HISTORY'] . "` SET " . $this->arrayToSQL($aSet);
        return (int)$this->query($sQuery) > 0;
    }

    public function updateHistory($aSet, $aWhere)
    {
        $sQuery = "UPDATE `" . $this->_oConfig->CNF['TABLE_HISTORY'] . "` SET " . $this->arrayToSQL($aSet) . " WHERE " . (!empty($aWhere) ? $this->arrayToSQL($aWhere, ' AND ') : "1");
        return (int)$this->query($sQuery) > 0;
    }

    public function deleteHistory($aWhere)
    {
    	if(empty($aWhere))
            return false;

        $sQuery = "DELETE FROM `" . $this->_oConfig->CNF['TABLE_HISTORY'] . "` WHERE " . $this->arrayToSQL($aWhere, ' AND ');
        return (int)$this->query($sQuery) > 0;
    }
}

/** @} */
